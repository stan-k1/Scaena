<?php
session_start();
include_once('Elements\dbConnector.php');
$_SESSION['exists']=false;
$_SESSION['nomatch']=false;

if(isset($_POST["reg_username"])){
    $reg_username=$_POST['reg_username'];
    $check_querry_str="SELECT username from users WHERE username='$reg_username'";
    $check_querry=$conn->query($check_querry_str);
    if ($check_querry->num_rows > 0){
        $_SESSION['exists']=true;
    }
    elseif($_POST['reg_password']!=$_POST['reg_password_confirm']){
        $_SESSION['nomatch']=true;
    }
    else {
        $reg_stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, email, type) VALUES (?,?,?,?,?,?)");
        $reg_password = $_POST['reg_password'];
        $reg_first_name = stringSanitizer($_POST['reg_first_name']);
        $reg_last_name = stringSanitizer($_POST['reg_last_name']);
        $reg_email = stringSanitizer($_POST['reg_email']);
        $type = 'user';

        $reg_stmt->bind_param("ssssss", $reg_username, $reg_password, $reg_first_name, $reg_last_name, $reg_email, $type);
        $reg_stmt->execute();
        header('Location: Signin.php');
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('Elements\Imports.html') ?>
    <meta charset="UTF-8">
    <title>Register | Sceana</title>
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

    <?php
    if($_SESSION['exists']){echo("<p style='color: darkred; font-weight: bold'>This username already exits. Please try another.</p> <br>");
        $_SESSION['exists']=false;}
    elseif($_SESSION['nomatch']){echo("<p style='color: darkred; font-weight: bold'>Passwords do not match.</p> <br>");
        $_SESSION['nomatch']=false;}
    ?>

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