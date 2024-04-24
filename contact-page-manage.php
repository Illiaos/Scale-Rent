<?php
// start a session, if not already started
session_start();

// include database configuration file
require_once "config.php";

// get messages from the database
$sql = "SELECT * FROM contact_messages ORDER BY timestamp DESC";
$result = $mysqli->query($sql); // execute the SQL query and store the result in $result variable
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a href="logout.php">Logout</a> <!-- link to logout the admin session -->

    <h2>Contact Form Messages</h2>
    <table border="1">
        <thead>
            <tr>
                <!-- table headings -->
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?> <!-- Loop to iterate through each row of the result set -->
                <tr>
                    <!-- output each column of the current row -->
                    <td><?php echo $row['id']; ?></td> <!-- ID of the message -->
                    <td><?php echo $row['name']; ?></td> <!-- the name of the sender -->
                    <td><?php echo $row['email']; ?></td> <!-- the email of the sender -->
                    <td><?php echo $row['phone']; ?></td> <!-- the phone number of the sender -->
                    <td><?php echo $row['message']; ?></td> <!-- the message content -->
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
