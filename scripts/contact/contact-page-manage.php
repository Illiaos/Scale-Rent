<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- connect bootstrap libraries -->
    <link href="../../bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../bootstrap/assets/js/vendor/jquery-slim.min.js"></script>
    <script src="../../bootstrap/assets/js/vendor/popper.min.js"></script>
    <script src="../../bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../style.css">
    <script src="script.js"></script>
    <title>Nassdam Solid Ventures</title>
</head>
<body>
    <?php 
        include('../header/header.php');
        //define server path
        if ($_SERVER['SERVER_NAME'] == 'knuth.griffith.ie')
        {
            $path_to_mysql_connect = '../../../../../mysql_connect.php';
        }
        else
        {
            $path_to_mysql_connect = '../../../../mysql_connect.php';
        }

        //connect to DB
        require ($path_to_mysql_connect);

        if(isset($_GET['message_id']))
        {
            deleteMessage($db_connection, $_GET['message_id']);
        }

        function deleteMessage($db_connection, $message_id)
        {
            $sql = "DELETE FROM contact_message WHERE message_id = $message_id";
            $db_connection->query($sql);
        }
    ?>

    <form action="contact-page-manage.php" method="GET">
    <div class="contact-form">
    <?php               
        if ($_SERVER["REQUEST_METHOD"] == "GET") 
        {
            showMessages($db_connection);
        }

        function showMessages($db_connection)
        {
            $stmt = $db_connection->prepare("SELECT * FROM contact_message");
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            echo
            (
                '
                <div class="container mt-5">
                    <h2>Contact Messages</h2>
                    <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Sender Email</th>
                            <th scope="col">Sender Phone</th>
                            <th scope="col">Sender Message</th>
                            <th scope="col">Delete Action</th>
                    </tr>
                </thead>
                <tbody>
                '
            );

            while($row = $result->fetch_assoc())
            {
                echo
                (
                    '
                    <tr>
                        <td>'.$row['sender_email'].'</td> <!-- ID of the message -->
                        <td>'.$row['sender_phone'].'</td> <!-- the name of the sender -->
                        <td>'.$row['sender_message'].'</td> <!-- the email of the sender -->
                        <td> <button type="submit" name="message_id" id="message_id" value="'.$row["message_id"].'" class="btn btn-primary">Delete Message</button>
                    </tr>
                    '
                );
            }

            echo
            (
                '
                    </tbody>
                </table>
                </div>
                '
            );
        }

        //method used to show error message which is passed as a parameter
        function showError($errorMessage)
        {
            echo
            ('
                <div class="alert alert-danger alert-dismissible fade show w-50 p-3">
                    <strong>Error! </strong>' 
                    . $errorMessage.
                '</div>
            ');
        }

        function showSingleError($errorMessage)
        {
            echo ('<div class="container-md w-50 p-3">');
            showError($errorMessage);
            echo ('</div>');
        }

        //method used to show Home Button
        function showSuccess($message)
        {
            //button used to return to the Home Page, which is house.html
            echo
            ('
                <div class="container-md w-50 p-3">
                    <div class="alert alert-success alert-dismissible fade show w-50 p-3">
                        <strong>Success! </strong>'
                        . $message .
                    '</div>
            ');
        }
    ?>
    </div>
    </form>
</body>
</html>