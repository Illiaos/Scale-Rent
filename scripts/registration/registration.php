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
        <link rel="stylesheet" href="log.css"> 
        <link rel="stylesheet" href="registration_style.css"> 
        <title>User Registration</title>
    </head>
    <body>
    <div class="registration-container">
        <h2>Register</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group">
                <label for="usertype">User Type</label>
                <select id="userType" name="userType" class="form-control">
                    <?php
                        $applianceOptions = array("", "Tenant", "Landlord");
                        foreach ($applianceOptions as $option) 
                        {
                            $selected = ($_POST["userType"] == $option) ? 'selected' : '';
                            echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="input-group">
                <label for="name">FirstName</label>
                <input type="text" id="userName" name="userName" placeholder="Enter firstname" value="<?php if(isset($_POST['userName'])) echo $_POST['userName']; ?>">
            </div>
            <div class="input-group">
                <label for="surname">Surname</label>
                <input type="text" id="userSurname" name="userSurname" placeholder="Enter User Surname"
                    value="<?php if(isset($_POST['userSurname'])) echo $_POST['userSurname']; ?>">
            </div>
            <div class="input-group">
                <label>User Email</label>
                <input type="text" id="userEmail" name="userEmail"  placeholder="Enter User Email"
                value="<?php if(isset($_POST['userEmail'])) echo $_POST['userEmail']; ?>">
            </div>
            <div class="input-group">
                <label>User Password</label>
                    <input type="text" id="userPassword" name="userPassword" placeholder="Enter Password"
                    value="<?php if(isset($_POST['userPassword'])) echo $_POST['userPassword']; ?>">
            </div>
            <div class="input-group">
                <label>Repeat User Password</label>
                    <input type="text" id="userPasswordRepeat" name="userPasswordRepeat"  placeholder="Re-Enter Password"
                    value="<?php if(isset($_POST['userPasswordRepeat'])) echo $_POST['userPasswordRepeat']; ?>">
            </div>
            <button type="submit" value="Submit">Register User</button>
        </form>
        <p>Already have an account? <a href="login.php">Log in here</a>.</p>
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

            if($_SERVER['REQUEST_METHOD'] == 'GET')
            {
                if(isset($_GET['loadHomePage']))
                {
                    header('Location: ../../index.php');
                    mysqli_close($db_connection);
                    exit();
                }
                else if(isset($_GET['userRegistered']))
                {
                    //header('Location: index.php');
                    //mysqli_close($db_connection);
                    //exit();
                    showSuccess("User Registered");
                }
                else if(isset($_GET['loadLogInPage']))
                {
                    header('Location: ../log_in/logIn.php');
                    mysqli_close($db_connection);
                    exit();
                }
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                //assign values, from POST
                $userType = validate_form_input($_POST['userType']);
                $userName = validate_form_input($_POST['userName']);
                $userSurname = validate_form_input($_POST['userSurname']);
                $userEmail = validate_form_input($_POST['userEmail']);
                $userPassword = validate_form_input($_POST['userPassword']);
                $userPasswordRepeat = validate_form_input($_POST['userPasswordRepeat']);

                //call method to check the correctness of user input
                if(checkUserInput($userType, $userName, $userSurname, $userEmail, $userPassword, $userPasswordRepeat) == true)
                {
                    //check if user exist
                    if(checkIfUserExist($db_connection, $userEmail) == true)
                    {
                        showSingleError("User with email already exist");
                        //call header to send a raw HTTP header to client, curently to the same script, and pass value userExist
                        //header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?userExist=true");
                        //stop script execution
                        //exit();
                    }
                    //in case all errors check are passed, call method that will add data to the DB
                    else
                    {
                        addToDB($db_connection, $userType, $userName, $userSurname, $userEmail, $userPassword);
                    }
                }
            } 
            
            //method which is used to add data to DB, as a parameter get a connection to a DB and needed data
            function addToDB($db_connection, $userType, $userName, $userSurname, $userEmail, $userPassword)
            {
                $userPassword = password_hash($userPassword, PASSWORD_BCRYPT);
                $userTypeForeignKey = getTypeIdForeignKey($db_connection, $userType);
                //sql query used to add data to DB
                $sql = "INSERT INTO user (type_id, name, surname, email, password)
                VALUES('$userTypeForeignKey', '$userName', '$userSurname', '$userEmail', '$userPassword')";

                //call sql compilation
                if(mysqli_query($db_connection, $sql))
                {
                    //call header to send a raw HTTP header to client, curently to the same script, and pass value user registered
                    header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?userRegistered=true");
                    //stop script execution
                    exit();
                }
                else
                {
                    //show error, if sql execution failed
                    showError(mysqli_error($db_connection));
                }
            }

            function getTypeIdForeignKey($db_connection, $userType)
            {
                //define sql query, seek by id for user type
                $stmt = $db_connection->prepare("SELECT * FROM user_type WHERE type=?");
                $stmt->bind_param("s", $userType);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                return $result->fetch_assoc()['type_id'];
            }

            //method used to check if user exist
            function checkIfUserExist($db_connection, $userEmail)
            {     
                //sql query used to check if user with email already exist
                $sql = "SELECT * FROM user WHERE email='$userEmail'";
                //execute sql
                $result = mysqli_query($db_connection, $sql);
                if(mysqli_num_rows($result) == 0)
                {
                    //clear sql result
                    mysqli_free_result($result);
                    return false;
                }
                //clear sql result
                mysqli_free_result($result);
                return true;
            }

            //method used to check user input from fields
            function checkUserInput($userType, $userName, $userSurname, $userEmail, $userPassword, $userPasswordRepeat) : bool
            {
                //patter for email                
                $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
                
                $errors = array(); 

                if(empty($userType))
                {
                    array_push($errors, "User Type is not selected");
                }

                if(empty($userName))
                {
                    array_push($errors, "User Name is not entered");
                }

                if(empty($userSurname))
                {
                    array_push($errors, "User Surname is not entered");
                }

                $errors =  array_merge($errors, checkPasswordStrength($userPassword, $userPasswordRepeat));

                //check if email is entered
                if(empty($userEmail))
                {
                    array_push($errors, "User Email is not entered");
                }
                else if(!preg_match($emailPattern, $userEmail)) //check if email math pattern
                {
                    array_push($errors, "User Email Address is in wrong format");
                }
           
                //if the array with errors is not empty, go through the $errors array and illustrate it
                if (!empty($errors)) 
                {
                    echo('<div class="container-md w-50 p-3">');
                    foreach($errors as $item)
                    {
                        //call method to render error, which is passed as a parameter
                        showError($item);
                    }
                    //call method to render home button
                    showHomeButton();
                    echo('</div>');
                    return false;
                }
                return true;                
            }

            function checkPasswordStrength($password, $rePassword) : array
            {
                $errors = array(); 

                $uppercase = preg_match('@[A-Z]@', $password);
                $lowercase = preg_match('@[a-z]@', $password);
                $number    = preg_match('@[0-9]@', $password);
                $specialChars = preg_match('@[^\w]@', $password);

                if(strlen($password) < 8)
                {
                    array_push($errors, "Length of password need to be at least 8 digits");
                }
            
                if(!$uppercase)
                {
                    array_push($errors, "You need to have at least one Uppercase letter");
                }
                
                if(!$lowercase)
                {
                    array_push($errors, "You need to have at least one Lowercase letter");
                }

                if(!$number)
                {
                    array_push($errors, "You need to have at least one digit");
                }

                if(!$specialChars)
                {
                    array_push($errors, "You need to have at least one special character");
                }

                if(strcmp($password, $rePassword) != 0)
                {
                    array_push($errors, "Password do not match");
                }

                return $errors;
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

            //method to render home button
            function showHomeButton()
            {
                echo ('
                    <form action="registration.php" method="GET">
                        <button type="submit" value="Submit" name="loadHomePage" class="btn btn-primary p-3">Home Page</button>
                    </form>
                ');
            }

            //method used to render single error, used when it is planning to show only one button
            function showSingleError($errorMessage)
            {
                echo ('<div class="container-md w-50 p-3">');
                showError($errorMessage);
                showHomeButton();
                echo ('</div>');
            }

            //method used render success element
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
                        <form action="registration.php" method="GET">
                            <button type="submit" value="Submit" name="loadHomePage" class="btn btn-primary p-3">Home Page</button>
                        </form>
                        <br />
                        <form action="registration.php" method="GET">
                            <button type="submit" value="Submit" name="loadLogInPage" class="btn btn-primary p-3">LogIn Page</button>
                        </form>
                    </div>
                ');
            }
        ?>
    </body>
</html>