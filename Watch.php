<?php
include_once('Elements\dbConnector.php');
session_start();

//Retrieves the video based on the url (GET), defaults to the sea test video
//E.g. Scaena.php?view=sea_video.mp4 loads the Scaena.php page with sea_video.mp4
if (@$_GET['view']==null){
    //If no view is provided, a default is used.
    $querry=$conn->query("SELECT * FROM content where filename='sea_video.mp4'");
}
else{
    //Otherwise, the user provided view string is used to display the correct video.
    //User provided view value is sanitized to prevent MySQL injection attacks.
    $view=htmlspecialchars($_GET['view']);
    $view=$conn->real_escape_string($view);
    $querry=$conn->query("SELECT * FROM content where filename='".$view."'");
}

//Get video details from database
$querry_output=$querry->fetch_assoc();
$c_filename=$querry_output["filename"];
$short_desc=$querry_output["short_desc"];
$poster=$querry_output["poster"];
$access=$querry_output["access_level"];
$uploader=$querry_output['uploader'];
$title=$querry_output['title'];
$cont_desc=$querry_output['cont_desc'];


//Access Control
if (!is_null($access)) {
    if ($access == "private") {
        echo " private if ";
        $username = $_SESSION['username'];
        $user_querry = $conn->query("SELECT * FROM users where username='$username'");
        $user_querry_output = $user_querry->fetch_assoc();
        $user_type = $user_querry_output['type'];
        if ($user_type != 'admin' && $username != $uploader) {
            $_SESSION['cust_error_msg'] = "You are not authorized to see this page. If you believe this is an error, please contact your administrator.";
            header('Location: Error.php');
        }
    } else if ($access == "protected") {
        if (!isset($_SESSION['username'])) {
            $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
            header('Location: Error.php');
        }
    }

}

$conn->close()
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <?php include('Elements/TagmgrTag.html') ?>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Scaena</title>
    <?php include('Elements\Imports.html') ?>

    <!--Functional Scripts-->
    <script>
        var currentNavItem = "#navLinkWatch";
    </script>
</head>

<body>
<?php include('Elements\Header.html'); ?>

<div class="row" id="videoWatchColumn">
    <div class="col-xl-12 text-center">
        <h1 id="contentHeading"><?php echo($title) ?></h1>
        <h6><?php echo $short_desc ?></h6>

        <video class="video-js vjs-theme-sea" controls="true" id="video_player_large"
               poster="Assets/Content/<?php echo $poster ?>">
            <source src="Assets/Content/<?php echo $c_filename ?>" type="video/mp4">
            <!--                <source src="//vjs.zencdn.net/v/oceans.webm" type="video/webm">-->
        </video>
        <script src="https://vjs.zencdn.net/7.8.3/video.js"></script>
        <script src="videojs.ga.min.js"></script>
        <script>
            videojs('video_player_large', {}, function () {
                this.ga();
            })
            var vidPlayer = document.getElementById("video_player_large");
        </script>
    </div>
    <p id="cont_desc"><?php echo($cont_desc) ?></p>
</div>

</body>