<?php

namespace App\Http\Controllers\Mqtt;

use PhpMqtt\Client\Contracts\Repository;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\PendingPublishConfirmationAlreadyExistsException;
use PhpMqtt\Client\Exceptions\UnexpectedAcknowledgementException;
use PhpMqtt\Client\MQTTClient;
use Psr\Log\LoggerInterface;

class InovaceMqttClient extends MQTTClient
{

    private $logger;
    private $repository;
    private $host;
    private $port;
    private $interrupted;
    private $currentMessageId;

    /**
     * Constructs a new MQTT client which subsequently supports publishing and subscribing.
     *
     * @param string               $host
     * @param int                  $port
     * @param string|null          $clientId
     * @param string|null          $caFile
     * @param Repository|null      $repository
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        string $host,
        int $port = 1883,
        string $clientId = null,
        string $caFile = null,
        Repository $repository = null,
        LoggerInterface $logger = null
    )
    {
        $this->interrupted = false;
        $this->host = $host;
        $this->port = $port;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->currentMessageId = null;
        parent::__construct(
            $host,
            $port,
            $clientId,
            $caFile,
            $repository,
            $logger
        );
    }

    /**
     * Runs an event loop that handles messages from the server and calls the registered
     * callbacks for published messages.
     *
     * If the second parameter is provided, the loop will exit as soon as all
     * queues are empty. This means there may be no open subscriptions,
     * no pending messages as well as acknowledgments and no pending unsubscribe requests.
     *
     * The third parameter will, if set, lead to a forceful exit after the specified
     * amount of seconds, but only if the second parameter is set to true. This basically
     * means that if we wait for all pending messages to be acknowledged, we only wait
     * a maximum of $queueWaitLimit seconds until we give up. We do not exit after the
     * given amount of time if there are open topic subscriptions though.
     *
     * @param bool     $allowSleep
     * @param bool     $exitWhenQueuesEmpty
     * @param int|null $queueWaitLimit
     * @return void
     * @throws DataTransferException
     * @throws UnexpectedAcknowledgementException
     */
    public function loop(bool $allowSleep = true, bool $exitWhenQueuesEmpty = false, int $queueWaitLimit = null): void
    {
        $this->logger->debug('Starting MQTT client loop.');

        $loopStartedAt            = microtime(true);
        $lastRepublishedAt        = microtime(true);
        $lastResendUnsubscribedAt = microtime(true);

        while (true) {
            if ($this->interrupted) {
                $this->interrupted = false;
                break;
            }

            $elapsedTime = microtime(true) - $loopStartedAt;

            foreach ($this->loopEventHandlers as $handler) {
                call_user_func($handler, $this, $elapsedTime);
            }

            $buffer = null;
            $byte   = $this->readFromSocket(1, true);

            if (strlen($byte) === 0) {
                if ($allowSleep) {
                    usleep(100000); // 100ms
                }
            } else {
                // Read the first byte of a message (command and flags).
                $command          = (int) (ord($byte) / 16);
                $qualityOfService = (ord($byte) & 0x06) >> 1;
                $retained         = (bool) (ord($byte) & 0x01);

                // Read the second byte of a message (remaining length)
                // If the continuation bit (8) is set on the length byte,
                // another byte will be read as length.
                $length     = 0;
                $multiplier = 1;
                do {
                    $digit       = ord($this->readFromSocket(1));
                    $length     += ($digit & 127) * $multiplier;
                    $multiplier *= 128;
                } while (($digit & 128) !== 0);

                // Read the remaining message according to the length information.
                if ($length) {
                    $buffer = $this->readFromSocket($length);
                }

                // Handle the received command according to the $command identifier.
                if ($command > 0 && $command < 15) {
                    switch ($command) {
                        case 2:
                            throw new UnexpectedAcknowledgementException(
                                self::EXCEPTION_ACK_CONNECT,
                                'We unexpectedly received a connection acknowledgement.'
                            );
                        case 3:
                            $this->handlePublishedMessage($buffer, $qualityOfService, $retained);
                            break;
                        case 4:
                            $this->handlePublishAcknowledgement($buffer);
                            break;
                        case 5:
                            $this->handlePublishReceipt($buffer);
                            break;
                        case 6:
                            $this->handlePublishRelease($buffer);
                            break;
                        case 7:
                            $this->handlePublishCompletion($buffer);
                            break;
                        case 9:
                            $this->handleSubscribeAcknowledgement($buffer);
                            break;
                        case 11:
                            $this->handleUnsubscribeAcknowledgement($buffer);
                            break;
                        case 12:
                            $this->handlePingRequest();
                            break;
                        case 13:
                            $this->handlePingAcknowledgement();
                            break;
                        default:
                            $this->logger->debug(sprintf('Received message with unsupported command [%s]. Skipping.', $command));
                            break;
                    }
                } else {
                    $this->logger->error('A reserved command has been received from an MQTT broker. Supported are commands (including) 1-14.', [
                        'broker' => sprintf('%s:%s', $this->host, $this->port),
                        'command' => $command,
                    ]);
                }
            }

            // Once a second we try to republish messages without confirmation.
            // This will only trigger the republishing though. If a message really
            // gets republished depends on the resend timeout and the last time
            // we sent the message.
            if (1 < (microtime(true) - $lastRepublishedAt)) {
                $this->republishPendingMessages();
                $lastRepublishedAt = microtime(true);
            }

            // Once a second we try to resend unconfirmed unsubscribe requests.
            // This will also only trigger the resending process. If an unsubscribe
            // request really gets resend depends on the resend timeout and the last
            // time we sent the unsubscribe request.
            if (1 < (microtime(true) - $lastResendUnsubscribedAt)) {
                $this->republishPendingUnsubscribeRequests();
                $lastResendUnsubscribedAt = microtime(true);
            }

            // If the last message of the broker has been received more seconds ago
            // than specified by the keep alive time, we will send a ping to ensure
            // the connection is kept alive.
            if ($this->nextPingAt() <= microtime(true)) {
                $this->ping();
            }

            // This check will ensure, that, if we want to exit as soon as all queues
            // are empty and they really are empty, we quit.
            if ($exitWhenQueuesEmpty) {
                if ($this->allQueuesAreEmpty() && $this->repository->countTopicSubscriptions() === 0) {
                    break;
                }

                // We also exit the loop if there are no open topic subscriptions
                // and we reached the time limit.
                if ($queueWaitLimit !== null &&
                    (microtime(true) - $loopStartedAt) > $queueWaitLimit &&
                    $this->repository->countTopicSubscriptions() === 0) {
                    break;
                }
            }
        }
    }

