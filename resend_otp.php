<?php
session_start();
require_once 'includes/db_connect.php';
require 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use AfricasTalking\SDK\AfricasTalking;

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Retrieve and validate phone number from POST data
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
if (empty($phone) || !preg_match('/^07\d{8}$/', $phone)) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
    exit();
}

// Store phone number in session for use on the verification page
$_SESSION['phone'] = $phone;

try {
    // Check if user exists and is not verified
    $stmt = $conn->prepare("SELECT id, registration_code, is_verified, UNIX_TIMESTAMP(code_expires_at) as code_expires 
                            FROM users WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'User not found']);
        exit();
    }
    
    if ($user['is_verified']) {
        echo json_encode(['success' => false, 'message' => 'Account already verified']);
        exit();
    }

    // Generate a new OTP code (6-digit)
    $otp = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
    
    // Set OTP expiration time to 1 hour from now
    $expires_at = date('Y-m-d H:i:s', time() + 3600);

    // Update the OTP and its expiration in the database
    $updateStmt = $conn->prepare("UPDATE users SET registration_code = ?, code_expires_at = ? WHERE id = ?");
    $updateStmt->execute([$otp, $expires_at, $user['id']]);

    // Prepare Africa's Talking credentials and initialize the SMS service
    $username = $_ENV['AT_USERNAME']; 
    $apiKey = $_ENV['AT_API_KEY'];
    $senderId = isset($_ENV['AT_SENDER_ID']) ? $_ENV['AT_SENDER_ID'] : null; 

    $AT = new AfricasTalking($username, $apiKey);
    $sms = $AT->sms();

    // Convert phone number to international format (e.g. +2547XXXXXXXX)
    $internationalPhone = preg_replace('/^0/', '+254', $phone);
    $message = "Your verification code is: $otp";

    try {
        $smsData = [
            'to' => $internationalPhone,
            'message' => $message,
        ];
        // Include sender ID if set
        if ($senderId) {
            $smsData['from'] = $senderId;
        }

        $response = $sms->send($smsData);
        error_log('Resend OTP Response: ' . json_encode($response));

        // Check response structure for success
        if (isset($response['SMSMessageData']['Recipients'][0]['status']) &&
            strtolower($response['SMSMessageData']['Recipients'][0]['status']) === 'success') {
            
            // Record successful OTP send in the database
            $logStmt = $conn->prepare("INSERT INTO sms_logs (user_id, phone, message_type, status) VALUES (?, ?, 'OTP_RESEND', 'SUCCESS')");
            $logStmt->execute([$user['id'], $phone]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'OTP resent successfully',
                'expires_in' => 3600 
            ]);
        } else {
            // Log failed attempt
            $logStmt = $conn->prepare("INSERT INTO sms_logs (user_id, phone, message_type, status, error_message) VALUES (?, ?, 'OTP_RESEND', 'FAILED', ?)");
            $logStmt->execute([$user['id'], $phone, json_encode($response)]);
            
            echo json_encode(['success' => false, 'message' => 'Failed to send OTP']);
        }
    } catch (Exception $e) {
        error_log('Africa\'s Talking Exception: ' . $e->getMessage());
        
        // Log exception details
        $logStmt = $conn->prepare("INSERT INTO sms_logs (user_id, phone, message_type, status, error_message) VALUES (?, ?, 'OTP_RESEND', 'ERROR', ?)");
        $logStmt->execute([$user['id'], $phone, $e->getMessage()]);
        
        echo json_encode(['success' => false, 'message' => 'SMS service error']);
    }

} catch (PDOException $e) {
    error_log('Database Error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
