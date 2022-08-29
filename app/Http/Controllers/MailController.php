<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController extends Controller
{
    public function GenerateAlarmMail($mailBody)
    {
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Mailer = "smtp";
        $mail->SMTPDebug  = 1;
        $mail->SMTPAuth   = TRUE;
        $mail->SMTPSecure = "tls";
        $mail->Port       = 587;
        $mail->Host       = "smtp.gmail.com";
        $mail->Username   = "cncshop.bitacdhaka@gmail.com";
        $mail->Password   = "k%uGR@8xpRZkjqA3";
        $mail->IsHTML(true);
        $mail->AddAddress("ashik.inovace@gmail.com", "Ashik");
        $mail->SetFrom("cncshop.bitacdhaka@gmail.com", "BITAC CNC Shop");
        //$mail->AddReplyTo("ashik.inovace@gmail.com", "Ashik");
        //$mail->AddCC("cc-recipient-email@domain", "cc-recipient-name");
        $mail->Subject = "Alarm | IOMS";
        //$content = "Test Body";
        $machineName = $mailBody['machine_name'];
        $alarmInfo = $mailBody['alarm_info'];
        $content = "<b>Machine: </b>".$machineName."<br><b>Alarm: </b>".$alarmInfo;
        $mail->MsgHTML($content);
        $this->SendMail($mail);
    }

//    public function LogAlarmMail(Request $mail){
//
//    }

    public function SendMail($mail){
        try{
            if($mail->Send()) {
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
