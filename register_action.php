<?php
session_start();

// Initialize CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once 'includes/db_connect.php';
require 'vendor/autoload.php';
require_once __DIR__ . '/includes/env_loader.php';

$envLoader = new EnvLoader();
$envLoader->load();

use AfricasTalking\SDK\AfricasTalking;

$username = $_ENV['AT_USERNAME'];
$apiKey = $_ENV['AT_API_KEY'];
$AT = new AfricasTalking($username, $apiKey);
$sms = $AT->sms();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['_token']) || $_POST['_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errors'] = ["Security token mismatch. Please try again."];
        $_SESSION['modal'] = "registerModal";
        header("Location: index.php");
        exit();
    }

    // Sanitize and validate inputs
    $phone = trim($_POST['phone']);
    $user_username = trim($_POST['user_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['password_confirmation']);
    $referral_code = isset($_POST['referral_code']) ? trim($_POST['referral_code']) : null;
    $accepted_terms = isset($_POST['terms']) ? 1 : 0;
    $accepted_responsibility = isset($_POST['responsibility']) ? 1 : 0;

    $_SESSION['old_inputs'] = [
        'phone' => $phone,
        'user_name' => $user_username,
        'email' => $email
    ];

    $errors = [];

    if (!preg_match('/^07\d{8}$/', $phone)) {
        $errors[] = "Invalid phone number format. Use 07XXXXXXXX.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!$accepted_terms || !$accepted_responsibility) {
        $errors[] = "You must accept the terms and confirm responsible play.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['modal'] = "registerModal";
        header("Location: index.php");
        exit();
    }

    try {
        // Check if phone exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->execute([$phone]);

        if ($stmt->fetch()) {
            $_SESSION['errors'] = ["Phone number is already registered."];
            $_SESSION['modal'] = "registerModal";
            header("Location: index.php");
            exit();
        }

        // Hash password
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        // Insert user into database
        $stmt = $conn->prepare("INSERT INTO users (username, phone, email, password_hash, referral_code, accepted_terms, accepted_responsibility) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $user_username,
            $phone,
            $email,
            $password_hash,
            $referral_code,
            $accepted_terms,
            $accepted_responsibility
        ]);

        // Commented out OTP sending
        // $registration_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        // $otpResult = send_otp($phone, $user_username, $registration_code);
        // $_SESSION['registration_otp'] = $registration_code;

        $_SESSION['success'] = "Registration successful. You can now log in.";
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        // $_SESSION['errors'] = ["Registration failed. Please try again."];
            $_SESSION['errors'] = ["Registration failed: " . $e->getMessage()];

        $_SESSION['modal'] = "registerModal";
        header("Location: index.php");
        exit();
    }
}


/**
 * Send OTP via SMS
 */
function send_otp($phone, $username, $otp) {
    global $sms;

    // Convert to international format
    if (substr($phone, 0, 1) === "0") {
        $phone = "+254" . substr($phone, 1);
    }

    $message = "Your verification code is: $otp";
    if (!empty($username)) {
        $message = "Dear $username, your verification code is: $otp";
    }

    try {
        $response = $sms->send([
            'to' => $phone,
            'message' => $message
        ]);

        if ($response['status'] === 'success') {
            return true;
        }
        return "SMS service error";
    } catch (Exception $e) {
        error_log("SMS Error: " . $e->getMessage());
        return $e->getMessage();
    }
}
?>