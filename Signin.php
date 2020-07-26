<?php
session_start();
include_once('Elements\dbConnector.php');

//DEBUG: Write the Post array
//foreach ($_POST as $postElement){
//    echo ($postElement);
//    echo("<br>");
//}

function stringSantizer($var)
{
    $var = htmlentities($var);
    $var = strip_tags($var);
    return $var;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('Elements\Imports.html') ?>
    <meta charset="UTF-8">
    <title>Sceana | Regiser</title>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<?php include_once('Elements/getBackground.php') ?>
<div class="row" style="position: relative">
    <div class="wrapper" id="signinBlock">
        <img src="Assets/scaena_logo_transparent.png" alt="Scaena" width="200" height="120" class="center">
        <h2>Sign in</h2>
        <br>
        <form action="Signin.php" method="post">
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="reg_username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="reg_password" class="form-control" required>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>

            <p>Don't have an account? <a href="register.php">Register here</a>.</p>
        </form>
    </div>
</div>
</body>
</html>