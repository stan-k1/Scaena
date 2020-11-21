<?php
session_start();
include_once('Elements/dbConnector.php');
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
        var currentNavItem = "#navLinkAbout";
    </script>
</head>
<body>
<?php include('Elements/Header.php'); ?>
<div class="jumbotron" id="aboutJumbotron">
    <h1 class="display-4">Scaena</h1>
    <p class="lead">Video Engagement and Analysis Platform for Education</p>
    <hr class="my-4">
    <p>Free and open source software</p>
</div>
<!--<img src="Assets/scaena_logo_transparent.png" alt="Scaena Logo" style="width:226px;height:138px;margin: auto" class="center">-->
<p>Version 1.0</p>
<br>
<p>Based on or using the follwing software:</p>
<br>
<div class="list-group" style="margin: auto; max-width: 30%">
    <a href="https://www.php.net/" class="list-group-item list-group-item-action">PHP</a>
    <a href="https://www.mysql.com/" class="list-group-item list-group-item-action">MySQL</a>
    <a href="https://getbootstrap.com/" class="list-group-item list-group-item-action">Bootstrap 4</a>
    <a href="https://marketingplatform.google.com/" class="list-group-item list-group-item-action">Google Marketing Platform</a>
    <a href="https://videojs.com/" class="list-group-item list-group-item-action">Video JS</a>
    <a href="https://www.chartjs.org/" class="list-group-item list-group-item-action">Charts.js</a>
</div>
</body>
</html>