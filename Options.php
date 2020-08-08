<?php
include_once('Elements\dbConnector.php');
session_start();
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $user_querry_str = "SELECT type from users WHERE username='$username'";
    $user_querry = $conn->query($user_querry_str);
    $user_querry_output = $user_querry->fetch_assoc();
    $user_type = $user_querry_output['type'];
    if ($user_type == 'admin') {
        $isAdmin = true;
    }
} else {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
    header('Location: Error.php');
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <?php include_once('Elements/Imports.html'); ?>
    <title>Options | Scaena</title>
    <script>
        var currentNavItem = "#navLinkOptions";
    </script>
</head>
<body>
<?php include_once('Elements/Header.html') ?>

<?php
if ($isAdmin) {
    echo '<div class="optionsMenuItem">';
    echo '<a class="optionsMenuLink" href="Users.php"><i class="material-icons font-icon-upped">supervisor_account</i> Manage Users</a>';
    echo '</div>';
}
?>

<div class="optionsMenuItem">
    <a class="optionsMenuLink" href="DeleteUser.php?user=<?php echo($username)?>"><i class="material-icons font-icon-upped">delete_forever</i> Delete Your Account</a>
</div>

</body>



