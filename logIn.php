<!--Illia Movchan 3098121 -->
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- connect bootstrap libraries -->
        <link href="../../bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="../../bootstrap/assets/js/vendor/jquery-slim.min.js"></script>
        <script src="../../bootstrap/assets/js/vendor/popper.min.js"></script>
        <script src="../../bootstrap/dist/js/bootstrap.min.js"></script>
        <style>
            body {
    margin: 0;
    padding: 0;
    font-family: Georgia, 'Times New Roman', Times, serif;
    background-color: #f4f4f4;
}

.login-container {
    width: 40%;
    margin: 100px auto;
    background-color: #fff;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #085F63;
}

.input-group {
    margin-bottom: 20px;
    padding-left: 4rem;
}

.input-group label {
    display: block;
    margin-bottom: .5rem;
    color: #085F63;
}

.input-group input {
    width: 80%;
    padding: 10px;
    border: 1px solid #085F63;
    border-radius: 5px;
}

button {
    width: 50%;
    padding: 10px;
    border: none;
    background-color: #085F63;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
    display: block;
    margin-right: auto;
    margin-left: auto;
}

button:hover {
    background-color: #49BEB7;
}
        </style>
        <title>Log In</title>
    </head>
    <body>
        <?php 
            include('../header/header.php');
        ?>
        <div class="login-container">
        <h2>Login</h2>
        <form action="logIn.php" method="POST">
            <div class="input-group">
                <label for="username">Email</label>
                <input type="text" id="userEmail" name="userEmail" class="form-control" placeholder="Enter Email"
                    value="<?php if(isset($_POST['userEmail'])) echo $_POST['userEmail']; ?>">
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="text" id="userPassword" name="userPassword" class="form-control" placeholder="Enter Password"
                value="<?php if(isset($_POST['userPassword'])) echo $_POST['userPassword']; ?>">
            </div>
            <button type="submit" value="submit">Login</button>
        </form>
        <p>If you don't have an account,click <a href="registration.php">here</a> to sign up.</p>
    </div>
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

                if ($_SERVER['REQUEST_METHOD'] == 'GET')
                {
                    if(isset($_GET['loadHomePage']))
                    {
                        //reqirect to the home page
                        header('Location: ../../index.php');
                        //close sql
                        mysqli_close($db_connection);
                        //stop script execution
                        exit();
                    }
                    else if(isset($_GET['loadRegistrationPage']))
                    {
                        header('Location: ../registration/registration.php');
                        //close sql
                        mysqli_close($db_connection);
                        //stop script execution
                        exit();
                    }
                }

                // checking to see if the server has received a POST request.
                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    //assign user input from POST
                    $userEmail = validate_form_input($_POST['userEmail']);
                    $userPassword = validate_form_input($_POST['userPassword']);

                    //check correctness of user input
                    $input = validateInput($userEmail, $userPassword);

                    if($input == false) return;

                    if(checkUserLogInData($db_connection, $userEmail, $userPassword))
                    {
                        header('Location: ../../index.php');
                        //close sql
                        mysqli_close($db_connection);
                        //stop script execution
                        exit();
                    }
                    else
                    {
                        echo("ENTER");
                        showSingleError("Wrong Email or Password");
                    }
                }

                //check correctness of user input
                function validateInput($userEmail, $userPassword)
                {
                    //patter for email
                    $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

                    $errors = array();
                    
                    //check if email is entered
                    if(empty($userEmail))
                    {
                        array_push($errors, "User Email is not entered");
                    }
                    else if(!preg_match($emailPattern, $userEmail)) //check if email math pattern
                    {
                        array_push($errors, "User Email Address is in wrong format");
                    }

                    if(empty($userPassword))
                    {
                        array_push($errors, "User Password is not entered");
                    }
                    
                    //if the array with errors is not empty, go through the $errors array and illustrate it
                    if (!empty($errors)) 
                    {
                        echo
                        ('<div class="container-md w-50 p-3">');
                        foreach($errors as $item)
                        {
                            //call method to render error, which is passed as a parameter
                            showError($item);
                        }
                        //render home button
                        showHomeButton();
                        showRegistrationButton();
                        echo('</div>');
                        return false;
                    }
                    return true;
                }

                function checkUserLogInData($db_connection, $userEmail, $userPassword) : bool
                {
                    $stmt = $db_connection->prepare("SELECT * FROM user WHERE email=?");
                    $stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();

                    if($result->num_rows == 0)
                    {
                        return false;
                    }

                    $userData = $result->fetch_assoc();
                    $passwordFromDB =  $userData['password'];
                    
                    if (password_verify($userPassword, $passwordFromDB)) 
                    {
                        $userType = getUserTypeFromDB($db_connection, $userData['type_id']);
                        setcookie("userLevel", $userType, time() + (86400 * 30), "/");
                        setcookie("userID", $userData['user_id'], time() + (86400 * 30), "/");
                        return true;
                    } 
                    return false;
                }

                function getUserTypeFromDB($db_connection, $typeId) : string
                {
                    $stmt = $db_connection->prepare("SELECT * FROM user_type WHERE type_id=?");
                    $stmt->bind_param("s", $typeId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc()['type'];
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
                    showHomeButton();
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
                    showHomeButton();
                    echo('<br />');
                    showRegistrationButton();
                    echo('</div>');
                }

                function showHomeButton()
                {
                    echo
                    ('
                        <form action="logIn.php" method="GET">
                            <button type="submit" value="Submit" name="loadHomePage" class="btn btn-primary p-3">Home Page</button>
                        </form>
                    ');
                }

                function showRegistrationButton()
                {
                    echo
                    ('
                        <form action="logIn.php" method="GET">
                            <button type="submit" value="Submit" name="loadRegistrationPage" class="btn btn-primary p-3">Registration Page</button>
                        </form>
                    ');
                }
            ?>
    </body>
</html>