<?php 
require 'config.php';

$db = new SQLite3('users.db');

$results = $db->query('SELECT * FROM users');


// Don't forget to desactivate access for less secure applications
date_default_timezone_set('Etc/UTC');

require 'phpMailer/PHPMailerAutoload.php';

while ($row = $results->fetchArray()) {
    
    usleep(1000000);
    
    if($row['valid']){
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 587;
        $mail->SMTPSecure = 'tls';
        $mail->SMTPAuth = true;
        $mail->Username = $email;
        $mail->Password = $pwd;

        $mail->setFrom($email, 'Manuel Coffin');
        $mail->addReplyTo($admin, 'Administrateur');

        $mail->addAddress($row['email']);
        $mail->Subject = 'PHPMailer GMail SMTP test';
        $mail->msgHTML('Bonjour '.$row['firstname'].' '.$row['name'].'. Voici un mail personnalisé. Cordialement. Pour se désabonner, cliquez <a href="http://localhost?email="'.$row['email'].'>ici</a> cher ami.');
        $mail->AltBody = 'This is a plain-text message body';
    }

    if (!$mail->send()) {
        if($row['error']<10){
            // increment error field
            $db->query('UPDATE users SET error='.$row['error']+1);
            echo "Mailer Error: " . $mail->ErrorInfo;
        }else {
            // if error field = 10, set valid to false
            $db->query('UPDATE users SET valid=false');
        }
    } else {
        echo "Message sent!";
    }
}