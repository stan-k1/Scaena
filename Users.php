<?php
include_once('Elements/dbConnector.php');

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
        function selectUser(username) {
            alert("Username: "+username);
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
    $users_query->data_seek($j);
    $row = $users_query->fetch_array(MYSQLI_ASSOC);
    $username=$row['username'];

    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
    echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['type']) . "</td>";
    $username='"'.htmlspecialchars($row['username']).'"';
    echo "<td><button type='button' class='btn btn-outline-secondary' style='display:block; margin: auto' onclick='selectUser($username)'>Manage</button></td>";
    echo "</tr>";
}

echo ("</table>");

?>

<form action="Users.php" id="userSelectionForm">
    <input type="hidden" id="username_input" name="username_input" value="username"><br>
</form>

</body>