<?php
/**
 * Game Handler for Cash & Spin
 * 
 * Manages game functionality including spins and payouts
 */

// Load environment variables
require_once 'EnvLoader.php';
$envLoader = new EnvLoader();
$envLoader->load();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class GameHandler {
    /**
     * Database connection
     * @var PDO
     */
    private $db;
    
    /**
     * Wheel sections with multipliers
     * @var array
     */
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
        ["text" => "Spin", "multiplier" => 1], // Free spin
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
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->connectToDatabase();
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
            // Allow demo play without database connection
        }
    }
    
    /**
     * Check if user is logged in
     * 
     * @return boolean
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Get user's account balance
     * 
     * @return float
     */
    public function getUserBalance() {
        if (!$this->isLoggedIn()) {
            return 0;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT balance FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return isset($result['balance']) ? floatval($result['balance']) : 0;
        } catch (PDOException $e) {
            error_log("Failed to get user balance: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Process a spin
     * 
     * @param float $betAmount Amount bet
     * @param boolean $isDemo Whether this is a demo spin
     * @return array Spin result
     */
    public function processSpin($betAmount, $isDemo = false) {
        // Validate bet amount
        $betAmount = floatval($betAmount);
        $minBet = EnvLoader::get('MIN_BET_AMOUNT', 10);
        $maxBet = EnvLoader::get('MAX_BET_AMOUNT', 10000);
        
        if ($betAmount < $minBet || $betAmount > $maxBet) {
            return [
                'success' => false,
                'message' => "Bet amount must be between {$minBet} and {$maxBet}"
            ];
        }
        
        // Check balance for live play
        if (!$isDemo) {
            if (!$this->isLoggedIn()) {
                return [
                    'success' => false,
                    'message' => 'You must be logged in to play with real money'
                ];
            }
            
            $balance = $this->getUserBalance();
            if ($balance < $betAmount) {
                return [
                    'success' => false,
                    'message' => 'Insufficient funds',
                    'balance' => $balance
                ];
            }
            
            // Deduct bet amount from balance
            $this->updateUserBalance(-$betAmount);
        }
        
        // Generate random spin result
        $sectionIndex = rand(0, count($this->wheelSections) - 1);
        $section = $this->wheelSections[$sectionIndex];
        
        // Calculate winnings
        $multiplier = $section['multiplier'];
        $winnings = $betAmount * $multiplier;
        $isFreeRespin = ($section['text'] === 'Spin');
        
        // For live play, add winnings to balance if won
        if (!$isDemo && $winnings > 0) {
            $this->updateUserBalance($winnings);
        }
        
        // Save spin result for live play
        if (!$isDemo) {
            $this->saveSpin($_SESSION['user_id'], $betAmount, $sectionIndex, $multiplier, $winnings);
        }
        
        // Get updated balance for live play
        $balance = !$isDemo ? $this->getUserBalance() : null;
        
        // Return result
        return [
            'success' => true,
            'section' => $sectionIndex,
            'text' => $section['text'],
            'multiplier' => $multiplier,
            'betAmount' => $betAmount,
            'winnings' => $winnings,
            'balance' => $balance,
            'isDemo' => $isDemo,
            'isFreeRespin' => $isFreeRespin,
            'rotationDegrees' => (360 / count($this->wheelSections)) * $sectionIndex
        ];
    }
    
    /**
     * Update user balance
     * 
     * @param float $amount Amount to add (negative for deduction)
     * @return boolean Success status
     */
    private function updateUserBalance($amount) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET 
                balance = balance + ?,
                updated_at = NOW()
                WHERE id = ?"
            );
            return $stmt->execute([$amount, $_SESSION['user_id']]);
        } catch (PDOException $e) {
            error_log("Failed to update user balance: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Save spin result to database
     * 
     * @param int $userId User ID
     * @param float $betAmount Bet amount
     * @param int $sectionIndex Section index
     * @param float $multiplier Multiplier
     * @param float $winnings Winnings
     * @return boolean Success status
     */
    private function saveSpin($userId, $betAmount, $sectionIndex, $multiplier, $winnings) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO spins (
                    user_id, bet_amount, section_index, multiplier, 
                    winnings, created_at
                ) VALUES (?, ?, ?, ?, ?, NOW())"
            );
            return $stmt->execute([
                $userId, $betAmount, $sectionIndex,
                $multiplier, $winnings
            ]);
        } catch (PDOException $e) {
            error_log("Failed to save spin: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's spin history
     * 
     * @param int $limit Number of records to return
     * @return array Spin history
     */
    public function getSpinHistory($limit = 10) {
        if (!$this->isLoggedIn()) {
            return [];
        }
        
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
            error_log("Failed to get spin history: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Process bonus round
     * 
     * @param float $originalBet Original bet amount that triggered the bonus
     * @param boolean $isDemo Whether this is a demo play
     * @return array Bonus result
     */
    
    public function processBonusRound($originalBet, $isDemo = false) {
        $bonusSpins = [];
        $totalWinnings = 0;
        
        // Create bonus wheel with better odds
        $bonusWheel = $this->wheelSections;
        // Remove the worst results
        $bonusWheel = array_filter($bonusWheel, function($section) {
            return $section['multiplier'] >= 1; 
        });
        $bonusWheel = array_values($bonusWheel); // Reindex array
        
        // Perform 3 bonus spins
        for ($i = 0; $i < 3; $i++) {
            // Generate random spin result with better odds
            $sectionIndex = rand(0, count($bonusWheel) - 1);
            $section = $bonusWheel[$sectionIndex];
            
            // Calculate winnings
            $multiplier = $section['multiplier'];
            $winnings = $originalBet * $multiplier;
            $totalWinnings += $winnings;
            
            // Add to bonus spins array
            $bonusSpins[] = [
                'section' => $sectionIndex,
                'text' => $section['text'],
                'multiplier' => $multiplier,
                'winnings' => $winnings,
                'rotationDegrees' => (360 / count($bonusWheel)) * $sectionIndex
            ];
        }
        
        // For live play, add total winnings to balance
        if (!$isDemo && $totalWinnings > 0) {
            $this->updateUserBalance($totalWinnings);
            
            // Save bonus round in database
            $this->saveBonusRound($_SESSION['user_id'], $originalBet, $totalWinnings);
        }
        
        // Get updated balance for live play
        $balance = !$isDemo ? $this->getUserBalance() : null;
        
        // Return result
        return [
            'success' => true,
            'bonusSpins' => $bonusSpins,
            'originalBet' => $originalBet,
            'totalWinnings' => $totalWinnings,
            'balance' => $balance,
            'isDemo' => $isDemo
        ];
    }
    
    /**
     * Save bonus round result to database
     * 
     * @param int $userId User ID
     * @param float $originalBet Original bet amount
     * @param float $totalWinnings Total winnings from bonus round
     * @return boolean Success status
     */
    private function saveBonusRound($userId, $originalBet, $totalWinnings) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO bonus_rounds (
                    user_id, original_bet, total_winnings, created_at
                ) VALUES (?, ?, ?, NOW())"
            );
            return $stmt->execute([
                $userId, $originalBet, $totalWinnings
            ]);
        } catch (PDOException $e) {
            error_log("Failed to save bonus round: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user's bonus round history
     * 
     * @param int $limit Number of records to return
     * @return array Bonus round history
     */
    public function getBonusHistory($limit = 5) {
        if (!$this->isLoggedIn()) {
            return [];
        }
        
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM bonus_rounds 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ?"
            );
            $stmt->execute([$_SESSION['user_id'], $limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to get bonus history: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get game statistics for the user
     * 
     * @return array Game statistics
     */
    public function getUserStatistics() {
        if (!$this->isLoggedIn()) {
            return [
                'totalSpins' => 0,
                'totalWagered' => 0,
                'totalWon' => 0,
                'biggestWin' => 0,
                'returnToPlayer' => 0
            ];
        }
        
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    COUNT(*) as totalSpins,
                    SUM(bet_amount) as totalWagered,
                    SUM(winnings) as totalWon,
                    MAX(winnings) as biggestWin
                FROM spins 
                WHERE user_id = ?"
            );
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Calculate return to player
            $totalWagered = floatval($result['totalWagered']);
            $returnToPlayer = $totalWagered > 0 ? 
                (floatval($result['totalWon']) / $totalWagered) * 100 : 0;
            
            return [
                'totalSpins' => intval($result['totalSpins']),
                'totalWagered' => $totalWagered,
                'totalWon' => floatval($result['totalWon']),
                'biggestWin' => floatval($result['biggestWin']),
                'returnToPlayer' => round($returnToPlayer, 2)
            ];
        } catch (PDOException $e) {
            error_log("Failed to get user statistics: " . $e->getMessage());
            return [
                'totalSpins' => 0,
                'totalWagered' => 0,
                'totalWon' => 0,
                'biggestWin' => 0,
                'returnToPlayer' => 0
            ];
        }
    }
    
    /**
     * Get leaderboard for top winners
     * 
     * @param int $limit Number of records to return
     * @return array Leaderboard data
     */
    public function getLeaderboard($limit = 10) {
        try {
            $stmt = $this->db->prepare(
                "SELECT 
                    u.username, 
                    MAX(s.winnings) as biggestWin,
                    COUNT(s.id) as spinCount
                FROM users u
                JOIN spins s ON u.id = s.user_id
                GROUP BY u.id
                ORDER BY biggestWin DESC
                LIMIT ?"
            );
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Failed to get leaderboard: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get the wheel sections configuration
     * 
     * @return array Wheel sections
     */
    public function getWheelSections() {
        return $this->wheelSections;
    }
}