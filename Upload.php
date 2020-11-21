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
    if ($user_type != 'admin' && $user_type != 'mod') {
        $_SESSION['cust_error_msg'] = "You are not authorized to see this page. If you believe this is an error, please contact your administrator.";
        header('Location: Error.php');
    }
} else {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
    header('Location: Error.php');
}

//Upload Script For Video
$uploadOk = 1;
if(isset($_POST["submit"])) {
    $target_dir = "Content/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));


// Check if file already exists
    if (file_exists($target_file)) {
        echo ("<p id='rejection_banner'>✘ A file with the same name already exits. Please try a different one.</p>");
        $uploadOk = 0;
    }

// Check file size. Default maximum file size is 128gb.
    if ($_FILES["fileToUpload"]["size"] > 128000000) {
        echo ("<p id='rejection_banner'>✘ This file exceeds the upload size limit. Please try a smaller file.</p>");
        $uploadOk = 0;
    }

// Allow certain file formats
    if ($imageFileType != "mp4") {
        echo ("<p id='rejection_banner'>✘ Please choose an MP4 video file to upload.</p>");
        $uploadOk = 0;
    }

// if everything is ok, try to upload file
    if($uploadOk==1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            //If Video Uplad is OK, start trying to upload the poster.
            $filename = $_FILES['fileToUpload']['name'];
            $target_dir = "Content/";
            $target_file = $target_dir . basename($_FILES["posterToUpload"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (file_exists($target_file)) {
                echo ("<p id='rejection_banner'>✘ A file with the same name already exits. Please try a different one.</p>");
                $uploadOk = 0;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                echo ("<p id='rejection_banner'>✘ Please choose a supported image file to upload.</p>");
                $uploadOk = 0;
            }

            if ($uploadOk==1 && move_uploaded_file($_FILES["posterToUpload"]["tmp_name"], $target_file)) {
                //If poster has also been uploaded,apply total of changes to database.
                $upload_query=$conn->prepare("INSERT INTO content (filename, uploader, short_desc, cont_desc, poster, 
                    title, access_level, upload_date) VALUES ( ?,?,?,?,?,?,?,?)");
                $uploader = $_SESSION['username'];
                $poster_filename = $_FILES['posterToUpload']['name'];
                $short_desc = $_POST['shortDesc'];
                $cont_desc = $_POST['compDesc'];
                $cont_title = $_POST['contTitle'];
                $access_level = $_POST['accessLevelRadio'];
                $upload_date=date("Y-m-d");
                $upload_query->bind_param("ssssssss", $filename, $uploader, $short_desc, $cont_desc, $poster_filename,
                    $cont_title, $access_level, $upload_date);
                $upload_query->execute();
                echo("<p id='confimration_banner'>✔ The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.<p>");
            }
            else{
                unlink("Content/$filename");
                echo ("<p id='rejection_banner'>✘ An error occurred while uploading your file. Please try again.</p>");
            }
        } else {
            echo ("<p id='rejection_banner'>✘ An error occurred while uploading your file. Please try again.</p>");
        }
    }
}

$conn->close();
?>

<html lang="en">
<head>
    <?php include('Elements/TagmgrTag.html') ?>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Scaena</title>
    <?php include('Elements/Imports.html') ?>

    <!--Functional Scripts-->
    <script>
        var currentNavItem = "#navLinkWatch";
    </script>
</head>

<body>
<?php include('Elements/Header.php'); ?>

<div class="container mt-12 restrictingContainer">
    <h2>Upload Video</h2>
    <p>Select content to upload. Videos must be in the MP4 format. Posters can be in JPEG/JPG or PNG format.</p>
    <br>
    <form action="Upload.php" method="post" enctype="multipart/form-data">
        <p class="uploadLabel">Video Upload: </p>
        <div class="custom-file mb-3">
            <input type="file" class="custom-file-input" id="fileToUpload" name="fileToUpload" required>
            <label class="custom-file-label" for="fileToUpload">Choose file...</label>
        </div>

        <p class="uploadLabel">Content Poster: </p>
        <div class="custom-file mb-3">
            <input type="file" class="custom-file-input" id="posterToUpload" name="posterToUpload" required>
            <label class="custom-file-label" for="posterToUpload">Choose file...</label>
        </div>

        <div class="form-group">
            <label>Video Title:</label>
            <input type="text" name="contTitle" class="form-control" maxlength="90">
        </div>

        <div class="form-group">
            <label>Short Description: <span style='color:gray')>(Up to 90 Characeters)</span></label>
            <input type="text" name="shortDesc" class="form-control" maxlength="90">
        </div>

        <div class="form-group">
            <label>Complete Description:</label>
            <textarea class="form-control" id="compDesc" name="compDesc" rows="5"></textarea>
        </div>

        <div class="form-group">
        <p class="uploadLabel">Access Level: </p>
            <label class="radio-inline radio-inline-spaced"><input type="radio" name="accessLevelRadio" value="public"> Public</label>
            <label class="radio-inline radio-inline-spaced"><input type="radio" name="accessLevelRadio" value="protected" checked> Protected</label>
            <label class="radio-inline radio-inline-spaced"><input type="radio" name="accessLevelRadio" value="private"> Private</label>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary" name="submit">Upload</button>
        </div>

    </form>
</div>

<script>
    // Add the following code if you want the name of the file appear on select
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });
</script>

</body>
</html>

