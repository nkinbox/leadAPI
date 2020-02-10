<!-- <?php 
require_once("config.php");
session_start();
if(!isset($_SESSION["user_email"])) {?>
<script>location.href="index.php";</script>
<?php exit; }?> -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accounts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .footer {
            position: fixed;
            bottom: 0;
        }
    </style>

</head>
<body>
    <div class="d-none d-print-block pb-2">
        <img height="50" src="https://www.tripclues.in/images/tripclues-logo.jpg">
    </div>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <router-link to="/" class="navbar-brand" href="#"><img height="50" src="https://www.tripclues.in/images/tripclues-logo.jpg"></router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/ledger/sales">Sales Ledger</router-link>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" to="/ledger/purchases">Purchase Ledger</router-link>
                    </li>
                </ul>
            </div>
        </nav>
        <toast></toast>
        <div class="m-2 p-2 border">
            <router-view></router-view>
        </div>
    </div>
    <div class="footer d-none d-print-block w-100">
        <div class="text-center my-2 pb-2 border-bottom border-danger font-weight-bold text-danger">
            Wishing You A Happy Journey with TripClues.com
        </div>
        <div class="text-center">
            <div>Noida Office: H 92 Block, 2nd Floor, Sector 63, Noida, Uttar Pradesh 201301</div>
            <div>Contact Number: +91.120.4561118, 7900206206 Email: <a href="#">info@tripclues.com</a> Web: <a href="#">www.tripclues.com</a></div>
        </div>
    </div>
    <script src="accounts.js"></script>
</body>
</html>