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

//If form has been upload announcement to db
if (isset($_POST['postAnnouncement'])) {
    $announce_text = $_POST['postAnnouncement'];
    $announcer = $username;
    $announce_query_str ="INSERT INTO announcements (announcer, announce_text) values(?,?)";
    $announce_query = $conn->prepare($announce_query_str);
    $announce_query->bind_param('ss', $announcer, $announce_text);
    $announce_query->execute();
    $_POST['postAnnouncement']=null;
    echo('<p id="confimration_banner">✔ Announcement posted successfully! <p>');
}

$conn->close();
?>

<html lang="en">
<head>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Scaena | Announce </title>
    <?php include('Elements/Imports.html') ?>

    <!--Functional Scripts-->
    <script>
        var currentNavItem = "#navLinkOptions";
    </script>
</head>
<body>
<?php include('Elements/Header.php'); ?>

<div class="container mt-12 restrictingContainer">
    <h2>New Announcement</h2>
    <p>Enter announcement text here. The latest announcement will be visible on the home page for all signed in users.</p>
    <br>
    <form action="Announce.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <textarea class="form-control" id="postAnnouncement" name="postAnnouncement" rows="5"></textarea>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary" name="submit">Post</button>
        </div>

    </form>
</div>

</body>
</html>


