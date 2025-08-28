<?php
session_start();
require_once __DIR__ . '/MpesaHandler.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

try {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['errors'][] = "Security token mismatch. Please try again.";
        $_SESSION['modal'] = "depositModal";
        header("Location: index.php");
        exit;
    }

    // 1. Get form input
    $amount = isset($_POST['amount']) ? (int) $_POST['amount'] : 0;

    if ($amount < 10) {
        if (!isset($_SESSION['errors'])) {
            $_SESSION['errors'] = [];
        }
        $_SESSION['errors'][] = "Minimum deposit is KES 10.";

        $_SESSION['modal'] = "depositModal";
        header("Location: index.php");
        exit;
    }

    // 2. Check if user logged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['phone'])) {
        $_SESSION['errors'][] = "User not authenticated. Please log in.";
        $_SESSION['modal'] = "loginModal";
        header("Location: index.php");
        exit;
    }

    $userId = $_SESSION['user_id'];
    $phone  = $_SESSION['phone'];

    // 3. Initialize MpesaHandler
    $mpesa = new MpesaHandler();

    // 4. Initiate STK Push
    $result = $mpesa->initiateSTKPush($phone, $amount, $userId);

    // 5. Handle response
    if (isset($result['ResponseCode']) && $result['ResponseCode'] == "0") {
        $_SESSION['success'] = "STK Push sent successfully. Check your phone.";
    } else {
        $_SESSION['errors'][] = $result['errorMessage'] ?? "Failed to initiate STK push.";
        $_SESSION['modal'] = "depositModal";
    }

    header("Location: index.php");
    exit;

} catch (Exception $e) {
    error_log("Deposit Error: " . $e->getMessage());
    $_SESSION['errors'][] = "Something went wrong. Please try again.";
    $_SESSION['modal'] = "depositModal";
    header("Location: index.php");
    exit;
}
