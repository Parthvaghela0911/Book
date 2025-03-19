<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php
session_start();
if(!isset($_SESSION['user']))
{
	header('location:login.php');
}
include('config.php');
extract($_POST);

//OTP Code
if (isset($otp) && $otp) { 
    $bookid = "BKID" . rand(1000000, 9999999);

    // Ensure $_SESSION['seats'] is processed correctly
    if (isset($_SESSION['seats']) && !empty($_SESSION['seats'])) {
        $seats = $_SESSION['seats'];
        $seatArray = explode(',', $seats);
        $no_seats = count($seatArray); 
    } else {
        $seats = ''; 
        $no_seats = 0;
    }

    $query = "INSERT INTO tbl_bookings (ticket_id, t_id, user_id, show_id, screen_id, no_seats, amount, ticket_date, date, status, seat_numbers) 
              VALUES ('$bookid', '" . $_SESSION['theatre'] . "', '" . $_SESSION['user'] . "', '" . $_SESSION['show'] . "', '" . $_SESSION['screen'] . "', '$no_seats', '" . $_SESSION['amount'] . "', '" . $_SESSION['date'] . "', CURDATE(), '1', '$seats')";

    mysqli_query($con, $query) or die(mysqli_error($con));
    $_SESSION['success'] = "Bookings Done!";
} else {
    $_SESSION['error'] = "Payment Failed: Invalid OTP.";
}
?>
<body><table align='center'><tr><td><STRONG>Transaction is being processed,</STRONG></td></tr><tr><td><font color='blue'>Please Wait <i class="fa fa-spinner fa-pulse fa-fw"></i>
<span class="sr-only"></font></td></tr><tr><td>(Do not 'RELOAD' this page or 'CLOSE' this page)</td></tr></table><h2>
<script>
    setTimeout(function(){ window.location="profile.php"; }, 3000);
</script>