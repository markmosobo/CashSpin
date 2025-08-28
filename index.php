<?php session_start(); 
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

    <title>Spin and Win in Kenya - Spin the Wheel, Earn Real Cash - CashSpin</title>

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <meta name="description" content="Spin and Win in Kenya. Free spins on registration, no deposit required. Choose your stake and Spin to Win cash instantly. Join Now!" />

    <meta name="csrf-token" content="IQrPCDMtBlzB3zelLtMgNczlHBYu7j0tbdouRK5B" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="theme-color" content="#000000" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="CashSpin" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="msapplication-TileColor" content="#000000" />
    <meta name="msapplication-TileImage" content="new-tpl/imgs/favicon.png" />
    <meta name="msapplication-config" content="new-tpl/imgs/browserconfig.xml" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />


    <!-- Meta information - for search engines -->
    <meta property="og:title" content="Spin and Win in Kenya - Spin the Wheel, Earn Real Cash - CashSpin" />
    <meta property="og:url" content="index.html" />
    <meta property="og:image" content="new-tpl/imgs/spinwheel.jpg" />

    <!-- External CSS -->
    <link href="new-tpl/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../use.fontawesome.com/releases/v5.6.3/css/all.css" />
    <link href="new-tpl/css/fontawesome.min.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;900&amp;family=Roboto+Condensed:wght@300;700&amp;display=swap" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Internal CSS -->
    <link href="new-tpl/css/styles.css" rel="stylesheet" />
    <link href="new-tpl/css/spinnerdemo.css" rel="stylesheet" />
    <link rel="stylesheet" href="new-tpl/css/media700.css">
    <link rel="stylesheet" type="text/css" href="css/custom.css">


    <!-- Scripts -->
    <script src="new-tpl/js/jquery.min.js"></script>
    <script src="new-tpl/js/popper.min.js"></script>
    <script src="new-tpl/js/bootstrap.min.js"></script>
    
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>    
<script src="js/generalfa8f.js?id=28686adac2ea735c2aa2"></script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-54021233-4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', 'UA-54021233-4');
    </script>
</head>

    <body class="">
    <div id="app">
        <!-- Header -->
        <?php include 'includes/header.php'; ?>
                <!-- //Header -->
            <!-- Vue Spinner Component -->
    <!-- Load the Vue component -->
        <script src="spinner.js"></script>    
                   <!-- Credits -->
        <section class="bg-blue text-white py-3 text-center credits">
            <div class="container-fluid">
                <div class="container">
                    <p class="mb-0">Welcome to CashSpin spin game. Spin the lucky wheel and win real money.</p>
                </div>
            </div>
        </section>
        <!-- //Credits -->

        <?php if (isset($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($_SESSION['errors'] as $error): ?>
                        <li><?= htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <!-- Facilities -->
        <section class="text-center py-3 facilities">
            <div class="container-fluid">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h3 class="mb-5">Facilities</h3>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="facility">
                                <div class="icon mx-auto">
                                    <img src="new-tpl/imgs/coins.svg" alt="CashSpin free spins" />
                                </div>
                                <div class="contents">Free spins on registration, no deposit required</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="facility">
                                <div class="icon mx-auto">
                                    <img src="new-tpl/imgs/ios.svg" alt="CashSpin withdrawals" />
                                </div>
                                <div class="contents">Instant withdrawals. Sell your credits for instant cash.</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="facility">
                                <div class="icon mx-auto">
                                    <img src="new-tpl/imgs/bars.png" alt="CashSpin stakes" />
                                </div>
                                <div class="contents">Minimum stake of Ksh. 20 and a high multiplier of X1000!</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="facility">
                                <div class="icon mx-auto">
                                    <img src="new-tpl/imgs/levels-facility.png" alt="CashSpin levels" />
                                </div>
                                <div class="contents">New levels equals more cash and free spins.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //Facilities -->

        <!-- Payment -->
        <section class="payment">
            <div class="container-fluid px-0">
                <div class="row">
                    <div class="col-12 col-md-6 payment_bg">
                        <img src="new-tpl/imgs/payment.png" class="img-fluid d-none d-lg-block" />
                        <img src="new-tpl/imgs/payment-mobile.png" class="img-fluid d-block d-sm-none" />
                    </div>
                    <div class="d-flex col-12 col-md-6 payment_contents bg-blue h-auto justify-content-center">
                        <div class="align-self-center w-100">
                            <h4 class="text-uppercase mb-0 d-none d-lg-block">Get payment in the <br />most convenient way</h4>
                            <h4 class="text-uppercase mb-0 d-block d-sm-none">Get payment in the most convenient way</h4>
                            <img src="new-tpl/imgs/pesa.png" class="img-fluid" /><br>
                            <a class="btn btn-primary open-modal" href="#" data-target="#registerModal">Register now</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //Payment -->

        <!-- Reviews -->
        <section class="reviews">
            <div class="container-fluid px-0">
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-5 pb-3">User Reviews</h5>
                    </div>
                    <div class="col-12 col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                        <div class="row">
                            <div class="col-12 col-md-6 text-center">
                                <div class="review">
                                    <div class="icon mx-auto">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <p>CashSpin is the best game to play and earn money. Instant withdrawals are great.</p>
                                    <p class="name"><span class="grey">- Kelvo,</span></p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 text-center">
                                <div class="review">
                                    <div class="icon mx-auto">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <p>CashSpin is a great app to win real money. I won 100 times my Ksh. 20 stake. It's a fun way to earn cash.</p>
                                    <p class="name"><span class="light-gray">- Oti</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //Reviews -->

        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
        <!-- //Footer -->

        <!-- Modals -->
        <?php include 'includes/modals.php'; ?>

        <!-- cookie consent -->
        <?php include 'includes/cookie_consent.php'; ?>
    </div>

    <!-- Scripts -->
    
            <?php if (isset($_SESSION['modal'])): ?>
        <script>
            $(document).ready(function() {
                $('#<?php echo $_SESSION['modal']; ?>').modal('show');
                <?php unset($_SESSION['modal']); ?>
            });
        </script>
        <?php endif; ?>
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    const containerElement = document.querySelector('.facilities .container');
    const rowElement = containerElement.querySelector('.row');

    if (rowElement && window.matchMedia('(max-width: 600px)').matches) {
        const fragment = document.createDocumentFragment();
        Array.from(rowElement.children).forEach(child => {
            fragment.appendChild(child);
        });
        containerElement.appendChild(fragment); // Append children directly to the container
        rowElement.remove(); // Remove the .row div
    }
});
    </script>
    <?php if (isset($_SESSION['depositModal'])): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        $("#<?php echo $_SESSION['depositModal']; ?>").modal("show");
    });
    </script>
    <?php unset($_SESSION['depositModal']); endif; ?>

    <script src="new-tpl/js/spinner2js.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/Draggable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ThrowPropsPlugin.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <script src="new-tpl/js/Spin2WinWheel.js"></script>
    <script src="new-tpl/js/spinner2js.js"></script>
    </body>

</html>