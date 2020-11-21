<?php
//Creates a connection with the MariaDB/MySQL Database.
// Modify Username and Password access values for database here.
$db_servername = "localhost";
$db_username = "root";
$db_password = "";

$conn = new mysqli($db_servername, $db_username, $db_password);
if ($conn->connect_error) {
    $_SESSION['cust_error_msg'] = "An error occurred while connecting to the database. 
    The database server may be offline or unresponsive.";
    header('Location: Error.php');
}
$conn->select_db("scaena");

function stringSanitizer($var)
{
    $var = htmlentities($var);
    $var = strip_tags($var);
    return $var;
}

//User privileges
$isMod = false;
$isAdmin = false;

if(isset($_SESSION['username'])){
    $username = $_SESSION['username'];
    $check_querry_str = "SELECT type from users WHERE username='$username'";
    $check_querry = $conn->query($check_querry_str);
    $check_querry_output = $check_querry->fetch_assoc();
    $user_type = $check_querry_output['type'];

    if ($user_type == 'admin'){
        $isMod=true;
        $isAdmin=true;
    }
    else if($user_type == 'mod') {
        $isMod=true;
    }

}
?>