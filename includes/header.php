<?php
/**
 * Enhanced User Account Handler for Cash & Spin Game
 * This script handles user login, session management, and UI rendering for the Cash & Spin game.
 * It connects to a MySQL database, retrieves user data, and generates the HTML for the game interface.
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
    private $db;
    private $userData = null;
    
    public function __construct() {
        $this->connectToDatabase();
        $this->loadUserData();
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
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && $this->userData !== false;
    }
    
    public function getBalance() {
        return $this->userData ? floatval($this->userData['balance']) : 0;
    }
    
    public function getUserData() {
        return $this->userData;
    }
    
    public function processLogin($phone, $password) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = ?");
            $stmt->execute([$phone]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                if (!$user['is_verified']) {
                    return [
                        'success' => false,
                        'message' => 'Account not verified. Please verify your phone number.'
                    ];
                }
                
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_name'] = $user['username'] ?? $user['phone'];
                $this->userData = $user;
                
                // Update last login
                $this->db->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);
                
                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => $user
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Invalid phone or password'
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
    
    public function logout() {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $this->userData = null;
    }
    
    public function getHeaderHTML() {
        $currency = EnvLoader::get('DEFAULT_CURRENCY', 'KES');
        
        return <<<HTML
        <header>
            <nav class="navbar navbar-expand-lg" style="background-color: #D4AF37">
                <div class="container">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="fa fa-bars text-black"></span>
                    </button>

                    <a class="navbar-brand w-auto responsible-link" href="index">
                        <img src="new-tpl/imgs/logo.png" alt="CashSpin" />
                    </a>

                    {$this->getMobileAuthButtons()}

                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav mx-auto centered">
                            <li class="nav-item">
                                <a class="nav-link responsible-link" href="how-to-play">How to Play</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link responsible-link" href="faq">Help</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="blog/index" target="_blank" rel="noopener">Blog</a>
                            </li>
                            <li class="nav-item dropdown d-link">
                                <a href="#" class="nav-link dropdown-toggle" id="navDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Other Games</a>
                                <div class="dropdown-menu text-black" aria-labelledby="navDropdown">
                                    <a href="predict-game" class="dropdown-item responsible-link">Choose-a-Box</a>
                                </div>
                            </li>
                        </ul>

                        {$this->getDesktopAuthButtons()}
                    </div>
                </div>
            </nav>
            
            <div class="inner">
                <div id="app">
                    <div class="container-fluid h-100">
                        <div class="row h-100 align-items-center">
                            {$this->getWheelHTML()}
                            {$this->getSideImageHTML()}
                            {$this->getGameControlsHTML()}
                        </div>
                    </div>
                </div>
            </div>
        </header>
        HTML;
    }
    
    private function getMobileAuthButtons() {
        if ($this->isLoggedIn()) {
            return '';
        }
        return <<<HTML
        <a class="btn btn-primary btn-mobile d-block d-lg-none ml-auto mr-2" data-toggle="modal" data-target="#loginModal">
            <i class="fa fa-sign-in-alt mr-2"></i> Login
        </a>

        <a class="btn btn-primary btn-mobile d-block d-lg-none" data-toggle="modal" data-target="#registerModal">
            <i class="fa fa-user mr-2"></i> Register
        </a>
        HTML;
    }
    
    private function getDesktopAuthButtons() {
        if ($this->isLoggedIn()) {
            $balance = number_format($this->getBalance(), 2);
            $userName = htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['user_phone']);
            $currency = EnvLoader::get('DEFAULT_CURRENCY', 'KES');
            
            return <<<HTML
            <ul class="navbar-nav ml-auto">
                <li class="nav-item mr-2">
                    <span class="nav-link">
                        <i class="fa fa-wallet mr-1"></i> {$currency} {$balance}
                    </span>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-user-circle mr-1"></i> {$userName}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="accountDropdown">
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
            return <<<HTML
            <ul class="navbar-nav ml-auto">
                <li class="nav-item mr-0 mr-md-2 mb-2 mb-md-0">
                    <a class="nav-link btn btn-primary" data-toggle="modal" data-target="#loginModal">
                        <i class="fa fa-sign-in-alt mr-2"></i> Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-primary" data-toggle="modal" data-target="#registerModal">
                        <i class="fa fa-user mr-2"></i> Register
                    </a>
                </li>
            </ul>
            HTML;
        }
    }
    
    private function getWheelHTML() {
        $numPartitions = 22;
        $angleStep = 360 / $numPartitions;
        $texts = [
            "Bonus", "X2", "X0.5", "X1", "X10", "X2.5", "X3", "X5", "X4", "X100",
            "Spin", "X1.5", "X2", "X4", "Bonus", "X1.5", "X10", "X2", "X0", "X5",
            "X2", "X1"
        ];
        
        $wheelSections = '';
        for ($i = 0; $i < $numPartitions; $i++) {
            $startAngle = $i * $angleStep;
            $endAngle = ($i + 1) * $angleStep;
            $x1 = 250 + 250 * cos(deg2rad($startAngle));
            $y1 = 250 + 250 * sin(deg2rad($startAngle));
            $x2 = 250 + 250 * cos(deg2rad($endAngle));
            $y2 = 250 + 250 * sin(deg2rad($endAngle));
            $textAngle = $startAngle + ($angleStep / 2);
            $textX = 250 + 200 * cos(deg2rad($textAngle));
            $textY = 250 + 200 * sin(deg2rad($textAngle));
            
            $wheelSections .= '<path d="M250,250 L' . $x1 . ',' . $y1 . ' A250,250 0 0,1 ' . $x2 . ',' . $y2 . ' Z" fill="#fff" stroke="#000" stroke-width="1"></path>';
            $wheelSections .= '<text x="' . $textX . '" y="' . $textY . '" text-anchor="middle" dominant-baseline="middle" transform="rotate(' . ($textAngle - 90) . ',' . $textX . ',' . $textY . ')" font-size="12" fill="#000">' . $texts[$i] . '</text>';
        }
        
        return <<<HTML
        <div class="col-12 col-md-4 wheelContainer h-100">
            <div class="wheel s-wheel-ctn-div">
                <g class="peg">
                    <rect x="245" y="10" width="10" height="30" fill="#000"></rect>
                </g>
                <div class="wheel-section">
                    <svg class="wheelSVG" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500">
                        <g class="wheel">
                            {$wheelSections}
                        </g>
                        <g class="centerCircle">
                            <circle cx="250" cy="250" r="100" fill="#fff"></circle>
                        </g>
                    </svg>
                    <div class="toast">
                        <p> Spin and win <br> up to x1000 your stake!</p>
                    </div>
                </div>
            </div>
        </div>
        HTML;
    }
 
    private function getSideImageHTML() {
        return <<<HTML
        <div class="col-12 col-md-3 h-100 d-flex align-items-center">
            <img src="new-tpl/imgs/dsws@2x.png" class="img-fluid mx-auto">
        </div>
        HTML;
    }
    
    /**
     * Generates the HTML for the game controls section.
     * This includes the stake amount selection and spin button.
     *
     * @return string The HTML for the game controls section.
    */

    public function getGameControlsHTML() {
        $currency = EnvLoader::get('DEFAULT_CURRENCY', 'KES');
        
        if ($this->isLoggedIn()) {
            $balance = number_format($this->getBalance(), 2);
            
            return <<<HTML
            <div class="col-12 col-md-6 col-lg-6 col-xl-4 right pr-md-0">
                <section>
                    <h1 class="text-uppercase d-none d-md-block">
                        Spin and win
                        <br>
                        cash prizes
                    </h1>
                    <h1 class="text-uppercase d-block d-sm-none text-center text-md-left">Spin and win cash prizes</h1>
                    <p class="d-none d-lg-block">
                        <a href="account" class="text-underline text-light-gray">My Account</a> balance:
                        <br>
                        {$currency} {$balance}
                    </p>
                    
                    <div class="bg-blue spindemo d-flex flex-wrap gap-2 justify-content-center">
                        <h2 class="text-uppercase">Live Play</h2>
                        <p class="full-width mb-2">Your Balance: <strong>{$currency} {$balance}</strong></p>
                        
                        <div class="btn-group mb-3" role="group">
                            <button type="button" class="btn btn-outline-light active play-mode" data-mode="live">Live Play</button>
                            <button type="button" class="btn btn-outline-light play-mode" data-mode="demo">Demo Play</button>
                        </div>
                        
                        <p class="full-width"><br>CHOOSE YOUR STAKE AMOUNT</p>
                        
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
                        
                        <button class="btn btn-primary btn-lg spinBtn spinNow" disabled="disabled">Click to spin the wheel</button>
                        
                        <div class="insufficient-funds alert alert-warning mt-2" style="display:none">
                            Insufficient funds! <a href="#" data-toggle="modal" data-target="#depositModal">Add funds</a> to continue playing.
                        </div>
                        
                        <div class="mt-1 spinner-loader" style="display:none">
                            <img src="/spinner.gif" alt="Please wait">
                        </div>
                    </div>
                </section>
            </div>
            HTML;
        } else {
            return <<<HTML
            <div class="col-12 col-md-6 col-lg-6 col-xl-4 right pr-md-0">
                <section>
                    <h1 class="text-uppercase d-none d-md-block">
                        Spin and win
                        <br>
                        cash prizes
                    </h1>
                    <h1 class="text-uppercase d-block d-sm-none text-center text-md-left">Spin and win cash prizes</h1>
                    <p class="d-none d-lg-block">
                        <a href="#" data-toggle="modal" data-target="#registerModal" class="text-underline text-light-gray hide-element">Register now</a>
                        and get
                        <br>
                        free spins
                    </p>
                    
                    <div class="bg-blue spindemo d-flex flex-wrap gap-2 justify-content-center">
                        <h2 class="text-uppercase">Demo play</h2>
                        
                        <p class="full-width"><br>CHOOSE YOUR DEMO STAKE TO PLAY</p>
                        <h4 class="text-white invisible">
                            <strong>{$currency} 0</strong>
                            " &nbsp; "
                            <a href="#">
                                <i class="fa fa-minus-circle text-white"> == $0
                                    ::before
                                </i>
                            </a>
                        </h4>
                        
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
                        
                        <button class="btn btn-primary btn-lg spinBtn spinNow" disabled="disabled">Click to spin demo wheel</button>
                        
                        <p class="mt-3 mb-1 text-center small">
                            <a href="#" data-toggle="modal" data-target="#registerModal" class="btn btn-sm btn-light">
                                <i class="fa fa-user-plus mr-1"></i> Register to play for real cash
                            </a>
                        </p>
                        
                        <div class="mt-1 spinner-loader" style="display:none">
                            <img src="/spinner.gif" alt="Please wait">
                        </div>
                    </div>
                </section>
            </div>
            HTML;
        }
    }
}

// Instantiate the user account handler
$userAccount = new UserAccountHandler();

// Handle login if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $loginResult = $userAccount->processLogin($phone, $password);
    
    if ($loginResult['success']) {
        // Redirect to prevent form resubmission
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        $errorMessage = $loginResult['message'];
    }
}

// Output the complete header HTML
echo $userAccount->getHeaderHTML();

// Include modals for login, register, etc.
include_once 'includes/modals.php';

// Display error message if login failed
if (isset($errorMessage)) {
    echo "<script>alert('{$errorMessage}');</script>";
}

// Add scripts at the end to ensure modals work properly
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if we need to show a modal based on URL hash
    if(window.location.hash) {
        const modalId = window.location.hash.substring(1);
        const modalElement = document.getElementById(modalId);
        if(modalElement) {
            $('#' + modalId).modal('show');
        }
    }
    
    <?php if(isset($_SESSION['modal'])): ?>
    // Show modal from session if needed
    $('#<?php echo $_SESSION['modal']; ?>').modal('show');
    <?php unset($_SESSION['modal']); endif; ?>
});
</script>