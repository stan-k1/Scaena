<?php
session_start();
include_once('Controller/Elements/dbConnector.php');

//Access Control
if (isset($_SESSION['username'])) {
    if ($user_type != 'admin' && $user_type != 'mod') {
        $_SESSION['cust_error_msg'] = "You are not authorized to see this page. If you believe this is an error, please contact your administrator.";
        header('Location: Error.php');
    }
}
else {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
    header('Location: Error.php');
}

if (isset($_GET['video_input'])){
    header('Location: Analyze.php?view='.$_GET['video_input']);
}

if($user_type=="admin") { //Admininstrators can edit and see analytics for all videos
    if (isset($_POST['order_by_input'])) {
        $order = $_POST['order_by_input'];
        if ($order == 'name') {
            $videos_query_str = "SELECT filename, title, uploader, upload_date, DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content ORDER BY title";
        } else if ($order == 'date') {
            $videos_query_str = "SELECT filename, title, uploader, upload_date, DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content ORDER BY upload_date DESC";
        } else if ($order == 'uploader') {
            $videos_query_str = "SELECT filename, title, uploader, upload_date, DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content ORDER BY uploader";
        }
    } else {
        $videos_query_str = "SELECT filename, title, uploader, upload_date, DATE_FORMAT(upload_date,'%d/%m/%Y'),  DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content ORDER BY title";
    }
}
else if($user_type=="mod") { //Moderators can edit and see analytics for videos they have uploaded
    if (isset($_POST['order_by_input'])) {
        $order = $_POST['order_by_input'];
        if ($order == 'name') {
            $videos_query_str = "SELECT filename, title, uploader, upload_date, DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content WHERE uploader='$username' ORDER BY title ";
        } else if ($order == 'date') {
            $videos_query_str = "SELECT filename, title, uploader, upload_date, DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content WHERE uploader='$username' ORDER BY upload_date DESC";
        } else if ($order == 'uploader') {
            $videos_query_str = "SELECT filename, title, uploader, upload_date, DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content WHERE uploader='$username' ORDER BY uploader ";
        }
    } else {
        $videos_query_str = "SELECT filename, title, uploader, upload_date, DATE_FORMAT(upload_date,'%d/%m/%Y') AS dateFormated from content WHERE uploader='$username' ORDER BY title";
    }
}
$videos_query=$conn->query($videos_query_str);
if (!$videos_query) echo 'not';
$rows = $videos_query->num_rows;

?>

<!DOCTYPE HTML>
<html lang="en">
<head>
    <?php include('Controller/Elements/Imports.html') ?>
    <title>Browse | Scaena</title>
    <script>
        var currentNavItem = "#navLinkAnalyze";
        var usernamevar='defaultname';

        function selectVideo(filename) {
            document.getElementById('video_input').value=filename;
            document.getElementById("videoSelectionForm").submit();
        }

        function orderBy(order){
            document.getElementById('order_by_input').value=order;
            document.getElementById("orderSelectionForm").submit();
        }
    </script>
</head>
<body>

<?php include('Controller/Elements/header.php');?>

<div class="container restrictingContainer">
    <div class="dropdown">
        <h2 style="text-align: left; display: inline">Select a Video to Analyze</h2>
        <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false" style="float: right; display: inline">
            Order By...
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <a class="dropdown-item" href="#" onclick="orderBy('name')">Title</a>
            <a class="dropdown-item" href="#" onclick="orderBy('uploader')">Creator</a>
            <a class="dropdown-item" href="#" onclick="orderBy('date')">Date</a>
        </div>
    </div>

    <br>
    <br>

    <?php echo "<table class='table' id='videosTable'><tr> <th>Title</th><th>Creator</th><th>Date</th><th> </th> </tr>";
    for ($j = 0; $j < $rows; ++$j) {
        //Get details for each video
        $row = $videos_query->fetch_array(MYSQLI_ASSOC);
        //Print link with video title
        $filename = '"'.$row['filename'].'"';
        $uploader=$row['uploader'];
        $upload_date=$row['dateFormated'];


        echo "<tr>";
        echo "<td><a href='Watch.php?view=".$row['filename']. "'>".$row['title'] . "</td>";
        //Print uploader first and last name
        $uploader_query_str="SELECT first_name, last_name from users WHERE username='$uploader'";
        $uploader_query=$conn->query($uploader_query_str);
        $uploader_row=$uploader_query->fetch_array(MYSQLI_ASSOC);
        $uploder_name=$uploader_row['first_name']." ".$uploader_row['last_name'];
        echo "<td>" . $uploder_name . "</td>";
        echo "<td>" . $upload_date . "</td>";
        echo "<td><button type='button' class='btn btn-outline-secondary' style='display:block; margin: auto' onclick='selectVideo($filename)'>Analyze</button></td>";
        echo "</tr>";
    }

    echo ("</table>");
    $conn->close();
    ?>

    <div id="LinkDiv">
        <a href="Upload.php"><i class="material-icons">add_circle_outline</i> Upload New Content</a>
    </div>

    <form action="Browse.php" id="videoSelectionForm">
        <input type="hidden" id="video_input" name="video_input" value="filename"><br>
    </form>

    <form action="Browse.php" id="orderSelectionForm" method="post">
        <input type="hidden" id="order_by_input" name="order_by_input" value=""><br>
    </form>
</div>
<script>
    //Clears State To Prevent Form Resend Warnings On Reload or Back Press
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
</body>

