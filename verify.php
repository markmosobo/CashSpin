<?php
session_start();
require_once 'includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF
    if (!isset($_POST['_token']) || $_POST['_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['errors'] = ["Security token mismatch. Please try again."];
        $_SESSION['modal'] = "regVerifyModal";
        header("Location: index.php");
        exit();
    }

    $entered_code = trim($_POST['code'] ?? '');
    $errors = [];

    if (!preg_match('/^\d{6}$/', $entered_code)) {
        $errors[] = "Invalid verification code format.";
    }

    if (!isset($_SESSION['registration_otp'], $_SESSION['registration_data'])) {
        $errors[] = "Session expired. Please register again.";
    }

    if ($errors) {
        $_SESSION['errors'] = $errors;
        $_SESSION['modal'] = "regVerifyModal";
        header("Location: index.php");
        exit();
    }

    if ($entered_code !== $_SESSION['registration_otp']) {
        $_SESSION['errors'] = ["Invalid verification code."];
        $_SESSION['modal'] = "regVerifyModal";
        header("Location: index.php");
        exit();
    }

    // OTP verified
    try {
        $data = $_SESSION['registration_data'];

        $stmt = $conn->prepare("INSERT INTO users 
            (phone, username, email, password_hash, referral_code, accepted_terms, accepted_responsibility, is_verified, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())");

        $stmt->execute([
            $data['phone'],
            $data['username'],
            $data['email'],
            $data['password_hash'],
            $data['referral_code'],
            $data['terms'],
            $data['responsibility']
        ]);

        // Clear sensitive session data
        unset($_SESSION['registration_data'], $_SESSION['registration_otp'], $_SESSION['old_inputs']);

        $_SESSION['success'] = "Registration successful! Please login.";
        $_SESSION['modal'] = "loginModal";

        // Rotate CSRF token
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header("Location: index.php");
        exit();

    } catch (PDOException $e) {
        error_log("Verification error: " . $e->getMessage());
        $_SESSION['errors'] = ["Verification failed. Please try again."];
        $_SESSION['modal'] = "regVerifyModal";
        header("Location: index.php");
        exit();
    }
}
?>
