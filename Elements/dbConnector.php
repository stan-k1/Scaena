<?php
//Creates a connection with the MariaDB/MySQL Database. Modify Username and Password access values for database here.
$db_servername = "localhost";
$db_username = "root";
$db_password = "";

$conn = new mysqli($db_servername, $db_username, $db_password);
if ($conn->connect_error) {
    $_SESSION['cust_error_msg'] = "An error occured while connecting to the database. The database server may be offline or unresponsive.";
    header('Location: Error.php');
}
$conn->select_db("scaena");

/* DEBUG: return name of current default database */
//if ($result = $conn->query("SELECT DATABASE()")) {
//    $row = $result->fetch_row();
//    printf("Default database is %s.\n", $row[0]);
//    $result->close();
//}
