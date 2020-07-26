<?php
session_start();
include_once('Elements\dbConnector.php');

//DEBUG: Write the Post array
//foreach ($_POST as $postElement){
//    echo ($postElement);
//    echo("<br>");
//}

if(isset($_POST["reg_username"]) && $_POST["reg_password"]==$_POST["reg_password_confirm"]){
    $reg_username=$_POST['reg_username'];
    $check_querry_str="SELECT username from users WHERE username='$reg_username'";
    $check_querry=$conn->query($check_querry_str);
    if ($check_querry->num_rows > 0){
        echo("username already exists!!!");
    }
    else {
        $reg_stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, email, type) VALUES (?,?,?,?,?,?)");

        $reg_password = stringSantizer($_POST['reg_password']);
        $reg_password = password_hash($reg_password,PASSWORD_DEFAULT);
        $reg_first_name = stringSantizer($_POST['reg_first_name']);
        $reg_last_name = stringSantizer($_POST['reg_last_name']);
        $reg_email = stringSantizer($_POST['reg_email']);
        $type = 'user';

        $reg_stmt->bind_param("ssssss", $reg_username, $reg_password, $reg_first_name, $reg_last_name, $reg_email, $type);
        $reg_stmt->execute();
        header('Location: Signin.php');
    }
}

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
<div class="wrapper" id="registerBlock">
    <img src="Assets/scaena_logo_transparent.png" alt="Scaena" width="200" height="120" class="center">
    <h2>Register</h2>
    <p>Create a new account.</p>
    <br>
    <form action="Register.php" method="post">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="reg_username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="reg_password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Confirm Password:</label>
            <input type="password" name="reg_password_confirm" class="form-control" required>
        </div>
        <div class="form-group">
            <label>First Name:</label>
            <input type="text" name="reg_first_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Last Name:</label>
            <input type="text" name="reg_last_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="reg_email" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>

        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </form>
</div>
</div>
</body>
</html>