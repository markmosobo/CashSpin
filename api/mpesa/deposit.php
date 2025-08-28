<?php
/**
 * User Account Handler for Cash & Spin
 * 
 * Manages user sessions, account functionality, and UI changes after login
 */


require_once 'includes/env_loader.php';
$envLoader = new EnvLoader();
$envLoader->load();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class UserAccountHandler {
    /**
     * Database connection
     * @var PDO
     */
    private $db;
    
    /**
     * User data
     * @var array
     */
    private $userData = null;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->connectToDatabase();
        $this->loadUserData();
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
            // Log error but don't expose details
            error_log("Database connection failed: " . $e->getMessage());
            // Fallback to continue with limited functionality
        }
    }
    
    /**
     * Load user data from session
     */
    private function loadUserData() {
        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $this->userData = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Failed to load user data: " . $e->getMessage());
            }
        }
    }
    
    /**
     * Check if user is logged in
     * 
     * @return boolean
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && $this->userData !== false;
    }
    
    /**
     * Get user's account balance
     * 
     * @return float
     */
    public function getBalance() {
        return $this->userData ? floatval($this->userData['balance']) : 0;
    }
    
    /**
     * Process login attempt
     * 
     * @param string $email
     * @param string $password
     * @return array Success status and message
     */
    public function processLogin($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['last_activity'] = time();
                
                $this->userData = $user;
                
                // Update last login
                $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
                
                return [
                    'success' => true,
                    'message' => 'Login successful'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred during login'
            ];
        }
    }
    
    /**
     * Process user logout
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Clear the user data
        $this->userData = null;
    }
    
    /**
     * Process M-Pesa deposit
     * 
     * @param float $amount Amount to deposit
     * @return array Result of deposit attempt
     */
    public function processDeposit($amount) {
        if (!$this->isLoggedIn()) {
            return [
                'success' => false,
                'message' => 'You must be logged in to make a deposit'
            ];
        }
        
        if ($amount < EnvLoader::get('MIN_BET_AMOUNT', 10)) {
            return [
                'success' => false,
                'message' => 'Minimum deposit amount is ' . EnvLoader::get('MIN_BET_AMOUNT', 10) . ' ' . EnvLoader::get('DEFAULT_CURRENCY', 'KES')
            ];
        }
        
        // Here you would integrate with M-Pesa API
        // For now, we'll just return a placeholder response
        return [
            'success' => true,
            'message' => 'Deposit request initiated. Follow the prompts on your phone to complete the transaction.',
            'reference' => 'DEP' . time()
        ];
    }
    
    /**
     * Get HTML for user account section in navbar
     * 
     * @return string HTML content
     */
    public function getUserAccountNavHTML() {
        if ($this->isLoggedIn()) {
            // User is logged in, show account section
            $balance = number_format($this->getBalance(), 2);
            $userName = htmlspecialchars($_SESSION['user_name']);
            $currency = EnvLoader::get('DEFAULT_CURRENCY', 'KES');
            
            return <<<HTML
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user-circle mr-1"></i> {$userName}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="accountDropdown">
                        <div class="dropdown-item">
                            <strong>Balance:</strong> {$currency} {$balance}
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="account">
                            <i class="fa fa-cog mr-2"></i> My Account
                        </a>
                        <a class="dropdown-item" href="transactions">
                            <i class="fa fa-history mr-2"></i> Transaction History
                        </a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#depositModal">
                            <i class="fa fa-money-bill-wave mr-2"></i> Deposit Funds
                        </a>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#withdrawModal">
                            <i class="fa fa-credit-card mr-2"></i> Withdraw Funds
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php">
                            <i class="fa fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
            HTML;
        } else {
            // User is not logged in, show just a simplified header with login/register options
            return <<<HTML
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link open-modal" data-target="#loginModal">
                        <i class="fa fa-sign-in-alt mr-1"></i> Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link open-modal" data-target="#registerModal">
                        <i class="fa fa-user-plus mr-1"></i> Register
                    </a>
                </li>
            </ul>
            HTML;
        }
    }
    
    /**
     * Get HTML for game controls (demo play when not logged in)
     * 
     * @return string HTML content
     */
    public function getGameControlsHTML() {
        $currency = EnvLoader::get('DEFAULT_CURRENCY', 'KES');
        
        if ($this->isLoggedIn()) {
            // User is logged in, show live play options
            $balance = number_format($this->getBalance(), 2);
            
            return <<<HTML
            <div class="bg-blue spindemo d-flex flex-wrap gap-2 justify-content-center">
                <h2 class="text-uppercase">Live Play</h2>
                <p class="full-width mb-2">Your Balance: <strong>{$currency} {$balance}</strong></p>
                
                <div class="btn-group mb-3" role="group">
                    <button type="button" class="btn btn-outline-light active play-mode" data-mode="live">Live Play</button>
                    <button type="button" class="btn btn-outline-light play-mode" data-mode="demo">Demo Play</button>
                </div>
                
                <p class="full-width">
                    <br>CHOOSE YOUR STAKE AMOUNT</p>
                
                <div class="input-group mb-3">
                    <div id="radioBtn" class="btn-group">
                        <a data-toggle="fun" data-title="{$currency} 20" class="btn btn-primary btn-sm not Active">{$currency} 20</a>
                        <a data-toggle="fun" data-title="{$currency} 50" class="btn btn-primary btn-sm not Active">{$currency} 50</a>
                        <a data-toggle="fun" data-title="{$currency} 100" class="btn btn-primary btn-sm not Active">{$currency} 100</a>
                        <a data-toggle="fun" data-title="{$currency} 150" class="btn btn-primary btn-sm not Active">{$currency} 150</a>
                        <a data-toggle="fun" data-title="{$currency} 200" class="btn btn-primary btn-sm not Active">{$currency} 200</a>
                        <a data-toggle="fun" data-title="{$currency} 250" class="btn btn-primary btn-sm not Active">{$currency} 250</a>
                    </div>
                    <input type="hidden" name="fun" id="fun">
                </div>
                
                <button class="btn btn-primary btn-lg spinBtn spinNow" style disabled="disabled">Click to spin the wheel</button>
                
                <div class="insufficient-funds alert alert-warning mt-2" style="display:none">
                    Insufficient funds! <a href="#" data-toggle="modal" data-target="#depositModal">Add funds</a> to continue playing.
                </div>
                
                <div class="mt-1 spinner-loader" style="display:none">
                    <img src="/spinner.gif" alt="Please wait">
                </div>
            </div>
            
            <!-- Deposit Modal -->
            <div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-labelledby="depositModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="depositModalLabel">Deposit Funds</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="depositForm">
                                <div class="form-group">
                                    <label for="depositAmount">Amount ({$currency})</label>
                                    <input type="number" class="form-control" id="depositAmount" min="10" step="10" required>
                                    <small class="form-text text-muted">Minimum deposit: {$currency} 10</small>
                                </div>
                                <div class="form-group">
                                    <label for="depositPhone">M-Pesa Phone Number</label>
                                    <input type="tel" class="form-control" id="depositPhone" placeholder="e.g. 07XXXXXXXX" required>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" id="initiateDeposit">Deposit Now</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <script>
                // Switch between live and demo play
                $('.play-mode').click(function() {
                    $('.play-mode').removeClass('active');
                    $(this).addClass('active');
                    
                    if ($(this).data('mode') === 'demo') {
                        $('.spinBtn').text('Click to spin demo wheel');
                        // Ensure "insufficient funds" warning is hidden in demo mode
                        $('.insufficient-funds').hide();
                    } else {
                        $('.spinBtn').text('Click to spin the wheel');
                    }
                });
                
                // Handle deposit form submission
                $('#initiateDeposit').click(function() {
                    if (!$('#depositForm')[0].checkValidity()) {
                        $('#depositForm')[0].reportValidity();
                        return;
                    }
                    
                    // Show loading state
                    const button = $(this);
                    const originalText = button.text();
                    button.prop('disabled', true).text('Processing...');
                    
                    // Send deposit request to server
                    $.ajax({
                        url: 'process_depo.php',
                        method: 'POST',
                        data: {
                            amount: $('#depositAmount').val(),
                            phone: $('#depositPhone').val()
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Hide modal
                                $('#depositModal').modal('hide');
                                
                                // Show success message
                                alert(response.message);
                            } else {
                                // Show error message
                                alert(response.message);
                            }
                        },
                        error: function() {
                            alert('An error occurred. Please try again later.');
                        },
                        complete: function() {
                            // Reset button state
                            button.prop('disabled', false).text(originalText);
                        }
                    });
                });
            </script>
            HTML;
        } else {
            // User is not logged in, show demo play by default without registration prompt
            return <<<HTML
            <div class="bg-blue spindemo d-flex flex-wrap gap-2 justify-content-center">
                <h2 class="text-uppercase">Cash & Spin Game</h2>
                <p class="full-width mb-2">Playing with demo account</p>
                
                <p class="full-width">
                    CHOOSE YOUR DEMO STAKE TO PLAY</p>
                <div class="input-group mb-3">
                    <div id="radioBtn" class="btn-group">
                        <a data-toggle="fun" data-title="{$currency} 20" class="btn btn-primary btn-sm not Active">{$currency} 20</a>
                        <a data-toggle="fun" data-title="{$currency} 50" class="btn btn-primary btn-sm not Active">{$currency} 50</a>
                        <a data-toggle="fun" data-title="{$currency} 100" class="btn btn-primary btn-sm not Active">{$currency} 100</a>
                        <a data-toggle="fun" data-title="{$currency} 150" class="btn btn-primary btn-sm not Active">{$currency} 150</a>
                        <a data-toggle="fun" data-title="{$currency} 200" class="btn btn-primary btn-sm not Active">{$currency} 200</a>
                        <a data-toggle="fun" data-title="{$currency} 250" class="btn btn-primary btn-sm not Active">{$currency} 250</a>
                    </div>
                    <input type="hidden" name="fun" id="fun">
                </div>
                <button class="btn btn-primary btn-lg spinBtn spinNow" style disabled="disabled">Click to spin demo wheel</button>
                
                <p class="mt-3 mb-1 text-center small">
                    <a href="#" data-target="#registerModal" class="btn btn-sm btn-light open-modal">
                        <i class="fa fa-user-plus mr-1"></i> Register to play for real cash
                    </a>
                </p>
                
                <div class="mt-1 spinner-loader" style="display:none">
                    <img src="/spinner.gif" alt="Please wait">
                </div>
            </div>
            
            <script>
                // When a bet amount is selected
                $('.btn-group a').click(function() {
                    $('.btn-group a').removeClass('Active').addClass('not');
                    $(this).removeClass('not').addClass('Active');
                    $('#fun').val($(this).data('title'));
                    
                    // Enable spin button
                    $('.spinBtn').prop('disabled', false);
                });
                
                // Handle spin button click for demo play
                $('.spinBtn').click(function() {
                    if ($(this).prop('disabled')) {
                        return;
                    }
                    
                    // Show loading indicator
                    $('.spinner-loader').show();
                    $(this).prop('disabled', true);
                    
                    // Get bet amount from the active button
                    const betAmount = $('#fun').val().replace(/[^0-9.]/g, '');
                    
                    // Process demo spin via AJAX
                    $.ajax({
                        url: 'process_spin.php',
                        method: 'POST',
                        data: {
                            betAmount: betAmount,
                            isDemo: true
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Spin the wheel and show results
                                spinWheel(response);
                            } else {
                                // Show error message
                                alert(response.message);
                                $('.spinBtn').prop('disabled', false);
                                $('.spinner-loader').hide();
                            }
                        },
                        error: function() {
                            alert('An error occurred. Please try again.');
                            $('.spinBtn').prop('disabled', false);
                            $('.spinner-loader').hide();
                        }
                    });
                });
                
                // Function to handle wheel spinning animation and results
                function spinWheel(result) {
                    // Animation code would go here
                    // For now, we'll just simulate with a timeout
                    setTimeout(function() {
                        $('.spinner-loader').hide();
                        
                        // Display result
                        const winnings = parseFloat(result.winnings).toFixed(2);
                        const message = result.text === 'Spin' ? 
                            'You won a free spin!' : 
                            'You won {$currency} ' + winnings + '!';
                            
                        alert(message);
                        
                        // Re-enable spin button
                        $('.spinBtn').prop('disabled', false);
                        
                        // If free spin, automatically spin again
                        if (result.isFreeRespin) {
                            $('.spinBtn').click();
                        }
                    }, 2000);
                }
            </script>
            HTML;
        }
    }
}

// Instantiate the user account handler
$userAccount = new UserAccountHandler();

// Optional: If this file is requested via AJAX, return the required HTML
if (isset($_GET['section']) && $_GET['section'] === 'navbar') {
    echo $userAccount->getUserAccountNavHTML();
    exit;
} else if (isset($_GET['section']) && $_GET['section'] === 'game-controls') {
    echo $userAccount->getGameControlsHTML();
    exit;
}
?>