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
    ?>
    
    
    
    <!-- main content section for the contact page -->
    <!--<main class="contact-page">
        <div class="contact-image">
            <img src="pics/contact.jpg" alt="">
        </div>
        <div class="contact-text">
            <h3>We're here for you!</h3>
            <p>Monday - Friday</p>
            <p>9am - 5pm</p>
            <p>Saturdays 10am - 3pm</p>
        </div>
        </main>-->
    <form id="contact-form" method="POST" action="contact-page.php">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2>Contact Us</h2>
                        <div class="form-group">
                            <label for="name">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Your Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Your Phone</label>
                            <input type="phone" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
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
            {            // define your admin email
                $admin_email = "movchan710@gmail.com";
                    
                // get form data
                $name = validate_form_input($_POST["name"]);
                $email = validate_form_input($_POST["email"]);
                $phone = validate_form_input($_POST["phone"]); 
                $message = validate_form_input($_POST["message"]);
                $message = wordwrap($message, 70);
                    
                // validation
                if (empty($name) || empty($email) || empty($phone) || empty($message)) 
                { 
                    echo "Please fill in all fields.";
                } 
                else 
                {
                    // send email to admin
                    $subject = "New message from Contact Form";
                    $body = "Name: $name\nEmail: $email\nPhone: $phone\n\n$message"; 
                    sendMessageToDB($db_connection, $email, $phone, $message);
                    
                    if (mail($admin_email, $subject, $body)) 
                    {
                        showSuccess("Message sent successfully. Thank you!");
                    } 
                    else 
                    {
                        showError("Oops! Something went wrong. Please try again later");
                    }
                }

            }
        }

        function sendMessageToDB($db_connection, $senderEmail, $senderPhone, $senderMessage)
        {
            //sql query used to add data to DB
            $sql = "INSERT INTO contact_message (sender_email, sender_phone, sender_message)
            VALUES('$senderEmail', '$senderPhone', '$senderMessage')";

            //call sql compilation
            if(mysqli_query($db_connection, $sql))
            {
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