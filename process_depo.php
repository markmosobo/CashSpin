<?php
/**
 * M-Pesa Deposit Handler for Cash & Spin
 * 
 * Processes M-Pesa deposit requests and responses
 */

// Load environment variables
require_once 'includes/env_loader.php';
$envLoader = new EnvLoader();
$envLoader->load();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class MpesaHandler {
    /**
     * Database connection
     * @var PDO
     */
    private $db;
    
    /**
     * M-Pesa API credentials
     */
    private $consumerKey;
    private $consumerSecret;
    private $passkey;
    private $shortcode;
    private $environment;
    private $callbackUrl;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->connectToDatabase();
        $this->loadMpesaConfig();
    }
    
    /**
     * Connect to the database
     */
    private function connectToDatabase() {
        try {
            $host = EnvLoader::get('DB_HOST', 'localhost');
            $dbname = EnvLoader::get('DB_NAME', 'cash_spin');
            $username = EnvLoader::get('DB_USER', 'root');
            $password = EnvLoader::get('DB_PASS', '');
            $port = EnvLoader::get('DB_PORT', '3306');
            
            $this->db = new PDO(
                "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }
    
    /**
     * Load M-Pesa configuration from environment
     */
    private function loadMpesaConfig() {
        $this->consumerKey = EnvLoader::get('MPESA_CONSUMER_KEY');
        $this->consumerSecret = EnvLoader::get('MPESA_CONSUMER_SECRET');
        $this->passkey = EnvLoader::get('MPESA_PASSKEY');
        $this->shortcode = EnvLoader::get('MPESA_SHORTCODE');
        $this->environment = EnvLoader::get('MPESA_ENVIRONMENT', 'sandbox');
        $this->callbackUrl = EnvLoader::get('MPESA_CALLBACK_URL');
    }
    
    /**
     * Get M-Pesa API access token
     * 
     * @return string Access token
     */
    private function getAccessToken() {
        $url = $this->environment === 'sandbox' 
            ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
            
        $credentials = base64_encode($this->consumerKey . ':' . $this->consumerSecret);
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($curl);
        
        if ($response === false) {
            error_log("cURL Error: " . curl_error($curl));
            throw new Exception("Failed to get access token");
        }
        
        $result = json_decode($response);
        
        if (isset($result->access_token)) {
            return $result->access_token;
        } else {
            error_log("Failed to get access token: " . print_r($result, true));
            throw new Exception("Failed to get access token");
        }
    }
    
    /**
     * Generate a timestamp in the format required by M-Pesa
     * 
     * @return string Timestamp
     */
    private function generateTimestamp() {
        return date('YmdHis');
    }
    
    /**
     * Generate a transaction reference
     * 
     * @return string Reference
     */
    private function generateReference() {
        // Use first 4 letters of 'CASHSPIN' and random numbers
        return 'CASH' . rand(100000, 999999);
    }
    
    /**
     * Generate the password for the STK Push request
     * 
     * @param string $timestamp Current timestamp
     * @return string Base64 encoded password
     */
    private function generatePassword($timestamp) {
        return base64_encode($this->shortcode . $this->passkey . $timestamp);
    }
    
    /**
     * Initiate STK Push request
     * 
     * @param string $phone Phone number
     * @param float $amount Amount to deposit
     * @param int $userId User ID
     * @return array Result of the request
     */
    public function initiateSTKPush($phone, $amount, $userId) {
        try {
            // Format phone number (remove leading 0 and add country code if needed)
            $phone = $this->formatPhoneNumber($phone);
            
            // Generate timestamp
            $timestamp = $this->generateTimestamp();
            
            // Generate transaction reference
            $reference = $this->generateReference();
            
            // Save transaction in database
            $transactionId = $this->saveTransaction($userId, $amount, $reference, $phone);
            
            // Get access token
            $accessToken = $this->getAccessToken();
            
            // STK Push API URL
            $url = $this->environment === 'sandbox'
                ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
                : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            
            // Prepare request data
            $data = [
                'BusinessShortCode' => $this->shortcode,
                'Password' => $this->generatePassword($timestamp),
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => round($amount),
                'PartyA' => $phone,
                'PartyB' => $this->shortcode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $this->callbackUrl,
                'AccountReference' => 'CASHSPIN',
                'TransactionDesc' => 'Deposit to Cash & Spin'
            ];
            
            // Make API request
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json'
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($curl);
            
            if ($response === false) {
                error_log("cURL Error: " . curl_error($curl));
                throw new Exception("Failed to initiate STK Push");
            }
            
            $result = json_decode($response, true);
            
            // Update transaction with STK Push response
            $this->updateTransaction($transactionId, $result);
            
            if (isset($result['ResponseCode']) && $result['ResponseCode'] == '0') {
                return [
                    'success' => true,
                    'message' => 'M-Pesa STK Push sent to your phone. Please enter your PIN to complete the transaction.',
                    'reference' => $reference,
                    'transaction_id' => $transactionId
                ];
            } else {
                return [
                    'success' => false,
                    'message' => isset($result['errorMessage']) ? $result['errorMessage'] : 'Failed to initiate payment',
                    'reference' => $reference
                ];
            }
        } catch (Exception $e) {
            error_log("M-Pesa STK Push Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred. Please try again later.'
            ];
        }
    }
    
    /**
     * Format phone number to required format (254XXXXXXXXX)
     * 
     * @param string $phone
     * @return string Formatted phone number
     */
    private function formatPhoneNumber($phone) {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Remove leading zero if present
        if (substr($phone, 0, 1) === '0') {
            $phone = substr($phone, 1);
        }
        
        // Add country code if not present
        if (substr($phone, 0, 3) !== '254') {
            $phone = '254' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Save transaction to database
     * 
     * @param int $userId User ID
     * @param float $amount Amount
     * @param string $reference Transaction reference
     * @param string $phone Phone number
     * @return int Transaction ID
     */
    private function saveTransaction($userId, $amount, $reference, $phone) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO transactions (user_id, amount, reference, phone, type, status, created_at) 
                VALUES (?, ?, ?, ?, 'deposit', 'pending', NOW())"
            );
            $stmt->execute([$userId, $amount, $reference, $phone]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Failed to save transaction: " . $e->getMessage());
            throw new Exception("Failed to save transaction");
        }
    }
    
    /**
     * Update transaction with STK Push response
     * 
     * @param int $transactionId Transaction ID
     * @param array $response STK Push response
     */
    private function updateTransaction($transactionId, $response) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE transactions SET 
                checkout_request_id = ?, 
                merchant_request_id = ?,
                response_data = ?,
                updated_at = NOW()
                WHERE id = ?"
            );
            $stmt->execute([
                $response['CheckoutRequestID'] ?? null,
                $response['MerchantRequestID'] ?? null,
                json_encode($response),
                $transactionId
            ]);
        } catch (PDOException $e) {
            error_log("Failed to update transaction: " . $e->getMessage());
        }
    }
    
    /**
     * Process M-Pesa STK callback
     * 
     * @param array $callbackData Callback data from M-Pesa
     * @return boolean Success status
     */
    public function processCallback($callbackData) {
        try {
            // Extract relevant data
            $resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
            $resultDesc = $callbackData['Body']['stkCallback']['ResultDesc'];
            $merchantRequestID = $callbackData['Body']['stkCallback']['MerchantRequestID'];
            $checkoutRequestID = $callbackData['Body']['stkCallback']['CheckoutRequestID'];
            
            // Find transaction by checkout request ID
            $stmt = $this->db->prepare(
                "SELECT * FROM transactions WHERE checkout_request_id = ? LIMIT 1"
            );
            $stmt->execute([$checkoutRequestID]);
            $transaction = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$transaction) {
                error_log("Transaction not found for checkout request ID: " . $checkoutRequestID);
                return false;
            }
            
            // Update transaction status
            if ($resultCode == 0) {
                // Success - update transaction and user balance
                $item = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'];
                $amount = $this->findCallbackValue($item, 'Amount');
                $mpesaReceiptNumber = $this->findCallbackValue($item, 'MpesaReceiptNumber');
                
                // Update transaction
                $this->updateTransactionSuccess($transaction['id'], $mpesaReceiptNumber, $resultDesc);
                
                // Update user balance
                $this->updateUserBalance($transaction['user_id'], $amount);
                
                return true;
            } else {
                // Failed - update transaction status
                $this->updateTransactionFailure($transaction['id'], $resultDesc);
                return false;
            }
        } catch (Exception $e) {
            error_log("M-Pesa callback processing error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Find a specific value in the M-Pesa callback data
     * 
     * @param array $items Callback items
     * @param string $name Name of the value to find
     * @return mixed Found value or null
     */
    private function findCallbackValue($items, $name) {
        foreach ($items as $item) {
            if ($item['Name'] == $name) {
                return $item['Value'];
            }
        }
        return null;
    }
    
    /**
     * Update transaction on successful payment
     * 
     * @param int $transactionId Transaction ID
     * @param string $receiptNumber M-Pesa receipt number
     * @param string $resultDesc Result description
     */
    private function updateTransactionSuccess($transactionId, $receiptNumber, $resultDesc) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE transactions SET 
                status = 'completed',
                receipt_number = ?,
                result_desc = ?,
                completed_at = NOW(),
                updated_at = NOW()
                WHERE id = ?"
            );
            $stmt->execute([$receiptNumber, $resultDesc, $transactionId]);
        } catch (PDOException $e) {
            error_log("Failed to update transaction success: " . $e->getMessage());
            throw new Exception("Failed to update transaction");
        }
    }
    
    /**
     * Update transaction on failed payment
     * 
     * @param int $transactionId Transaction ID
     * @param string $resultDesc Result description
     */
    private function updateTransactionFailure($transactionId, $resultDesc) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE transactions SET 
                status = 'failed',
                result_desc = ?,
                updated_at = NOW()
                WHERE id = ?"
            );
            $stmt->execute([$resultDesc, $transactionId]);
        } catch (PDOException $e) {
            error_log("Failed to update transaction failure: " . $e->getMessage());
            throw new Exception("Failed to update transaction");
        }
    }
    
    /**
     * Update user balance
     * 
     * @param int $userId User ID
     * @param float $amount Amount to add
     */
    private function updateUserBalance($userId, $amount) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET 
                balance = balance + ?,
                updated_at = NOW()
                WHERE id = ?"
            );
            $stmt->execute([$amount, $userId]);
        } catch (PDOException $e) {
            error_log("Failed to update user balance: " . $e->getMessage());
            throw new Exception("Failed to update user balance");
        }
    }
}

// Instantiate the handler if this file is accessed directly
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    // Check if this is a callback
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the raw post data
        $callbackData = json_decode(file_get_contents('php://input'), true);
        
        if ($callbackData) {
            // Process the callback
            $mpesaHandler = new MpesaHandler();
            $result = $mpesaHandler->processCallback($callbackData);
            
            // Respond with success to M-Pesa
            header('Content-Type: application/json');
            echo json_encode([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted'
            ]);
            exit;
        }
    }
}
?>