<?php
require_once 'db_connect.php';
session_start();

class CookieConsent {
    private $conn;
    private $sessionId;
    private $userId;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->sessionId = session_id();
        $this->userId = $_SESSION['user_id'] ?? null;
    }
    
    public function handleConsent() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cookie_consent'])) {
            $consentType = $_POST['consent_type'] ?? 'basic';
            $this->saveConsent(true, $consentType);
            $this->setConsentCookies($consentType);
            return true;
        }
        
        return false;
    }
    
    public function hasConsent() {
        // Check cookie first
        if (isset($_COOKIE['cookie_consent']) && $_COOKIE['cookie_consent'] === 'accepted') {
            return true;
        }
        
        // Check database
        $stmt = $this->conn->prepare("SELECT cookie_consent FROM user_browser_data 
                                     WHERE session_id = ? OR user_id = ? 
                                     ORDER BY updated_at DESC LIMIT 1");
        $stmt->execute([$this->sessionId, $this->userId]);
        $result = $stmt->fetch();
        
        return $result && $result['cookie_consent'];
    }
    
    private function saveConsent($granted, $consentType) {
        $browserData = [
            'session_id' => $this->sessionId,
            'user_id' => $this->userId,
            'cookie_consent' => $granted ? 1 : 0,
            'consent_timestamp' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'last_activity' => date('Y-m-d H:i:s')
        ];
        
        $sql = "INSERT INTO user_browser_data 
                (session_id, user_id, cookie_consent, consent_timestamp, ip_address, user_agent, last_activity) 
                VALUES (:session_id, :user_id, :cookie_consent, :consent_timestamp, :ip_address, :user_agent, :last_activity)
                ON DUPLICATE KEY UPDATE 
                cookie_consent = VALUES(cookie_consent),
                consent_timestamp = VALUES(consent_timestamp),
                last_activity = VALUES(last_activity)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($browserData);
        
        // Log consent action
        $logSql = "INSERT INTO user_consent_logs 
                  (user_id, session_id, consent_type, action, ip_address, user_agent)
                  VALUES (?, ?, ?, ?, ?, ?)";
        $this->conn->prepare($logSql)->execute([
            $this->userId,
            $this->sessionId,
            'cookies',
            $granted ? 'granted' : 'revoked',
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT']
        ]);
    }
    
    private function setConsentCookies($consentType) {
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days
        setcookie('cookie_consent', 'accepted', $expiry, '/');
        
        if ($consentType === 'advanced') {
            setcookie('analytics_consent', 'accepted', $expiry, '/');
            setcookie('marketing_consent', 'accepted', $expiry, '/');
        }
    }
    
    public function getConsentModal() {
        if ($this->hasConsent()) {
            return '';
        }
        
        return '
        <div id="cookieConsentModal" class="cookie-consent-modal">
            <div class="cookie-consent-content">
                <h3>We Value Your Privacy</h3>
                <p>We use cookies to enhance your experience, analyze traffic, and for marketing purposes. 
                   By clicking "Accept All", you consent to our use of cookies.</p>
                
                <div class="cookie-consent-options">
                    <button type="button" class="btn-consent" data-consent="basic">Necessary Only</button>
                    <button type="button" class="btn-consent" data-consent="advanced">Accept All</button>
                </div>
                
                <a href="/privacy-policy" class="privacy-link">Learn More</a>
            </div>
        </div>
        
        <style>
            .cookie-consent-modal {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: #2c3e50;
                color: white;
                padding: 20px;
                z-index: 9999;
                box-shadow: 0 -2px 10px rgba(0,0,0,0.2);
            }
            .cookie-consent-content {
                max-width: 1200px;
                margin: 0 auto;
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: space-between;
            }
            .cookie-consent-content h3 {
                margin: 0 0 10px 0;
                width: 100%;
            }
            .cookie-consent-content p {
                margin: 0 20px 0 0;
                flex: 1;
            }
            .cookie-consent-options {
                display: flex;
                gap: 10px;
            }
            .btn-consent {
                padding: 8px 16px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
            }
            .privacy-link {
                color: #3498db;
                text-decoration: none;
                margin-left: 20px;
            }
        </style>
        
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = document.getElementById("cookieConsentModal");
            const buttons = document.querySelectorAll(".btn-consent");
            
            buttons.forEach(button => {
                button.addEventListener("click", function() {
                    const consentType = this.dataset.consent;
                    
                    fetch("handle-consent", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: "cookie_consent=true&consent_type=" + consentType
                    }).then(() => {
                        modal.style.display = "none";
                        location.reload();
                    });
                });
            });
            
            // Only show if no consent cookie
            if (document.cookie.indexOf("cookie_consent=") === -1) {
                modal.style.display = "block";
            }
        });
        </script>';
    }
}

// Initialize and handle consent
$cookieConsent = new CookieConsent($conn);
$consentGiven = $cookieConsent->handleConsent();

// For AJAX handling
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode(['success' => $consentGiven]);
    exit;
}
?>