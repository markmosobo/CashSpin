
<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-blue border-0 rounded-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <div class="heading">Login Now</div>
                <form method="POST" action="login_action.php">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="modal" value="loginModal">
                    <?php if (!empty($_SESSION['errors']) && isset($_SESSION['modal']) && $_SESSION['modal'] === 'loginModal'): ?>
                        <div class="alert alert-danger">
                            <?php 
                                foreach ($_SESSION['errors'] as $error) {
                                    echo "<div>$error</div>";
                                }
                                unset($_SESSION['errors']); 
                                unset($_SESSION['modal']);
                            ?>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <input type="text" class="form-control" name="phone" placeholder="Your Phone Number e.g. 0722******" value="" required />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Password" minlength="8" autocomplete="current-password" required />
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1" />
                        <label class="form-check-label w-100 text-white" for="remember">
                            Remember me
                            <span class="float-right">
                                <a href="#" class="text-white text-underline open-modal" data-target="#forgotPasswordModal">Forgot password?</a>
                            </span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg my-4">Login now</button>

                    <p class="text-center">Don’t have an account?
                        <a href="#" class="text-white text-underline open-modal" data-target="#registerModal">Create one here</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-blue border-0 rounded-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <div class="heading">Reset Password</div>
                <form method="POST" action="cashspin/forgot-password">
                    <input type="hidden" name="_token" value="IQrPCDMtBlzB3zelLtMgNczlHBYu7j0tbdouRK5B">
                    <input type="hidden" name="modal" value="forgotPasswordModal">

                    <div class="text-center mb-4">
                        Enter your phone number and we will send you a reset code.
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="phone" placeholder="Enter phone no. e.g. 0722******" value="" required />
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg my-4" id="send_code">Send Reset Code</button>

                    <p class="text-center d-flex justify-content-between">
                        <a href="#" class="text-white text-underline open-modal" data-target="#newPasswordModal">Already have a code?</a>
                        <a href="#" class="text-white text-underline open-modal" data-target="#registerModal">Register New Account</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- New Password Modal -->
<div class="modal fade" id="newPasswordModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-blue border-0 rounded-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <div class="heading">New Password</div>
                <form method="POST" action="cashspin/new-password">
                    <input type="hidden" name="_token" value="IQrPCDMtBlzB3zelLtMgNczlHBYu7j0tbdouRK5B">
                    <input type="hidden" name="modal" value="newPasswordModal">

                    <div class="text-center mb-4">
                        Enter the password reset code sent to your phone and a new password for your account.
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="code" placeholder="Enter code" value="" maxlength="6" required />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="New Password" minlength="8" autocomplete="new-password" required />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm New Password" minlength="8" autocomplete="new-password" required />
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg my-4">Submit</button>

                    <p class="text-center d-flex justify-content-between">
                        <a href="#" class="text-white text-underline open-modal" data-target="#loginModal">Proceed to login</a>
                        <a href="#" class="text-white text-underline open-modal" data-target="#registerModal">Register new account</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-blue border-0 rounded-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <div class="heading">Register Now</div>

                <!-- Show error message -->
                <?php if (isset($_SESSION['errors'])): ?>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <!-- Show success message -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <form method="POST" action="register_action.php">
                    <input type="hidden" name="_token" value="<?php echo isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : ''; ?>">
                    <input type="hidden" name="referral_code" value="">
                    
                    <div class="form-group">
                        <input type="text" class="form-control" name="phone" placeholder="Your Phone Number e.g. 0722******" value="<?php echo $_SESSION['old_inputs']['phone'] ?? ''; ?>" required />
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="user_name" placeholder="Username (Optional)" value="<?php echo $_SESSION['old_inputs']['user_name'] ?? ''; ?>" minlength="3" maxlength="50" />
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" placeholder="Email (Optional)" value="<?php echo $_SESSION['old_inputs']['email'] ?? ''; ?>" maxlength="100" />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Password" minlength="8" maxlength="50" required />
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" minlength="8" maxlength="50" required />
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" value="1" required />
                        <label class="form-check-label w-100 text-white" for="terms">
                            I have read and agreed to <a href="terms.html" class="text-white text-underline">terms of use</a>
                            and <a href="privacy.html" class="text-white text-underline">privacy policy</a>.
                        </label>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="responsibility" name="responsibility" value="1" required />
                        <label class="form-check-label w-100 text-white" for="responsibility">
                            I confirm I will play responsibly.
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg my-4">Register Now</button>

                    <p class="text-center">
                        Already have an account?
                        <a href="#" class="text-white text-underline open-modal" data-target="#loginModal">Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Registration Verification Modal -->
