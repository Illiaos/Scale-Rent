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
        <link rel="stylesheet" href="../style.css">
        <title>Password Recover</title>
    </head>
    <body>
        <div class="container-md w-50 p-3">
            <h1 class="mt-4 mb-4">Password Recover Form</h1>

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
                showFormForUserSearch();
                if(isset($_GET['loadHomePage']))
                {
                    header('Location: ../../index.php');
                    mysqli_close($db_connection);
                    exit();
                }
                else if(isset($_GET['loadLogInPage']))
                {
                    header('Location: ../log_in/logIn.php');
                    mysqli_close($db_connection);
                    exit();
                }
            }

            if ($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                if(isset($_POST['userEmail']))
                {
                    $userEmail = validate_form_input($_POST['userEmail']);
                    $errors = checkUserEmailInput($userEmail); 
                    
                    if(empty($errors) == true)
                    {
                        if(checkIfUserExist($db_connection, $userEmail) == true)
                        {
                            setcookie("userChangePasswordEmail", $userEmail);
                            showFormForUserRecover();
                        }
                        else
                        {
                            showFormForUserSearch();
                            showSingleError("User do not exist");
                        }
                    }
                    else
                    {
                        showFormForUserSearch();
                        showErrorsArray($errors);
                    }
                }
                else if(isset($_POST['changePassword']))
                {
                    $userPassword = validate_form_input($_POST['userPassword']);
                    $userRePassword = validate_form_input($_POST['userRePassword']);
                    $userEmail = validate_form_input($_COOKIE['userChangePasswordEmail']);

                    $errors = checkPasswordStrength($userPassword, $userRePassword);
                    if(empty($errors) == true)
                    {
                        updatePassword($db_connection, $userEmail, $userPassword);
                    }
                    else
                    {
                        showFormForUserRecover();
                        showErrorsArray($errors);
                    }
                }
            }
            
            function showFormForUserSearch()
            {
                $userEmail = "";
                if(isset($_POST['userEmail']))
                {
                    $userEmail = $_POST['userEmail'];
                }
                echo ('
                    <form action="passwordRecover.php" method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold">User Name</label>
                            <input type="text" id="userEmail" name="userEmail" class="form-control" placeholder="Enter User Email"
                            value="'.$userEmail.'">
                        </div>
                        <button type="submit" name="userSearchValue" value="" class="btn btn-primary p-3">Find User</button>
                    </form>
                ');
            }

            function showFormForUserRecover()
            {
                $userPassword = $userRePassword = "";
                if(isset($_POST['userPassword']))
                {
                    $userPassword = $_POST['userPassword'];
                }

                if(isset($_POST['userRePassword']))
                {
                    $userRePassword = $_POST['userRePassword'];
                }

                echo ('
                    <form action="passwordRecover.php" method="POST">
                        <div class="form-group">
                            <label class="font-weight-bold">User New Password</label>
                            <input type="text" id="userPassword" name="userPassword" class="form-control" placeholder="Enter New Password"
                            value="'.$userPassword.'">
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">User New Re-Password</label>
                            <input type="text" id="userRePassword" name="userRePassword" class="form-control" placeholder="Enter New Re-Password"
                            value="'.$userRePassword.'">
                        </div>
                        <button type="submit" name="changePassword" value="" class="btn btn-primary p-3">Change Password</button>
                    </form>
                ');
            }
            
            function updatePassword($db_connection, $userEmail, $password)
            {
                $password = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $db_connection->prepare("UPDATE user SET password = ? where email = ?");
                $stmt->bind_param("ss", $password, $userEmail);
                if($stmt->execute())
                {
                    showSuccess("Data Updated");
                }
                else
                {
                    showError("Data Not Updated");
                }
                $stmt->close();
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

            function checkUserEmailInput($userEmail) : array
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
                return $errors;
            }

            function showErrorsArray($errors)
            {
                echo("<br />");
                //if the array with errors is not empty, go through the $errors array and illustrate it
                if (!empty($errors)) 
                {
                    foreach($errors as $item)
                    {
                        //call method to render error, which is passed as a parameter
                        showError($item);
                    }
                    //call method to render home button
                    showHomeButton();
                }
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
                    <form action="passwordRecover.php" method="GET">
                        <button type="submit" value="Submit" name="loadHomePage" class="btn btn-primary p-3">Home Page</button>
                    </form>
                ');
            }

            //method used to render single error, used when it is planning to show only one button
            function showSingleError($errorMessage)
            {
                showError($errorMessage);
                showHomeButton();
            }

            //method used render success element
            function showSuccess($message)
            {
                //button used to return to the Home Page, which is house.html
                echo
                ('
                        <div class="alert alert-success alert-dismissible fade show w-50 p-3">
                            <strong>Success! </strong>'
                            . $message .
                        '</div>
                        <form action="passwordRecover.php" method="GET">
                            <button type="submit" name="loadHomePage" class="btn btn-primary p-3">Home Page</button>
                        </form>
                        <br />
                        <form action="passwordRecover.php" method="GET">
                            <button type="submit" name="loadLogInPage" class="btn btn-primary p-3">LogIn Page</button>
                        </form>
                ');
            }
        ?>
        </div>
    </body>
</html>