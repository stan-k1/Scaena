<?php
// Initialize the session.
session_start();

// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
?>

<head>
    <?php include('Elements\Imports.html') ?>
    <title>Sign Out | Scaena</title>
    <style>
        body{
            background-image: url('Assets/scaena_background.png');
        }
        .wrapper{
            width: 350px; padding: 20px;
        }
    </style>
</head>

<body>
<div class="row" style="position: relative">
    <div class="wrapper" id="errorBlock">
        <img src="Assets/baseline_login_black_48dp.png" id="errorIcon">
        <br>
        <h4>You have signed out.</h4>
        <br>
        <p>You may also want to close all browser windows.</p>
    </div>
</div>
</body>
