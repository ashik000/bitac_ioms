<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController extends Controller
{
    public function generateAlarmMail($mailBody, $toEmails)
    {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Mailer     = "smtp";
        $mail->SMTPDebug  = 1;
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "cncshop.bitacdhaka@gmail.com";
        $mail->Password   = "k%uGR@8xpRZkjqA3";
        $mail->isHTML(true);

        for ($i=0; $i<count($toEmails['emails']); $i++)
        {
            $mail->addAddress($toEmails['emails'][$i], $toEmails['names'][$i]);
        }

        $mail->setFrom("cncshop.bitacdhaka@gmail.com", "BITAC CNC Shop");
        //$mail->AddReplyTo("john@gmail.com", "John Doe");
        //$mail->AddCC("cc-recipient-email@domain", "cc-recipient-name");
        $mail->Subject = "Alarm | IOMS";
        $machineName = $mailBody['machine_name'];
        $alarmInfo = $mailBody['alarm_info'];
        $content = "<b>Machine: </b>".$machineName."<br><b>Alarm: </b>".$alarmInfo;
        $mail->msgHTML($content);
        $this->sendMail($mail);
    }

    public function sendMail($mail)
    {
        try{
            if($mail->send()) {
                Log::debug("Email sent successfully");
            } else {
                Log::debug("Error while sending Email");
            }
        }
        catch(Exception $e){
            Log::debug($e->getMessage());
        }
    }
}
