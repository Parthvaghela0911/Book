<?php
include('config.php');
session_start();
date_default_timezone_set('Asia/Kathmandu');
?>
<style>
    .h-logo .logo-img {
    width: 240px;
    height: 150px; 
}
</style>

<!DOCTYPE HTML>
<html>
<head>
    <title>OMTBS</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/style.css" type="text/css" media="all" />
    <link rel="stylesheet" href="css/flexslider.css" type="text/css" media="all" />
    <link type="text/css" rel="stylesheet" href="http://www.dreamtemplate.com/dreamcodes/tabs/css/tsc_tabs.css" />
    <link rel="stylesheet" href="css/tsc_tabs.css" type="text/css" media="all" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src='js/jquery.color-RGBa-patch.js'></script>
    <script src='js/example.js'></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<div class="header">
    <div class="header-top">
        <div class="wrap">
            <div class="h-logo">
                <a href="index.php"><img src="images/logo_1" alt="Logo Image Here" class="logo-img"/></a>
            </div>
            <div class="nav-wrap">
                <ul class="group" id="example-one">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="movies_events.php">Movies</a></li>
                    <li>
                        <?php if(isset($_SESSION['user'])) {
                            $us = mysqli_query($con, "SELECT * FROM tbl_registration WHERE user_id='".$_SESSION['user']."'");
                            $user = mysqli_fetch_array($us);
                        ?>
                            <a href="profile.php" id="usernameLink"><?php echo $user['name']; ?></a>
                            <a href="logout.php">Logout</a>
                        <?php } else { ?>
                            <a href="login.php">Login</a>
                            <a href="registration.php">Register</a>
                        <?php } ?>
                    </li>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="clear"></div>
</div>

<div class="block">
    <div class="wrap">
        <?php 
        if (basename($_SERVER['PHP_SELF']) != 'process_booking.php') { 
        ?>
            <form action="process_search.php" id="reservation-form" method="post" onsubmit="return myFunction()">
                <fieldset>
                    <div class="field">
                        <input type="text" placeholder="Enter A Movie Name" style="height:30px;width:300px" required id="search111" name="search">
                        <input type="submit" value="Search" style="height:34px;padding-top:3px" id="button111">
                    </div>
                </fieldset>
            </form>
        <?php 
        }
        ?>
        <div class="clear"></div>
    </div>
</div>
<script>
function myFunction() {
    if($('#search111').val() == "") {
        alert("Please enter a movie name...");
        return false;
    } else {
        return true;
    }
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var usernameLink = document.getElementById('usernameLink');

    if (usernameLink) {
        usernameLink.addEventListener('click', function(event) {
            event.preventDefault();
            var links = document.querySelectorAll('.nav-wrap ul li a');
            links.forEach(function(link) {
                link.classList.remove('active');
            });
            this.classList.add('active');
        });
    }
});
</script>
<script>

document.addEventListener('DOMContentLoaded', function () {
    var usernameLink = document.getElementById('usernameLink');

    if (usernameLink) {
        usernameLink.addEventListener('click', function (event) {
            event.preventDefault();

            var isLoggedIn = <?php echo isset($_SESSION['user']) ? 'true' : 'false'; ?>;
            
            if (isLoggedIn) {
                window.location.href = 'profile.php';
            } else {
                console.log('User is not logged in.');e
                window.location.href = 'login.php';
            }
        });
    }
});
</script>
</body>
</html>
