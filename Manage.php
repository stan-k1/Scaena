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
    if ($user_type != 'admin') {
        $_SESSION['cust_error_msg'] = "You are not authorized to see this page. If you believe this is an error, please contact your administrator.";
        header('Location: Error.php');
    }
} else {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
    header('Location: Error.php');
}

//Retrieve the username of the user to manage from the url parameter
$userManaged = $_GET['user'];

//If form has been sumbitted update the db
if (isset($_POST['mng_username'])) {
    $updated_username = $_POST['mng_username'];
    $updated_password = $_POST["mng_password"];
    $updated_first_name = $_POST["mng_first_name"];
    $updated_last_name = $_POST["mng_last_name"];
    $updated_email = $_POST["mng_email"];
    $updated_type = $_POST["mng_type"];
    $user_edit_query_str = "UPDATE Users SET username='$updated_username', password='$updated_password', first_name='$updated_last_name', 
     last_name='$updated_last_name', type='$updated_type', email='$updated_email' WHERE username='$userManaged'";
    $check_querry = $conn->query($user_edit_query_str);
    $_POST['mng_username']=null;
    $_GET['user']=$updated_username;
    $userManaged = $updated_username;
    echo('<p id="confimration_banner">✔ Chanages to user '. $updated_username.' '.$updated_last_name.' ('.$userManaged.')'.' saved! <p>');
}

//Retrieve Managed User Details
$manage_query_str = "SELECT username, password, email, first_name, last_name, type from users WHERE username='$userManaged'";
$manage_query = $conn->query($manage_query_str);
$manage_query = $manage_query->fetch_assoc();
if (!$manage_query) {
    $_SESSION['cust_error_msg'] = "Invalid argument. Please access this page through the proper interfaces.";
    header('Location: Error.php');
}

$userManaged_password = $manage_query['password'];
$userManaged_first_name = $manage_query['first_name'];
$userManaged_last_name = $manage_query['last_name'];
$userManaged_email = $manage_query['email'];
$userManaged_type = $manage_query['type'];

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Manage User | Scaena</title>
    <script>
        function enable(element){
            element.preventDefault();
            $('.inputDisabled').prop("disabled", false);
    </script>
</head>
<body>
<?php include('Elements/Imports.html') ?>
<?php include('Elements/header.html') ?>
<h1>Manage User | <?php echo($userManaged_first_name.' '.$userManaged_last_name.' ('.$userManaged.')') ?></h1>
<h6>Edit User Details and Privileges</h6>

<form action="Manage.php?user=<?php echo($userManaged) ?>" method="POST">
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

    <div class="form-group row" id="mng_user_form">
        <label for="mng_password" class="col-sm-1 col-form-label">User Type: </label>
        <div class="col-sm-11">
            <div class="form-check-inline">
                <input class="form-check-input" type="radio" name="mng_type" id="mng_type1" value="user" required
                <?php if($userManaged_type=='user') echo('checked'); ?> >
                <label class="form-check-label" for="exampleRadios1">
                    Student
                </label>
            </div>
            <div class="form-check-inline">
                <input class="form-check-input" type="radio" name="mng_type" id="mng_type2" value="mod"
                    <?php if($userManaged_type=='mod') echo('checked'); ?> >
                <label class="form-check-label" for="exampleRadios2">
                    Faculty
                </label>
            </div>
            <div class="form-check-inline">
                <input class="form-check-input" type="radio" name="mng_type" id="mng_type3" value="admin"
                <?php if($userManaged_type=='admin') echo('checked'); ?> >
                <label class="form-check-label" for="exampleRadios3">
                    Administrator
                </label>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <button type="submit" class="btn btn-secondary" style="margin: auto">Save Changes</button>
    </div>
</form>

<div id="mngUsersLinkDiv">
    <a href="Manage.php">Manage Users</a>
    <span> | </span>
    <a href="DeleteUser.php?user=<?php echo($userManaged)?>">Delete User</a>
</div>

</body>
