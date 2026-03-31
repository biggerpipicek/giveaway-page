<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🔹 1. IMPORTY (ÚPLNĚ NAHOŘE)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

print_r(get_declared_classes());
exit();
require 'config.php';

// 🔹 2. DB PŘIPOJENÍ
$conn = new mysqli("127.0.0.1", "root", "", "giveaway-page");

// 🔹 3. DATA Z FORMU
$name = htmlspecialchars($_POST['name']);
$type = $_POST['contact_type'];
$email = htmlspecialchars($_POST['contact_value']);
// 🔹 4. TOKEN
$token = bin2hex(random_bytes(32));

// 🔹 5. LOGIKA VERIFIED (TO JE TEN "BOD 4")
if ($type === "email") {
    $verified = 0;
}

// 🔹 6. ULOŽENÍ DO DB
$stmt = $conn->prepare("
    INSERT INTO participants (name, contact_type, contact_value, token, verified) 
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssi", $name, $type, $email, $token, $verified);
$stmt->execute();

// 🔹 7. VERIFY LINK
$link = "http://localhost/giveaway-page/verify.php?token=$token";

// 🔹 8. PHPMailer (TO JE TEN "BOD 3")
if ($type === "email") {

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.seznam.cz';
        $mail->SMTPAuth = true;
        $mail->Username = $email_user;
        $mail->Password = $email_pass;
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('michael.phillips@seznam.cz', 'Giveaway Bot');
        $mail->addReplyTo('michael.phillips@seznam.cz');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8'; // 👈 TADY
        $mail->Subject = 'Ověření soutěže';
        $mail->Body = "
            <h2>Díky za zapojení do Soutěže!</h2>
            <h2>Pro kompletní zapojení do soutěže potřebuju, aby ses ověřil kliknutím na odkaz níže.</h2>
            <p>Klikni níže:</p>
            <a href='$link'>Ověřit!</a>
            <br>
            <br>
            <p>Díky,<br>pip-idk</p>
        ";
        
        #$mail->SMTPDebug = 2;
        $mail->send();
        
        
    } catch (Exception $e) {
        echo "Mailer error: {$mail->ErrorInfo}";
    }
}

// 🔹 9. REDIRECT
//header("refresh:1; url=success.html");
//exit();