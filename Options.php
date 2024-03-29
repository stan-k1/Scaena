<?php
session_start();
include_once('Controller/Elements/dbConnector.php');

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user_querry_str = "SELECT type from users WHERE username='$username'";
    $user_querry = $conn->query($user_querry_str);
    $user_querry_output = $user_querry->fetch_assoc();
    $user_type = $user_querry_output['type'];
    if ($user_type == 'admin') {
        $isAdmin = true;
    }
    else {$isAdmin=false;}
} else {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
    header('Location: Error.php');
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once('Controller/Elements/Imports.html'); ?>
    <title>Options | Scaena</title>
    <script>
        var currentNavItem = "#navLinkOptions";
    </script>
</head>
<body>
<?php include_once('Controller/Elements/Header.php') ?>

<div class="optionsMenuItem">
    <a class="optionsMenuLink" href="Profile.php"><i class="material-icons font-icon-upped">account_circle</i> Edit Your Profile</a>
</div>

<?php
if ($isAdmin) {
    echo '<div class="optionsMenuItem">';
    echo '<a class="optionsMenuLink" href="Users.php"><i class="material-icons font-icon-upped">supervisor_account</i> Manage Users</a>';
    echo '</div>';
}
?>

<div class="optionsMenuItem">
    <a class="optionsMenuLink" href="Signout.php"><i class="material-icons font-icon-upped">logout</i> Sign Out</a>
</div>

<div class="optionsMenuItem">
    <a class="optionsMenuLink" href="DeleteUser.php?user=<?php echo($username)?>"><i class="material-icons font-icon-upped">delete_forever</i> Delete Your Account</a>
</div>

</body>
</html>



