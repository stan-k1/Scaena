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
    header('Location: Signin.php');
}

//Delete an announcement if the form is set
if (isset($_POST['delete_announcement_input'])){
    $delete_comment_id=stringSanitizer($_POST['delete_announcement_input']);
    $post_querry = $conn->query("DELETE FROM announcements WHERE id=$delete_comment_id");
}

//Get Announcements from DB
$announce_query_str = "SELECT id, announcer, datetime, announce_text,
DATE_FORMAT(datetime,'%d/%m/%Y - %H:%i') AS dateFormated from announcements ORDER BY id DESC";
$announce_query = $conn->query($announce_query_str);
$rows = $announce_query->num_rows;

$isAdmin=false;
$isMod=false;
if($user_type== "admin"){
    $isAdmin=true;
    $isMod=true;
}
else if($user_type== "mod"){
    $isMod=true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Scaena</title>
    <?php include('Elements/Imports.html') ?>

    <!--Functional Scripts-->
    <script>
        var currentNavItem = "#navLinkOptions";
        var setDatesToggled = false;

        function selectVideo(filename) {
            document.getElementById('video_input').value = filename;
            document.getElementById("videoSelectionForm").submit();
        }
    </script>
</head>
<body>
<?php include('Elements/Header.php'); ?>

<h1>Announcements</h1>

<?php
if(!is_null($rows)) {
//Get announcements details
    for ($j = 0; $j < $rows; ++$j) {
        $row = $announce_query->fetch_array(MYSQLI_ASSOC);
        $id = $row['id'];
        $announcer = $row['announcer'];
        $dateFormated = $row['dateFormated'];
        $text = $row['announce_text'];
//Get announcer details
        $user_query_str = "SELECT first_name, last_name from users WHERE username='$announcer'";
        $user_query = $conn->query($user_query_str);
        $user_row = $user_query->fetch_array(MYSQLI_ASSOC);
        $uploder_name = $user_row['first_name'] . " " . $user_row['last_name'];

        echo <<<EOD
        <div class='announceItem'>
            <h4>Announcement by $uploder_name on $dateFormated</h4>
            <p>$text</p>
EOD;
        if ($isAdmin) {
            echo "<p><a href='#' style='color: #cd1c1c; text-align: center; display: block; margin-top: 16px' onclick='deleteAnnounce($id)'> Delete </a></p>";
        }
        echo '</div>';
    }
}

if ($isAdmin){
    echo <<<EOD
    <div id="LinkDiv">
    <a href="Announce.php"><i class="material-icons">add_circle_outline</i> Make a New Announcement</a>
    </div>
EOD;
}

?>

<!--Delete Announcement Hidden Form-->
<form action="#" id="deleteAnnouncementForm" method="post">
    <input type="hidden" id="delete_announcement_input" name="delete_announcement_input" value="announceid"><br>
</form>

<script>
    function deleteAnnounce(id){
        document.getElementById('delete_announcement_input').value=id;
        document.getElementById('deleteAnnouncementForm').submit();
    }
</script>