<div class="modal fade" id="regVerifyModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-blue border-0 rounded-0">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="modal-body">
                <div class="heading">Enter Registration Code</div>
                
                <form method="POST" action="verify.php">
                    <input type="hidden" name="_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="modal" value="regVerifyModal">
                    <input type="hidden" name="phone" value="<?php echo $_SESSION['phone'] ?? ''; ?>">
                    <input type="hidden" name="username" value="<?php echo $_SESSION['username'] ?? ''; ?>">
                    <input type="hidden" name="email" value="<?php echo $_SESSION['email'] ?? ''; ?>">

                    <div class="text-center mb-4">
                        <!-- Show success message -->
                        <?php if (isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                        <?php endif; ?>
                        It might take a couple of minutes for the code to be delivered to your phone.
                    </div>

                    <div class="form-group">
                        <input type="text" class="form-control" name="code" placeholder="E.g. 000000" value="" maxlength="6" required />
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg my-4">Verify Code</button>

                    <p class="text-center">
                        <a id="resendOtp" href="#" class="text-white">Resend Code</a>
                    </p>

                    <div class="mt-2" id="reg_result"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Deposit Modal -->
<div class="modal fade" id="depositModal" tabindex="-1" role="dialog" aria-labelledby="depositModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
    <div class="modal-content shadow-lg border-0">
      
      <!-- Header -->
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title mb-0" id="depositModalLabel">
          <i class="fa fa-money-bill-wave mr-2"></i> Deposit Funds
        </h5>
        <button type="button" class="btn text-white ml-auto p-0" data-dismiss="modal" aria-label="Close" style="font-size:1.3rem;">
        <i class="fas fa-times"></i>
        </button>

      </div>

      <!-- Body -->
      <div class="modal-body">
        <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger py-2">
            <?php foreach ($_SESSION['errors'] as $error): ?>
            <div><?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <form id="depositForm" method="POST" action="deposit_action.php" novalidate>
          <div class="form-group">
            <label for="amount" class="font-weight-bold">Amount (KES)</label>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" min="10" step="1" required>
            <small class="form-text text-muted">Minimum deposit: KES 10</small>
          </div>
          <small class="text-muted d-block mt-2">
            The STK push will be sent to your registered phone number.
          </small>
        </form>
      </div>

      <!-- Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times mr-1"></i> Cancel
        </button>
        <button type="submit" form="depositForm" class="btn btn-success">
          <i class="fa fa-mobile-alt mr-1"></i> Send STK Push
        </button>
      </div>

    </div>
  </div>
</div>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        const resendOtpLink = document.getElementById('resendOtp');
        if (resendOtpLink) {
            resendOtpLink.addEventListener('click', function(e) {
                e.preventDefault();
                if (!confirm('Request a new verification code?')) return;

                const phoneInput = document.querySelector('input[name="phone"]');
                if (!phoneInput || !phoneInput.value) {
                    alert('Error: Phone number not found. Please try registering again.');
                    return;
                }

                const phone = phoneInput.value;
                resendOtpLink.innerText = 'Sending...';
                resendOtpLink.style.pointerEvents = 'none';

                fetch('resend_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'phone=' + encodeURIComponent(phone)
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.success ? 'Verification code resent successfully!' : 'Error: ' + (data.message || 'Failed to resend verification code.'));
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                })
                .finally(() => {
                    setTimeout(() => {
                        resendOtpLink.innerText = 'Resend Code';
                        resendOtpLink.style.pointerEvents = 'auto';
                    }, 30000);
                });
            });
        }
    });
    
    $(document).ready(function() {
        // Handle modal transitions when clicking links with .open-modal class
        $(document).on('click', '.open-modal', function(e) {
            e.preventDefault();
            
            var targetModal = $(this).data('target');
            var currentModal = $('.modal.show');
            
            if (currentModal.length) {
                // Hide current modal and show new one after it's hidden
                currentModal.one('hidden.bs.modal', function() {
                    $(targetModal).modal('show');
                });
                currentModal.modal('hide');
            } else {
                // If no modal is open, just show the target
                $(targetModal).modal('show');
            }
        });

        // When verification is successful, close regVerifyModal and open loginModal
        $(document).on('registrationSuccess', function() {
            $('#regVerifyModal').modal('hide');
            $('#loginModal').modal('show');
        });
    });

    // document.addEventListener("DOMContentLoaded", function() {
    //     $('#loginModal').modal('show');
    // });
    
</script>
