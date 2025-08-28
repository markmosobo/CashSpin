<?php
/**
 * Enhanced Game Handler for Cash & Spin
 * 
 * Manages game functionality including authentication, spins, and payouts
 */

require_once 'includes/env_loader.php';
$envLoader = new EnvLoader();
$envLoader->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class GameHandler {
    private $db;
    private $wheelSections = [
        ["text" => "Bonus", "multiplier" => 3], 
        ["text" => "X2", "multiplier" => 2], 
        ["text" => "X0.5", "multiplier" => 0.5], 
        ["text" => "X1", "multiplier" => 1], 
        ["text" => "X10", "multiplier" => 10], 
        ["text" => "X2.5", "multiplier" => 2.5], 
        ["text" => "X3", "multiplier" => 3], 
        ["text" => "X5", "multiplier" => 5], 
        ["text" => "X4", "multiplier" => 4], 
        ["text" => "X100", "multiplier" => 100], 
        ["text" => "Spin", "multiplier" => 1],
        ["text" => "X1.5", "multiplier" => 1.5], 
        ["text" => "X2", "multiplier" => 2], 
        ["text" => "X4", "multiplier" => 4], 
        ["text" => "Bonus", "multiplier" => 3], 
        ["text" => "X1.5", "multiplier" => 1.5], 
        ["text" => "X10", "multiplier" => 10], 
        ["text" => "X2", "multiplier" => 2], 
        ["text" => "X0", "multiplier" => 0], 
        ["text" => "X5", "multiplier" => 5], 
        ["text" => "X2", "multiplier" => 2], 
        ["text" => "X1", "multiplier" => 1]
    ];
    
    public function __construct() {
        $this->connectToDatabase();
    }
    
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
        }
    }
    
    // Authentication Methods
    
    public function registerUser($phone, $password, $registrationCode, $referralCode = null) {
        try {
            // Check if phone already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->execute([$phone]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Phone number already registered'];
            }
            
            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $this->db->prepare(
                "INSERT INTO users (
                    phone, password_hash, registration_code, 
                    referral_code, accepted_terms, accepted_responsibility
                ) VALUES (?, ?, ?, ?, 1, 1)"
            );
            $stmt->execute([$phone, $passwordHash, $registrationCode, $referralCode]);
            
            return ['success' => true, 'userId' => $this->db->lastInsertId()];
        } catch (PDOException $e) {
            error_log("Registration failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed'];
        }
    }
    
    public function verifyUser($phone, $verificationCode) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET 
                is_verified = 1,
                registration_code = NULL
                WHERE phone = ? AND registration_code = ?"
            );
            $stmt->execute([$phone, $verificationCode]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => true];
            }
            return ['success' => false, 'message' => 'Invalid verification code'];
        } catch (PDOException $e) {
            error_log("Verification failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Verification failed'];
        }
    }
    
    public function loginUser($phone, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = ?");
            $stmt->execute([$phone]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // 🔹 Commented out verification for now
                /*
                if (!$user['is_verified']) {
                    return ['success' => false, 'message' => 'Account not verified'];
                }
                */
                
                // Update last login
                $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")
                    ->execute([$user['id']]);
                
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_name'] = $user['username'] ?? $user['phone'];
                
                return ['success' => true, 'user' => $user];
            }
            
            return ['success' => false, 'message' => 'Invalid phone or password'];
        } catch (PDOException $e) {
            error_log("Login failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed'];
        }
    }

    
    public function logout() {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Game Methods
    
    public function getUserBalance() {
        if (!$this->isLoggedIn()) return 0;
        
        try {
            $stmt = $this->db->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? floatval($result['balance']) : 0;
        } catch (PDOException $e) {
            error_log("Failed to get balance: " . $e->getMessage());
            return 0;
        }
    }
    
    public function processSpin($betAmount, $isDemo = false) {
        $betAmount = floatval($betAmount);
        $minBet = EnvLoader::get('MIN_BET_AMOUNT', 20);
        $maxBet = EnvLoader::get('MAX_BET_AMOUNT', 10000);
        
        if ($betAmount < $minBet || $betAmount > $maxBet) {
            return [
                'success' => false,
                'message' => "Bet amount must be between {$minBet} and {$maxBet}"
            ];
        }
        
        if (!$isDemo) {
            if (!$this->isLoggedIn()) {
                return ['success' => false, 'message' => 'Login required'];
            }
            
            $balance = $this->getUserBalance();
            if ($balance < $betAmount) {
                return ['success' => false, 'message' => 'Insufficient funds'];
            }
            
            // Deduct bet amount
            $this->updateUserBalance(-$betAmount);
            $this->logTransaction($_SESSION['user_id'], 'bet', $betAmount);
        }
        
        // Generate spin result
        $sectionIndex = rand(0, count($this->wheelSections) - 1);
        $section = $this->wheelSections[$sectionIndex];
        $multiplier = $section['multiplier'];
        $winnings = $betAmount * $multiplier;
        $isFreeRespin = ($section['text'] === 'Spin');
        
        if (!$isDemo && $winnings > 0) {
            $this->updateUserBalance($winnings);
            $this->logTransaction($_SESSION['user_id'], 'win', $winnings);
        }
        
        if (!$isDemo) {
            $this->saveSpin($_SESSION['user_id'], $betAmount, $sectionIndex, $multiplier, $winnings);
        }
        
        return [
            'success' => true,
            'section' => $sectionIndex,
            'text' => $section['text'],
            'multiplier' => $multiplier,
            'betAmount' => $betAmount,
            'winnings' => $winnings,
            'balance' => !$isDemo ? $this->getUserBalance() : null,
            'isDemo' => $isDemo,
            'isFreeRespin' => $isFreeRespin,
            'rotationDegrees' => (360 / count($this->wheelSections)) * $sectionIndex
        ];
    }
    
    public function processDeposit($userId, $amount, $phoneNumber) {
        try {
            // In a real implementation, this would call M-Pesa API
            $mpesaCode = 'MPESA' . strtoupper(uniqid());
            
            $stmt = $this->db->prepare(
                "INSERT INTO deposits (
                    user_id, amount, phone_number, mpesa_code, status
                ) VALUES (?, ?, ?, ?, 'pending')"
            );
            $stmt->execute([$userId, $amount, $phoneNumber, $mpesaCode]);
            
            // Simulate successful deposit after 5 seconds
            $depositId = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'message' => 'Deposit initiated',
                'depositId' => $depositId,
                'mpesaCode' => $mpesaCode
            ];
        } catch (PDOException $e) {
            error_log("Deposit failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Deposit failed'];
        }
    }
    
    public function completeDeposit($depositId) {
        try {
            $this->db->beginTransaction();
            
            // Get deposit info
            $stmt = $this->db->prepare(
                "SELECT user_id, amount FROM deposits 
                WHERE id = ? AND status = 'pending' FOR UPDATE"
            );
            $stmt->execute([$depositId]);
            $deposit = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$deposit) {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Deposit not found'];
            }
            
            // Update user balance
            $stmt = $this->db->prepare(
                "UPDATE users SET balance = balance + ? WHERE id = ?"
            );
            $stmt->execute([$deposit['amount'], $deposit['user_id']]);
            
            // Update deposit status
            $stmt = $this->db->prepare(
                "UPDATE deposits SET 
                status = 'completed',
                completed_at = NOW()
                WHERE id = ?"
            );
            $stmt->execute([$depositId]);
            
            // Log transaction
            $this->logTransaction(
                $deposit['user_id'], 
                'deposit', 
                $deposit['amount']
            );
            
            $this->db->commit();
            return ['success' => true];
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Complete deposit failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Complete deposit failed'];
        }
    }
    
    // Helper Methods
    
    private function updateUserBalance($amount) {
        if (!$this->isLoggedIn()) return false;
        
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET balance = balance + ? WHERE id = ?"
            );
            return $stmt->execute([$amount, $_SESSION['user_id']]);
        } catch (PDOException $e) {
            error_log("Balance update failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function saveSpin($userId, $betAmount, $sectionIndex, $multiplier, $winnings) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO spins (
                    user_id, bet_amount, section_index, 
                    multiplier, winnings, created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$userId, $betAmount, $sectionIndex, $multiplier, $winnings]);
            return true;
        } catch (PDOException $e) {
            error_log("Save spin failed: " . $e->getMessage());
            return false;
        }
    }
    
    private function logTransaction($userId, $type, $amount) {
        try {
            $balance = $this->getUserBalance();
            $reference = strtoupper(uniqid());
            
            $stmt = $this->db->prepare(
                "INSERT INTO transactions (
                    user_id, type, amount, balance_after, 
                    reference, status, created_at
                ) VALUES (?, ?, ?, ?, ?, 'completed', NOW())"
            );
            $stmt->execute([$userId, $type, $amount, $balance, $reference]);
            return true;
        } catch (PDOException $e) {
            error_log("Transaction log failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getSpinHistory($limit = 10) {
        if (!$this->isLoggedIn()) return [];
        
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM spins 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?"
            );
            $stmt->execute([$_SESSION['user_id'], $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get spin history failed: " . $e->getMessage());
            return [];
        }
    }
    
    public function getTransactionHistory($limit = 10) {
        if (!$this->isLoggedIn()) return [];
        
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM transactions 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?"
            );
            $stmt->execute([$_SESSION['user_id'], $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get transaction history failed: " . $e->getMessage());
            return [];
        }
    }
    
    public function getWheelSections() {
        return $this->wheelSections;
    }
}