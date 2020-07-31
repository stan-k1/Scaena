<?php
include_once('Elements\dbConnector.php');

if(isset($_POST["reset_username"])){
$reset_username=stringSantizer($_POST['reg_username']);
$reset_new_password=stringSantizer($_POST['reset_password']);

$check_querry_str="SELECT username, pw_reset, reset_token from users WHERE username='$reset_username'";
$check_querry=$conn->query($check_querry_str);
if ($check_querry->num_rows > 0 ){
    $server_reset_active=$check_querry['pw_reset'];
    $server_reset_token=$check_querry['reset_token'];
    if ($server_reset_active && $server_reset_token){}
}
else {$_SESSION['cust_error_msg'] = "An error occured while attempting to reset your password. Please try again or contact your administrator.";
    header('Location: Error.php');}
}
?>

<head>
    <?php include('Elements\Imports.html') ?>
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
        <h1>Reset your password.</h1>
        <h6>If you have requested a password reset from your administrator and received a token, you can create a new password here.
            If you do not have a reset token, contact your administrator for assitance.</h6>
        <form action="PW_Reset.php" method="post">
            <div class="form-group">
                <label>Enter your username:</label>
                <input type="text" name="reset_username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Select a new password:</label>
                <input type="password" name="reset_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Enter your reset token:</label>
                <input type="password" name="reset_token" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>

    </div>
</div>
</body>

