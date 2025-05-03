<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load Composer's autoloader

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // CAPTCHA verification
    $captcha = $_POST['g-recaptcha-response'];
    $secretKey = "6Lfw5ywrAAAAAADlJ14gZhp1ubCZREzRIj5x4zjG";

    $verifyResponse = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}"
    );
    $responseData = json_decode($verifyResponse);

    if (!$responseData->success) {
        echo "CAPTCHA verification failed. Please try again.";
        exit;
    }

    // Get inputs
    $name         = htmlspecialchars($_POST['name']);
    $mobile       = htmlspecialchars($_POST['mobile']);
    $email        = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $country      = htmlspecialchars($_POST['country']);
    $looking_for  = htmlspecialchars($_POST['looking_for']);
    $experience   = htmlspecialchars($_POST['experience']);
    $participants = intval($_POST['participants']);
    $note         = htmlspecialchars($_POST['note']);

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp-relay.brevo.com'; // Brevo SMTP server
        $mail->SMTPAuth = true;
        $mail->Port = 587;
        $mail->Username = '896607001@smtp-brevo.com'; 
        $mail->Password = 'QDNRjgJ6s8vUpPqK';

        // Email sender and recipients
        $mail->setFrom('info@divingmirissa.com', 'Diving Mirissa');
        $mail->addAddress('tharinduranaweera523@gmail.com');
        $mail->addAddress('info@divingmirissa.com');
        $mail->addReplyTo($email, $name); // User's email for reply

        // Email subject
        $mail->Subject = "New Contact Form Submission $name";

        // Load email template
        $emailBody = file_get_contents('email_template.html');

        // Replace placeholders with actual form data
        $emailBody = str_replace('{{name}}', $name, $emailBody);
        $emailBody = str_replace('{{email}}', $email, $emailBody);
        $emailBody = str_replace('{{phone}}', $mobile, $emailBody);
        $emailBody = str_replace('{{country}}', $country, $emailBody);
        $emailBody = str_replace('{{looking_for}}', $looking_for, $emailBody);
        $emailBody = str_replace('{{participants}}', $participants, $emailBody);
        $emailBody = str_replace('{{experience}}', $experience, $emailBody);
        $emailBody = str_replace('{{note}}', nl2br($note), $emailBody);

        // Email content
        $mail->isHTML(isHtml: true);
        $mail->Body = $emailBody;

        // Send email
        if ($mail->send()) {
            http_response_code(200);
        } else {
            http_response_code(500); 
        }
            
    } catch (Exception $e) {
        http_response_code(500); 
    }
} else {
    http_response_code(500); 
}

?>
