<?php
session_start();
include_once('Elements/dbConnector.php');

//Access Control
if (isset($_SESSION['username'])) {
    $username=$_SESSION['username'];
    $check_querry_str="SELECT type from users WHERE username='$username'";
    $check_querry=$conn->query($check_querry_str);
    $check_querry_output = $check_querry->fetch_assoc();
    $user_type = $check_querry_output['type'];
    if ($user_type != 'admin') {
        $_SESSION['cust_error_msg'] = "You are not authorized to see this page. If you believe this is an error, please contact your administrator.";
        header('Location: Error.php');
    }
}
else{
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. Please sign in to proceed.";
    header('Location: Error.php');
}

if (isset($_GET['username_input'])){
    header('Location: ManageUser.php?user='.$_GET['username_input']);
}

$users_query_str="SELECT username, email, first_name, last_name, type from users";
$users_query=$conn->query($users_query_str);
$rows = $users_query->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Users | Scaena</title>
    <?php include('Elements\Imports.html') ?>
    <script>
        var currentNavItem = "#navLinkOptions";
        function selectUser(username) {
            document.getElementById('username_input').value=username;
            document.getElementById("userSelectionForm").submit();
        }
        var usernamevar='defaultname';
    </script>
</head>

<body>
<?php
include_once('Elements/Header.html');
echo('<h1>Manage Users</h1>');

echo "<table class='table' id='usersTable'><tr> <th>Username</th> <th>First Name</th><th>Last Name</th><th>Email</th><th>Type</th><th> </th> </tr>";
for ($j = 0 ; $j < $rows ; ++$j)
{
    $row = $users_query->fetch_array(MYSQLI_ASSOC);
    $username=$row['username'];

    $type=$row['type'];
    if($type=='user'){$type='Student';}
    else if($type=='mod'){$type='Faculty';}
    else if($type=='admin'){$type='Administrator';}

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . $type . "</td>";
    $username='"'.htmlspecialchars($row['username']).'"';
    echo "<td><button type='button' class='btn btn-outline-secondary' style='display:block; margin: auto' onclick='selectUser($username)'>Manage</button></td>";
    echo "</tr>";
}

echo ("</table>");
$conn->close();
?>

<form action="Users.php" id="userSelectionForm">
    <input type="hidden" id="username_input" name="username_input" value="username"><br>
</form>

</body>