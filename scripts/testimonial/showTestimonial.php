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
    ?>

    <form action="showTestimonial.php" method="GET">
    <div class="contact-form">
    <?php               
        if ($_SERVER["REQUEST_METHOD"] == "GET") 
        {
            showMessages($db_connection);
        }

        function showMessages($db_connection)
        {
            $stmt = $db_connection->prepare("SELECT * FROM testimonial WHERE testimonial.approved=true");
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();

            echo
            (
                '
                <div class="container mt-5">
                    <h2>Testimonials</h2>
                    <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Writter</th>
                            <th scope="col">Service Name</th>
                            <th scope="col">Publication Date</th>
                            <th scope="col">Rate</th>
                            <th scope="col">Description</th>
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
                        <td>'.$row['writter_name'].'</td> <!-- ID of the message -->
                        <td>'.$row['service_name'].'</td> <!-- the name of the sender -->
                        <td>'.$row['date'].'</td> <!-- the email of the sender -->
                        <td>'.$row['rate'].'</td> <!-- the email of the sender -->
                        <td>'.$row['description'].'</td> <!-- the email of the sender -->
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
    ?>
    </div>
    </form>
</body>
</html>