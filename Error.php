<?php
session_start();
if(!empty($_SESSION['cust_error_msg'])){
    $msg=$_SESSION['cust_error_msg'];
};
?>

<!DOCTYPE HTML>
<HTML lang="en">
<head>
    <?php include('Elements/Imports.html') ?>
    <title>Error</title>
    <style>
        body{
            background-image: url('Assets/scaena_background.png');
        }
        .wrapper{
            width: 350px; padding: 20px;
        }
    </style>
</head>

<body>
<div class="row" style="position: relative">
    <div class="wrapper" id="errorBlock">
        <img src="Assets/baseline_error_black_48dp.png" id="errorIcon">
        <br>
       <h4>An error has occured.</h4>
        <br>
        <?php
        if(!empty($msg)){echo("<p> $msg </p>");}
        else {echo("<p> An unknown issue was encountered. </p>");}
        ?>
        <br>
        <p>Please try again later. If this problem persists, contact your administrator.</p>
    </div>
</div>
</body>
</HTML>
