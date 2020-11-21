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
//Get User First and Last Name
//Print uploader first and last name
$user_query_str = "SELECT first_name, last_name from users WHERE username='$username'";
$user_query = $conn->query($user_query_str);
$user_row = $user_query->fetch_array(MYSQLI_ASSOC);
$first_name = $user_row['first_name'];
$full_name = $user_row['first_name'] . " " . $user_row['last_name'];

//Get Latest Videos From DB
$videos_query_str = "SELECT filename, title, uploader, poster, upload_date, access_level, 
DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content ORDER BY upload_date DESC LIMIT 5";
$videos_query = $conn->query($videos_query_str);
$rows = $videos_query->num_rows;

//Get Announcements from DB
$announcement_available=false;
$announce_query_str = "SELECT id, announcer, datetime, announce_text,
DATE_FORMAT(datetime,'%d/%m/%Y - %H:%i') AS dateFormated from announcements ORDER BY id DESC LIMIT 1";
$announce_query = $conn->query($announce_query_str);
if(!is_null($announce_query)){
    $announcement = $announce_query->num_rows;
    $announcement_available=true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('Elements/TagmgrTag.html') ?>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Scaena</title>
    <?php include('Elements/Imports.html') ?>

    <!--Functional Scripts-->
    <script>
        var currentNavItem = "#navLinkHome";
        var setDatesToggled = false;

        function selectVideo(filename) {
            document.getElementById('video_input').value = filename;
            document.getElementById("videoSelectionForm").submit();
        }
    </script>
</head>
<body>
<?php include('Elements/Header.php'); ?>


<div class="jumbotron jumbotron-fluid" id="welcomeJumbotron">
    <div class="container">
        <h1 class="display-4">Welcome, <?php echo $first_name ?></h1>
        <?php
        if($isMod) echo ('<p class="lead"><a href="Browse.php" id="jumbotronLink">Analyze your videos ►</a> </p>');
        else echo('<p class="lead"><a href="Discover.php" id="jumbotronLink">Discover all videos ►</a> </p>')
        ?>
    </div>
</div>

<h2>What would you like to do today?</h2>
<div class="homeMenuItem">
    <a class="optionsMenuLink" href="Discover.php"><i class="material-icons font-icon-upped">video_library</i> Watch a Video</a>
</div>
<?php
if ($isMod) {
    echo '<div class="homeMenuItem">';
    echo '<a class="optionsMenuLink" href="Browse.php"><i class="material-icons font-icon-upped">show_chart</i> Analyze Your Videos</a>';
    echo '</div>';

    echo '<div class="homeMenuItem">';
    echo '<a class="optionsMenuLink" href="Upload.php"><i class="material-icons font-icon-upped">cloud_upload</i> Upload a Video</a>';
    echo '</div>';
}
if ($isAdmin) {
    echo '<div class="homeMenuItem">';
    echo '<a class="optionsMenuLink" href="Users.php"><i class="material-icons font-icon-upped">supervisor_account</i> Manage Users</a>';
    echo '</div>';
}
?>
<div class="homeMenuItem">
    <a class="optionsMenuLink" href="Signout.php"><i class="material-icons font-icon-upped">logout</i> Sign Out</a>
</div>

<?php
if($announcement_available) {
    echo '<h2>See the Latest Announcement</h2>';
//Get announcements details
        $row = $announce_query->fetch_array(MYSQLI_ASSOC);
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
            <h5><i>Announcement by $uploder_name on $dateFormated</i></h5>
            <p>$text</p>
        </div>
        <p><a href="Announcements.php">Review all announcements</a></p>
EOD;
}
?>

<h2>Explore the Latest Uploads</h2>
<?php
//Display 5 Latest Vidoes
echo "<table class='table' id='videosTable'><tr><th>Title and Preview</th><th>Creator</th><th>Date</th><th> </th> </tr>";
for ($j = 0; $j < $rows; ++$j) {
    //Get details for each video
    $row = $videos_query->fetch_array(MYSQLI_ASSOC);
    //Print link with video title
    $filename = '"' . $row['filename'] . '"';
    $uploader = $row['uploader'];
    $upload_date = $row['dateFormated'];
    $poster = $row['poster'];
    $access_level = $row['access_level'];

    //Skip a video that is private if it is not uploaded by current user and current user is not an admin
    if ($access_level=='private'){
        if ($user_type!='admin' && $username!=$uploader) continue;
    }

    echo "<tr>";
    echo "<td><img src='Content/" . $poster . "'alt='Scaena Video' class='previewImg' width='150' height='100'><a href='Watch.php?view=" . $row['filename'] . "'>" . $row['title'] . "</td>";
    //Print uploader first and last name
    $user_query_str = "SELECT first_name, last_name from users WHERE username='$uploader'";
    $user_query = $conn->query($user_query_str);
    $user_row = $user_query->fetch_array(MYSQLI_ASSOC);
    $uploder_name = $user_row['first_name'] . " " . $user_row['last_name'];
    echo "<td>" . $uploder_name . "</td>";
    echo "<td>" . $upload_date . "</td>";
    echo "<td><button type='button' class='btn btn-outline-secondary' style='display:block; margin: auto' onclick='selectVideo($filename)'>Watch</button></td>";
    echo "</tr>";
}

echo("</table>");
$conn->close();
?>
<script>
    //Clears State To Prevent Form Resend Warnings On Reload or Back Press
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
<p>Signed in as <?php echo $full_name.' ('.$username.')'.'. '?><a href="Signout.php">Sign Out</a></p>
<form action="Discover.php" id="videoSelectionForm">
    <input type="hidden" id="video_input" name="video_input" value="filename"><br>
</form>

<form action="Discover.php" id="orderSelectionForm" method="post">
    <input type="hidden" id="order_by_input" name="order_by_input" value=""><br>
</form>
</body>
</html>