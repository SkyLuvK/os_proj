<?php

session_start();
require_once 'config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function generateOTP() {
    return rand(100000, 999999);
}

$error_message = ""; // Define a global variable to store error messages

function sendOTP($email, $otp) {
    global $error_message; // Use the global error message variable
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'kyteh01@gmail.com'; // Your Gmail address
        $mail->Password = 'hngtwmvwzcpovlyv'; // Your Gmail App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('kyteh01@gmail.com', 'Personal Files Access System');
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'This is your OTP Code';
        $mail->Body    = "OTP code is: $otp";
        $mail->AltBody = "OTP code is: $otp";

        $mail->send();
        return true;
    } catch (Exception $e) {
        $error_message = $e->getMessage(); // Capture the error message
        return false;
    }
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function getEmailById($id, $conn) {
    global $error_message;
    $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['email'];
    } else {
        $error_message = "No user found with the provided ID.";
        return false;
    }
}

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    $servername = "localhost";
    $username = "ky";
    $password = "1234";
    $dbname = "web_project_db";
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = getEmailById($user_id, $conn);

    if ($email === false) {
        echo "Failed to retrieve email. Error: " . htmlspecialchars($error_message);
        $conn->close();
        exit;
    }

    if (!isValidEmail($email)) {
        echo "Invalid email address: " . htmlspecialchars($email);
        $conn->close();
        exit;
    }

    $otp = generateOTP();
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_expiry'] = time() + 300; // OTP expires in 5 minutes

    if (sendOTP($email, $otp)) {
        echo "OTP sent successfully.";
    } else {
        global $error_message;
        echo "Failed to send OTP. Error: " . htmlspecialchars($error_message);
    }

    $conn->close();
} else {
    echo "No user ID provided in POST.";
}
?>