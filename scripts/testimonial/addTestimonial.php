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

    <style>
    .rating {
        unicode-bidi: bidi-override;
        direction: rtl;
        display: inline-block;
    }
    .rating > input {
        display: none;
    }
    .rating > label {
        font-size: 25px;
        padding: 0 3px;
        float: right;
        color: #ccc;
    }
    .rating > label:before {
        content: '\2605';
        padding: 5px;
        font-size: 30px;
        cursor: pointer;
    }
    .rating > input:checked ~ label,
    .rating > input:checked ~ label:before {
        color: #ffca08;
    }
</style>

</head>
<body>
    <?php 
        include('../header/header.php');
    ?>

    <form id="contact-form" method="POST" action="addTestimonial.php">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2>Testimonial Form</h2>
                        <div class="form-group">
                            <label for="name">Service Name</label>
                            <input type="text" class="form-control" id="serviceName" name="serviceName" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>


                        <div class="container">
                            <h4>Rate from 0 to 5 using Stars</h4>
                            <div class="form-group">
                                <div class="rating">
                                    <input type="radio" id="star5" name="rating" value="5"><label for="star5"></label>
                                    <input type="radio" id="star4" name="rating" value="4"><label for="star4"></label>
                                    <input type="radio" id="star3" name="rating" value="3"><label for="star3"></label>
                                    <input type="radio" id="star2" name="rating" value="2"><label for="star2"></label>
                                    <input type="radio" id="star1" name="rating" value="1"><label for="star1"></label>
                                    <input type="radio" id="star0" name="rating" value="0"><label for="star0"></label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="sendMessage" name="sendMessage" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </form>

    <div class="contact-form">
    <?php               
        //define server path
        if ($_SERVER['SERVER_NAME'] == 'knuth.griffith.ie')
        {
            $path_to_mysql_connect = '../../../../mysql_connect.php';
        }
        else
        {
            $path_to_mysql_connect = '../../../../mysql_connect.php';
        }

        //connect to DB
        require ($path_to_mysql_connect);
    
        // check if form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
            if(isset($_POST['sendMessage']))
            {           
                $userID = $_COOKIE['userID'];
                $userData = getUserData($db_connection, $userID);
                $userName = $userData['name'];
                $userSurname = $userData['surname'];
                $message = $_POST['message'];
                $rate = 0;
                if(isset($_POST['rating']))
                {
                    $rate = $_POST['rating'];
                }
                $date = date("Y-m-d"); 
                $serviceName = $_POST['serviceName'];
                $approved = false;
                $writerName = $userName . " " . $userSurname;
                // validation
                if (empty($serviceName) || empty($message)) 
                { 
                    showError("Please fill in all fields.");
                } 
                else 
                {
                    sendMessageToDB($db_connection, $message, $rate, $date, $serviceName, $approved, $writerName);
                }
            }
        }

        function getUserData($db_connection, $userID)
        {
            $stmt = $db_connection->prepare("SELECT * FROM user_ass03 WHERE user_ass03.user_id=".$userID);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result->fetch_assoc();
        }

        function sendMessageToDB($db_connection, $message, $rate, $date, $serviceName, $approved, $writerName)
        {
            //sql query used to add data to DB
            $sql = "INSERT INTO testimonial (description, rate, date, service_name, approved, writter_name)
            VALUES('$message', '$rate', '$date', '$serviceName', '$approved' , '$writerName')";

            //call sql compilation
            if(mysqli_query($db_connection, $sql))
            {
                showSuccess("Testimonial Send");
                //call header to send a raw HTTP header to client, curently to the same script, and pass value user registered
                //header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
                //stop script execution
                //exit();
            }
            else
            {
                //show error, if sql execution failed
                showError(mysqli_error($db_connection));
            }
        }

        //method to validate user input
        function validate_form_input($input)
        {
            //remove spaces at hte beggining and end
            $input = trim($input);
            //repace special symbols
            $input = str_replace("\r", "", $input);
            $input = str_replace("\n", "", $input);
            $input = htmlentities($input);
            $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); 
            return $input;
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
</body>
</html>