<?php
session_start();
include_once('Controller/Elements/dbConnector.php');

//Retrieves the video based on the url (GET), defaults to the sea test video
//E.g. Analyze.php?view=sea_video.mp4 loads the Analyze.php page with sea_video.mp4
if (@$_GET['view'] == null) {
    //If no view is provided, a default is used.
    $querry = $conn->query("SELECT * FROM content where filename='sea_video.mp4'");
} else {
    //Otherwise, the user provided view string is used to display the correct video.
    //User provided view value is sanitized to prevent MySQL injection attacks.
    $view = htmlspecialchars($_GET['view']);
    $view = $conn->real_escape_string($view);
    $querry = $conn->query("SELECT * FROM content where filename='" . $view . "'");
}

//Get video details from database
$querry_output = $querry->fetch_assoc();
$c_filename = $querry_output["filename"];
$short_desc = $querry_output["short_desc"];
$poster = $querry_output["poster"];
$access = $querry_output["access_level"];
$uploader = $querry_output['uploader'];
$title = $querry_output['title'];
$cont_desc = $querry_output['cont_desc'];

$uploader_query_str = "SELECT first_name, last_name from users WHERE username='$uploader'";
$uploader_query = $conn->query($uploader_query_str);
$uploader_row = $uploader_query->fetch_array(MYSQLI_ASSOC);
$uploder_name = $uploader_row['first_name'] . " " . $uploader_row['last_name'];

$link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

//Access Control
if (!is_null($access)) {
    if ($access == "private") {
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
    <?php include('Controller/Elements/TagmgrTag.html') ?>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Scaena</title>
    <?php include('Controller/Elements/Imports.html') ?>

    <!--Functional Scripts-->
    <script>
        var currentNavItem = "#navLinkWatch";
    </script>
</head>

<body>
<!-- Load Facebook SDK for JavaScript -->
<div id="fb-root"></div>
<script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>

<!--Main Body-->
<?php include('Controller/Elements/Header.php'); ?>


<div class="row" id="videoWatchColumn">
    <div class="col-xl-12 text-center">
        <video class="video-js vjs-theme-sea" controls="true" id="video_player_large"
               poster="Model/Content/<?php echo $poster ?>">
            <source src="Model/Content/<?php echo $c_filename ?>" type="video/mp4">
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
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <h2 id="watchVideoTitle"><?php echo($title) ?></h2>
        <p id="watchVideoUploader">by <?php echo($uploder_name) ?></p>
    </div>
    <br>
    <div class="col-md-12">
        <p id="watchVideoShortDesc"><?php echo $short_desc ?></p>
    </div>
    <div class="col-md-12" id="watchVideoContDesc">
        <p id="cont_desc"><?php echo($cont_desc) ?></p>
    </div>
</div>

<div class="row" id="shareButtonsRow">
    <div class="col-xl-12" align="center">
        <div class="fb-share-button" style="height:20px; vertical-align: top;" data-href="<?php echo $link ?>" data-layout="button" data-size="large"><a
                    target="_blank"
                    href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fscaena.com%2Fwatch&amp;src=sdkpreparse"
                    class="fb-xfbml-parse-ignore">Share</a></div>

        <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-size="large" data-text="<?php echo ($title.":");?>" data-show-count="false">Tweet</a>
        <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

        <a href="mailto:?subject=<?php echo $title ?>&body=<?php echo $title.' : '.$link ?>"
           class="share-button facebook"
           target="_blank"><svg fill="currentColor"
                               height="12"
                               viewBox="0 0 24 24"
                               width="24"
                               xmlns="http://www.w3.org/2000/svg"><path d="M20,8L12,13L4,8V6L12,11L20,6M20,4H4C2.89,4 2,4.89 2,6V18A2,2 0 0,0 4,20H20A2,2 0 0,0 22,18V6C22,4.89 21.1,4 20,4Z" /></svg>
         Email</a>
</div>
</div>
<br>

</body>