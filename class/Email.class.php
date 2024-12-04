<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
   
   public function minify_html($html)
   {
      $search = array(
         '/(\n|^)(\x20+|\t)/',
         '/(\n|^)\/\/(.*?)(\n|$)/',
         '/\n/',
         '/\<\!--.*?-->/',
         '/(\x20+|\t)/', # Delete multispace (Without \n)
         '/\>\s+\</', # strip whitespaces between tags
         '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
         '/=\s+(\"|\')/'
      ); # strip whitespaces between = "'

      $replace = array(
         "\n",
         "\n",
         " ",
         "",
         " ",
         "><",
         "$1>",
         "=$1"
      );

      $html = preg_replace($search, $replace, $html);
      return $html;
   }


   public function nameFile($setFile)
   {
      $slug = strtolower(trim($setFile->name));
      $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
      $slug = preg_replace('/-+/', '-', $slug);
      $slug = trim($slug, '-');
      return $slug . '.' . $setFile->extension;
   }


   public function sendMail($data, $title, $content, $appname, $setFile = false)
   {

      $content = self::minify_html($content);

      $mail = new PHPMailer(true);

      try {
         //Server settings
         $mail->SMTPDebug = false;
         $mail->isSMTP();
         $mail->Host = SMTP_HOST;
         $mail->SMTPAuth = TRUE;
         $mail->Username = SMTP_USER;
         $mail->Password = SMTP_PASS;
         $mail->SMTPSecure = SMTP_SECURE;
         $mail->Port = SMTP_PORT;
         $mail->CharSet = "UTF-8";

         //Recipients
         $mail->setFrom(SMTP_USER, APP_NAME);
         $mail->addAddress($data->email, $data->nome);

         // //Attachments
         if ($setFile) {
            $mail->addAttachment($setFile->file, $this->nameFile($setFile));
         }

         //Content
         $mail->isHTML(true);
         $mail->Subject = $title;
         $mail->Body = $content;
         $mail->AltBody = strip_tags($content);

         if ($mail->send()) {
            return true;
         } else {
            return false;
         }

      } catch (Exception $e) {
         return false;
      }

   }

}