    /**
     * Handles a received message. The buffer contains the whole message except
     * command and length. The message structure is:
     *
     *   [topic-length:topic:message]+
     *
     * @param string $buffer
     * @param int    $qualityOfServiceLevel
     * @param bool   $retained
     * @return void
     * @throws DataTransferException
     */
    protected function handlePublishedMessage(string $buffer, int $qualityOfServiceLevel, bool $retained = false): void
    {
        $topicLength = (ord($buffer[0]) << 8) + ord($buffer[1]);
        $topic       = substr($buffer, 2, $topicLength);
        $message     = substr($buffer, ($topicLength + 2));

        if ($qualityOfServiceLevel > 0) {
            if (strlen($message) < 2) {
                $this->logger->error(sprintf(
                    'Received a published message with QoS level [%s] from an MQTT broker, but without a message identifier.',
                    $qualityOfServiceLevel
                ));

                // This message seems to be incomplete or damaged. We ignore it and wait for a retransmission,
                // which will occur at some point due to QoS level > 0.
                return;
            }

            $messageId = $this->stringToNumber($this->pop($message, 2));
            $this->currentMessageId = $messageId;

            if ($qualityOfServiceLevel === 2) {
                try {
                    $this->sendPublishReceived($messageId);
                    $this->repository->addNewPendingPublishConfirmation($messageId, $topic, $message);
                } catch (PendingPublishConfirmationAlreadyExistsException $e) {
                    // We already received and processed this message, therefore we do not respond
                    // with a receipt a second time and wait for the release instead.
                }
                // We only deliver this published message as soon as we receive a publish complete.
                return;
            }
        }

        $this->deliverPublishedMessage($topic, $message, $qualityOfServiceLevel, $retained);
    }

    /**
     * Sends a publish acknowledgement for the given message identifier.
     *
     * @param int $messageId
     * @return void
     * @throws DataTransferException
     */
    public function sendPublishAcknowledgementAfterProcessing(): void
    {
        $messageId = $this->currentMessageId;
        $this->logger->debug('Sending publish acknowledgement to an MQTT broker.', [
            'broker' => sprintf('%s:%s', $this->host, $this->port),
            'message_id' => $messageId,
        ]);

        $this->writeToSocket(chr(0x40) . chr(0x02) . $this->encodeMessageId($messageId));
    }
}
