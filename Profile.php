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
} else {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
    header('Location: Error.php');
}

//Retrieve the username of the user to manage from session
$userManaged = $username;
//Updates the username var in session if the user has changed his own username so that access to the site can continue without loggin in again
if (isset($_POST['mng_username'])) {
     $_SESSION['username']= $_POST['mng_username'];
}

//If form has been sumbitted update the db
if (isset($_POST['mng_username'])) {
    $updated_username = $_POST['mng_username'];
    $updated_password = $_POST["mng_password"];
    $updated_first_name = $_POST["mng_first_name"];
    $updated_last_name = $_POST["mng_last_name"];
    $updated_email = $_POST["mng_email"];
    $user_edit_query_str = "UPDATE Users SET username=?, password= ?, first_name= ?, 
     last_name=?, email=? WHERE username=?";
    $user_edit_query = $conn->prepare($user_edit_query_str);
    $user_edit_query->bind_param('ssssss',$updated_username, $updated_password, $updated_first_name, $updated_last_name,
        $updated_email, $userManaged);
    $user_edit_query->execute();
    $_POST['mng_username']=null;
    $_GET['user']=$updated_username;
    $userManaged = $updated_username;
    if ($user_edit_query->error) echo '<p>✘ Could not update your profile. Please try again.</p>';
    else echo('<p id="confimration_banner">✔ Your profile has been updated. <p>');
}

//Retrieve Managed User Details
$manage_query_str = "SELECT username, password, email, first_name, last_name from users WHERE username='$userManaged'";
$manage_query = $conn->query($manage_query_str);
$manage_query = $manage_query->fetch_assoc();
//if (!$manage_query) {
//    $_SESSION['cust_error_msg'] = "Invalid argument. Please access this page through the proper interfaces.";
//    header('Location: Error.php');
//}

$userManaged_password = $manage_query['password'];
$userManaged_first_name = $manage_query['first_name'];
$userManaged_last_name = $manage_query['last_name'];
$userManaged_email = $manage_query['email'];

$conn->close();
?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <?php include('Elements/Imports.html') ?>
    <title>Profile | Scaena</title>
    <script>
        var currentNavItem = "#navLinkOptions";
    </script>
</head>
<body>
<?php include('Elements/header.php') ?>
<h1>Edit Your Profile</h1>
<h6>Update your user profile details</h6>

<form action="Profile.php" method="POST">
    <div class="form-group row">
        <label for="mng_username" class="col-sm-1 col-form-label">Username: </label>
        <div class="col-sm-11">
            <input type="text" class="form-control" name="mng_username" id="mng_username" value="<?php echo($userManaged)?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="mng_password" class="col-sm-1 col-form-label">Password: </label>
        <div class="col-sm-11">
            <input type="text" class="form-control" name="mng_password" id="mng_password" value="<?php echo($userManaged_password)?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="mng_password" class="col-sm-1 col-form-label">First Name: </label>
        <div class="col-sm-11">
            <input type="text" class="form-control" id="mng_password" name="mng_first_name" value="<?php echo($userManaged_first_name)?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="mng_last_name" class="col-sm-1 col-form-label">Last Name: </label>
        <div class="col-sm-11">
            <input type="text" class="form-control" id="mng_last_name" name="mng_last_name" value="<?php echo($userManaged_last_name)?>" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="mng_email" class="col-sm-1 col-form-label">Email: </label>
        <div class="col-sm-11">
            <input type="text" class="form-control" id="mng_email" name="mng_email" value="<?php echo($userManaged_email)?>" required>
        </div>
    </div>

    <div class="form-group row">
        <button type="submit" class="btn btn-secondary" style="margin: auto">Save Changes</button>
    </div>
</form>

<div id="LinkDiv">
    <a href="DeleteUser.php?user=<?php echo($userManaged)?>">Delete Your Profile</a>
</div>

</body>
</html>

