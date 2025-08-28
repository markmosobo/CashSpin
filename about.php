<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

    <title>About CashSpin Game - CashSpin</title>

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <meta name="description" content="CashSpin is a type of roulette spin game that involves spinning the wheel and winning." />

    <meta name="csrf-token" content="IQrPCDMtBlzB3zelLtMgNczlHBYu7j0tbdouRK5B" />

    <!-- Meta information - for search engines -->
    <meta property="og:title" content="About CashSpin Game - CashSpin" />
    <meta property="og:url" content="about.html" />
    <meta property="og:image" content="new-tpl/imgs/spinwheel.jpg" />

    <!-- External CSS -->
    <link href="new-tpl/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../use.fontawesome.com/releases/v5.6.3/css/all.css" />
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;900&amp;family=Roboto+Condensed:wght@300;700&amp;display=swap" rel="stylesheet" type="text/css" />

    <!-- Internal CSS -->
    <link href="new-tpl/css/spinner.css" rel="stylesheet" />
    <link href="new-tpl/css/styles.css" rel="stylesheet" />
    <link href="new-tpl/css/media700.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="css/custom.css" />

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

        <!-- Page Heading -->
        <section class="bg-blue text-white py-3 text-center page_heading">
            <div class="container h-100 page_heading_bg" style="background: url('new-tpl/imgs/about-title.svg');">
                <div class="row d-flex align-items-center justify-content-center h-100">
                    <div class="col-12 align-self-center">
                        <h1 class="title">About</h1>
                    </div>
                </div>
            </div>
        </section>
        <!-- //Page Heading -->

        <!-- Spacer -->
        <div class="container">
            <div class="row">
                <div class="col-md-12 mt-2"></div>
            </div>
        </div>

        <!-- About Section -->
        <section class="about pb-0">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h2 class="text-uppercase">Welcome to CashSpin - Spin and Win</h2>
                        <p>
                            CashSpin is a type of roulette game that involves spinning the wheel and winning. Players need to
                            register an account, buy credits, then choose what to stake to spin the wheel. When the pointer
                            lands on a multiplier, their stake is multiplied by that multiplier. For instance, if the pointer
                            lands on a x3 multiplier on a Ksh. 20 stake, the player earns Ksh 60.
                        </p>
                    </div>
                    <div class="col-12 col-md-6">
                        <img src="new-tpl/imgs/about/cover-photo.jpg" class="img-fluid d-block mx-auto" alt="About CashSpin luckywheel" />
                    </div>
                </div>
            </div>
        </section>
        <!-- //About Section -->

        <!-- Payment Section -->
        <section class="payment about pb-3">
            <div class="container-fluid px-0">
                <div class="row">
                    <div class="col-12 col-md-6 payment_bg">
                        <img src="new-tpl/imgs/payment.png" class="img-fluid d-none d-lg-block" alt="M-Pesa payment" />
                        <img src="new-tpl/imgs/payment-mobile.png" class="img-fluid d-block d-sm-none" alt="M-Pesa payment" />
                    </div>
                    <div class="d-flex col-12 col-md-6 payment_contents bg-blue h-auto justify-content-center">
                        <div class="align-self-center w-100">
                            <h4 class="text-uppercase mb-3">Register Now and <br />get free spins</h4>
                            <a class="btn btn-primary open-modal" href="#" data-target="#registerModal">Register now</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- //Payment Section -->

        <!-- More About Section -->
        <section class="about more pb-0">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <h2 class="text-uppercase">More about our game</h2>
                        <p>
                            Our game is enjoyable and engaging. Players can earn extra money while spinning the wheel. Here are some of our features.
                        </p>
                        <div class="row mt-0 mt-lg-5">
                            <div class="col-12 col-md-4 tiny_details">
                                <img src="new-tpl/imgs/about/child.png" class="img-fluid h-25 w-25" alt="Easy to play game" />
                                <p class="name">Easy to play game</p>
                                <p>CashSpin spin the wheel game is the simplest and most straightforward game to play.</p>
                            </div>
                            <div class="col-12 col-md-4 tiny_details">
                                <img src="new-tpl/imgs/about/steps.png" class="img-fluid h-25 w-25" alt="Levels for more cash" />
                                <p class="name">Levels for more cash</p>
                                <p>Keep winning regardless of your spin result. Get rewarded every time you reach a new level.</p>
                            </div>
                            <div class="col-12 col-md-4 tiny_details">
                                <img src="new-tpl/imgs/about/spin.png" class="img-fluid h-25 w-25" alt="CashSpin Free spins" />
                                <p class="name">Free spins for real cash</p>
                                <p>We give out free spins every now and then. Players use these free spins to win real cash.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <img src="new-tpl/imgs/dsws%402x.png" class="img-fluid d-block mx-auto" alt="Spin to win" />
                    </div>
                </div>
            </div>
        </section>
        <!-- //More About Section -->
    </div>
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="new-tpl/js/jquery.min.js"></script>
    <script src="new-tpl/js/popper.min.js"></script>
    <script src="new-tpl/js/bootstrap.min.js"></script>
    <script src="../unpkg.com/sweetalert%402.1.2/dist/sweetalert.min.js"></script>
    <script src="js/generalfa8f.js?id=28686adac2ea735c2aa2"></script>
</body>

</html>