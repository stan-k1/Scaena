<?php
session_start();
include_once('Controller/Elements/dbConnector.php');

if (@$_GET['view'] == null) {
    $_SESSION['cust_error_msg'] = "Invalid argument. Please access this page through the proper interfaces.";
    header('Location: Error.php');
} else {
    //Otherwise, the user provided view string is used to display the correct video.
    //User provided view value is sanitized to prevent MySQL injection attacks.
    $view = htmlspecialchars($_GET['view']);
    $view = $conn->real_escape_string($view);
    $querry = $conn->query("SELECT * FROM content where filename='" . $view . "'");
    $querry_output = $querry->fetch_assoc();
    $c_filename = $querry_output["filename"];
}

//Access Control for Analytics Pages
if ($user_type != 'admin' && $username != $uploader) {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. If you believe this is an error, please contact your administrator.";
    header('Location: Error.php');
}

//Update Content Details if Form is Set
$cont_filename=$_GET['view'];

if(isset($_POST['contTitle'])) {
    $cont_title = $_POST['contTitle'];
    $short_desc = $_POST['shortDesc'];
    $cont_desc = $_POST['compDesc'];
    $access = $access_level = $_POST['accessLevelRadio'];
    $edit_query_string = "UPDATE content SET short_desc=?, cont_desc=?, title=?, access_level=? WHERE filename=?";
    $edit_query = $conn->prepare($edit_query_string);
    $edit_query->bind_param("sssss", $short_desc, $cont_desc, $cont_title, $access_level, $c_filename);
    $edit_query->execute();
    if (!$edit_query){
        echo '<p>✘ Could not update content details. Please try again.</p>';
    }
}

//Get video details from database
$querry = $conn->query("SELECT * FROM content where filename='" . $view . "'");
$querry_output = $querry->fetch_assoc();
$c_filename = $querry_output["filename"];
$short_desc = $querry_output["short_desc"];
$poster = $querry_output["poster"];
$access = $querry_output["access_level"];
$uploader = $querry_output['uploader'];
$title = $querry_output['title'];
$cont_desc = $querry_output['cont_desc'];

?>

<html lang="en">
<head>
    <?php include('Controller/Elements/TagmgrTag.html') ?>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Edit Content | Scaena</title>
    <?php include('Controller/Elements/Imports.html') ?>

    <!--Functional Scripts-->
    <script>
        var currentNavItem = "#navLinkAnalyze";
    </script>
</head>

<body>
<?php include('Controller/Elements/Header.php'); ?>

<div class="container mt-12 restrictingContainer">
    <h2>Edit Content Details</h2>
    <p>Update title, descriptions or access level.</p>
    <br>
    <form action="#" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Video Title:</label>
            <input type="text" name="contTitle" class="form-control" maxlength="90" value="<?php echo $title ?>">
        </div>

        <div class="form-group">
            <label>Short Description:</label>
            <input type="text" name="shortDesc" class="form-control" maxlength="90" value="<?php echo $short_desc ?>">
        </div>

        <div class="form-group">
            <label>Complete Description:</label>
            <textarea class="form-control" id="compDesc" name="compDesc" rows="5"><?php echo $cont_desc ?></textarea>
        </div>

        <div class="form-group">
            <p class="uploadLabel">Access Level: </p>
            <?php if(isset($_POST['access_leval'])) $set=true; ?>
            <label class="radio-inline radio-inline-spaced"><input type="radio" name="accessLevelRadio" value="public" <?php if($access =='public') echo 'checked' ?>> Public</label>
            <label class="radio-inline radio-inline-spaced"><input type="radio" name="accessLevelRadio" value="protected" <?php if($access=='protected') echo 'checked' ?>> Protected</label>
            <label class="radio-inline radio-inline-spaced"><input type="radio" name="accessLevelRadio" value="private" <?php if($access =='private') echo 'checked' ?>> Private</label>
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
