<?php
    require __DIR__.'/PHPMailer/PHPMailer.php';
    require __DIR__.'/PHPMailer/SMTP.php';
    require __DIR__.'/PHPMailer/Exception.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    function send_email($email, $subject, $body) {
        $mail = new PHPMailer(true);

        require './load_env.php';

        $mail -> isSMTP();
        $mail -> Host = 'smtp.gmail.com';
        $mail -> SMTPAuth = true;
        $mail -> Username = getenv('EMAIL_USERNAME');
        $mail -> Password = getenv('EMAIL_PASSWORD');
        $mail -> SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail -> Port = 587;

        $mail -> setFrom(getenv('EMAIL_USERNAME'), 'Geydrop');
        $mail -> addAddress($email);

        $mail -> isHTML(true);
        $mail -> Subject = $subject;
        $mail -> Body = $body;

        $mail ->send();
    }
?>