<?php
session_start();
include_once('Elements/dbConnector.php');
$_SESSION['signin_failed']=false;

if(isset($_POST['username'])){
$username=stringSanitizer($_POST['username']);
$check_querry_str="SELECT username, password from users WHERE username='$username'";
$check_querry=$conn->query($check_querry_str);
if ($check_querry->num_rows > 0){
    $signin_password=$_POST['signin_password'];
    $user_details=$check_querry -> fetch_row();
    if($signin_password==$user_details[1]){
        $_SESSION['username']=$username;
        if($_SESSION['signin_failed']) {$_SESSION['signin_failed']=false;}
        header('Location: Home.php');
    }
    else {$_SESSION['signin_failed']=true;}
}
else {$_SESSION['signin_failed']=true;}
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('Elements/Imports.html') ?>
    <meta charset="UTF-8">
    <title>Sign in | Sceana</title>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
         body{
             background-image: url('Assets/Backgrounds/background19.jpg');
         }
    </style>
</head>
<body>
<?php include_once('Elements/getBackground.php') ?>
<div class="row" style="position: relative">
    <div class="wrapper" id="signinBlock">
        <img src="Assets/scaena_logo_transparent.png" alt="Scaena" width="200" height="120" class="center">
        <h2>Sign in</h2>
        <br>
        <?php if($_SESSION['signin_failed']){echo("<p style='color: darkred; font-weight: bold'>Incorrect username or password. Please try again. </p> <br>");}?>
        <form action="Signin.php" method="post">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="signin_password" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>

            <p>Don't have an account? <a href="Register.php">Register here</a>.</p>
        </form>
    </div>
</div>
</body>
</html>