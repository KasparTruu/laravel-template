<?php
$to = 'your-email@example.com';
$subject = 'Weekly Email';
$message = 'This is your weekly email!';
$headers = 'From: no-reply@example.com' . "\r\n" .
           'Reply-To: no-reply@example.com' . "\r\n" .
           'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)) {
    echo 'Email sent successfully';
} else {
    echo 'Failed to send email';
}
?>