<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />

    <title>Contact Us - CashSpin</title>

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    <meta name="description" content="Contact Us. Experiencing issues with our website or need any assistance? Fill in this form and we will get in touch with you." />

    <meta name="csrf-token" content="IQrPCDMtBlzB3zelLtMgNczlHBYu7j0tbdouRK5B" />

    <!-- Meta information - for search engines -->
    <meta property="og:title" content="Contact Us - CashSpin" />
    <meta property="og:url" content="contact.html" />
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
        <!-- //Header -->

        <!-- Page Heading -->
        <section class="bg-blue text-white py-3 text-center page_heading">
            <div class="container h-100 page_heading_bg" style="background: url('new-tpl/imgs/contact-title.svg');">
                <div class="row d-flex align-items-center justify-content-center h-100">
                    <div class="col-12 align-self-center">
                        <h1 class="title">Contact Us</h1>
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

        <!-- Contact Section -->
        <section class="contact">
            <div class="container">
                <div class="row">
                    <!-- Contact Information -->
                    <div class="col-12 col-md-8">
                        <h2 class="text-uppercase" style="color: white;">We'd love to hear from you</h2>
                        <p style="color:ghostwhite">Experiencing issues with our website or need any assistance? Fill in this form and we will get in touch with you.</p>
                        <ul class="list-unstyled">
                            <li>
                                <i class="fa fa-phone mr-4 text-blue"></i>
                                <a href="tel:+254782145121" class="text-black">+254 782 145 121</a>
                            </li>
                            <li>
                                <i class="fa fa-envelope mr-4 text-blue"></i>
                                <a href="cdn-cgi/l/email-protection.html#1a727f7676755a707f747d7b797b697234797577" class="text-black">
                                    <span class="__cf_email__" data-cfemail="2b434e4747446b414e454c4a484a584305484446">[email&#160;protected]</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact Form -->
                    <div class="col-12 col-md-4">
                        <form wire:id="oqJppPoJ0d5PwWIyVRwR" wire:initial-data="{&quot;fingerprint&quot;:{&quot;id&quot;:&quot;oqJppPoJ0d5PwWIyVRwR&quot;,&quot;name&quot;:&quot;contact-form&quot;,&quot;locale&quot;:&quot;en&quot;,&quot;path&quot;:&quot;contact&quot;,&quot;method&quot;:&quot;GET&quot;},&quot;effects&quot;:{&quot;listeners&quot;:[]},&quot;serverMemo&quot;:{&quot;children&quot;:[],&quot;errors&quot;:[],&quot;htmlHash&quot;:&quot;bcfba7cd&quot;,&quot;data&quot;:{&quot;name&quot;:null,&quot;phone&quot;:null,&quot;email&quot;:null,&quot;subject&quot;:null,&quot;message&quot;:null,&quot;sent&quot;:false},&quot;dataMeta&quot;:[],&quot;checksum&quot;:&quot;3e7a297629d0eacbb2b7438d697309c8311332ed8c226992c4b4a4348952d68d&quot;}}" wire:submit.prevent="submit" x-cloak>
                            <div class="form-group">
                                <input type="text" class="form-control" wire:model.defer="name" placeholder="Name" maxlength="100" required />
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" wire:model.defer="phone" placeholder="Your phone number" required />
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" wire:model.defer="email" placeholder="Your Email" required />
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" wire:model.defer="subject" placeholder="Subject" maxlength="100" required />
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" wire:model.defer="message" placeholder="Write something..." rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block btn-lg">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- //Contact Section -->

        <!-- Footer -->
        <?php include 'includes/footer.php'; ?>
        <!-- //Footer -->
    </div>

    <!-- Scripts -->
    <script data-cfasync="false" src="cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script src="new-tpl/js/jquery.min.js"></script>
    <script src="new-tpl/js/popper.min.js"></script>
    <script src="new-tpl/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>    
<script src="js/generalfa8f.js?id=28686adac2ea735c2aa2"></script>

    <!-- Livewire Scripts -->
    <script src="livewire/livewire9bad.js?id=e6704f81026a73a52725" data-turbo-eval="false" data-turbolinks-eval="false"></script>
    <script>
        window.livewire = new Livewire();
        window.Livewire = window.livewire;
        window.livewire_app_url = '';
        window.livewire_token = 'IQrPCDMtBlzB3zelLtMgNczlHBYu7j0tbdouRK5B';
        window.deferLoadingAlpine = function (callback) {
            window.addEventListener('livewire:load', function () {
                callback();
            });
        };
        let started = false;
        window.addEventListener('alpine:initializing', () => {
            if (!started) {
                window.livewire.start();
                started = true;
            }
        });
        document.addEventListener("DOMContentLoaded", function () {
            if (!started) {
                window.livewire.start();
                started = true;
            }
        });
    </script>
    <script src="../cdn.jsdelivr.net/gh/alpinejs/alpine%40v2.7.0/dist/alpine.min.js" defer></script>

    <!-- Tawk.to Script -->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
        (function () {
            var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/5f64c8d04704467e89f02fb6/default';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!-- //Tawk.to Script -->
</body>

</html>