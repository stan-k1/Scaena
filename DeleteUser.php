<?php
session_start();
include_once('Elements/dbConnector.php');

//Access Control
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $check_querry_str = "SELECT type from users WHERE username='$username'";
    $check_querry = $conn->query($check_querry_str);
    $check_querry_output = $check_querry->fetch_assoc();
    $user_type = $check_querry_output['type'];
    if ($user_type != 'admin' && $username!= $_GET['user']) {
        $_SESSION['cust_error_msg'] = "You are not authorized to see this page. If you believe this is an error, please contact your administrator.";
        header('Location: Error.php');
    }
} else {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
    header('Location: Error.php');
}

//Retrieve the username of the user to manage from the url parameter
$userManaged = $_GET['user'];

//Check if username exists
$delete_query_str = "SELECT username from users WHERE username='$userManaged'";
$delete_query = $conn->query($delete_query_str);
$delete_query = $delete_query->fetch_row();
if (!$delete_query) {
    $_SESSION['cust_error_msg'] = "Invalid argument. Please access this page through the proper interfaces.";
    header('Location: Error.php');
}

//If form is submitted delete user
if(isset($_POST['deleteInput'])) {
    if ($_POST['deleteInput'] == 'delete') {
        $delete_query_str = "DELETE FROM users WHERE username='$userManaged'";
        $delete_query = $conn->query($delete_query_str);
        $_GET['deleteInput'] = null;
        if($userManaged==$_SESSION['username']) {
            header('Location: Signin.php');
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
        }

        else header('Location:Users.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
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
        <h4>This action will permananty delete user <?php echo($userManaged) ?> !</h4>
        <br>
        All data will be erased. This action cannot be undone.
        <br>
        <br>
        <p>Are you sure you want to procceed?</p>
        <br>

        <form method="post" action="DeleteUser.php?user=<?php echo($userManaged) ?>">
            <div class="form-group">
                <input type="hidden" class="form-control" id="deleteInput" name="deleteInput" value="delete">
            <button type="submit" class="btn btn-danger">Permanently Delete User</button>
        </form>
    </div>
</div>
</body>

