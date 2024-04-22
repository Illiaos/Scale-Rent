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
        <title>User Registration</title>
    </head>
    <body>
        <div class="container-md w-50 p-3">
            <h1 class="mt-4 mb-4">User Registration Form</h1>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <!-- block for appliacne selection type -->
                <div class="form-group">
                    <label class="font-weight-bold">User Type</label>
                    <select id="userType" name="userType" class="form-control">
                        <?php
                            $applianceOptions = array("", "Tenant", "Landlord");
                            foreach ($applianceOptions as $option) 
                            {
                                $selected = ($applianceType == $option) ? 'selected' : '';
                                echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <!-- block for a Brand input -->
                <div class="form-group">
                    <label class="font-weight-bold">User Name</label>
                    <input type="text" id="userName" name="userName" class="form-control" placeholder="Enter User Name"
                    value="<?php echo $brand; ?>">
                </div>

                <!-- block for a Model input -->
                <div class="form-group">
                    <label class="font-weight-bold">User Surname</label>
                    <input type="text" id="userSurname" name="userSurname" class="form-control" placeholder="Enter User Surname"
                    value="<?php echo $model; ?>">
                </div>

                <!-- block for a Serial Number input -->
                <div class="form-group">
                    <label class="font-weight-bold">User Email</label>
                    <input type="text" id="userEmail" name="userEmail" class="form-control" placeholder="Enter User Email"
                    value="<?php echo $serial; ?>">
                </div>

                <!-- block for a Purchase Date selection -->
                <div class="form-group">
                    <label class="font-weight-bold">User Password</label>
                    <input type="text" id="userPassword" name="userPassword" class="form-control" placeholder="Enter Password"
                    value="<?php echo $purchaseDate; ?>">
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Repeat User Password</label>
                    <input type="text" id="userPasswordRepeat" name="userPasswordRepeat" class="form-control" placeholder="Re-Enter Password"
                    value="<?php echo $purchaseDate; ?>">
                </div>
                <button type="submit" value="Submit" class="btn btn-primary p-3">Register User</button>
            </form>
        </div>

        <?php
            //define server path
            if ($_SERVER['SERVER_NAME'] == 'knuth.griffith.ie')
            {
                $path_to_mysql_connect = '../../../../mysql_connect.php';
            }
            else
            {
                $path_to_mysql_connect = '../../../mysql_connect.php';
            }
        
            //connect to DB
            require ($path_to_mysql_connect);

            if($_SERVER['REQUEST_METHOD'] == 'GET')
            {
                if(isset($_GET['userExist']))
                {
                    //redirect to log in page
                    //header('Location: addUser.php');
                    mysqli_close($db_connection);
                    exit();
                }
                else if(isset($_GET['loadHomePage']))
                {
                    header('Location: index.php');
                    mysqli_close($db_connection);
                    exit();
                }
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                //assign values, from POST
                $userType = validate_form_input($_POST['userType'] ?? '');
                $userName = validate_form_input($_POST['userName'] ?? '');
                $userSurname = validate_form_input($_POST['userSurname'] ?? '');
                $userEmail = validate_form_input($_POST['userEmail'] ?? '');
                $userPassword = validate_form_input($_POST['userPassword'] ?? '');
                $userPasswordRepeat = validate_form_input($_POST['userPasswordRepeat'] ?? '');

                //call method to check the correctness of user input
                if(checkUserInput($userType, $userName, $userSurname, $userEmail, $userPassword, $userPasswordRepeat) == true)
                {
                    //check if user exist
                    if(checkIfUserExist($db_connection, $userEmail) == true)
                    {
                        //call header to send a raw HTTP header to client, curently to the same script, and pass value userExist
                        header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?userExist=true");
                        //stop script execution
                        exit();
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
                //sql query used to add data to DB
                $sql = "INSERT INTO appliance (UserID, ApplianceType, Brand, ModelNumber, SerialNumber, PurchaseDate, WarrantyExpirationDate, CostOfAppliance)
                VALUES('$userID', '$applianceType', '$brand', '$model', '$serial', '$purchaseDate', '$warantyExpirationDate', '$costOfAppliance')";

                //call sql compilation
                if(mysqli_query($db_connection, $sql))
                {
                    //call header to send a raw HTTP header to client, curently to the same script, and pass value applianceAdded
                    header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']) . "?applianceAdded=true");
                    //stop script execution
                    exit();
                }
                else
                {
                    //show error, if sql execution failed
                    showError(mysqli_error($db_connection));
                }
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
                    <form action="addAppliance.php" method="GET">
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

            //method used to render a view button, which will call GET, and send value for applianceID
            function showRedirectToViewPageById($applianceID)
            {
                echo
                ('
                    <div class="container-md w-50 p-3">
                        <form action="addAppliance.php" method="GET">
                            <button type="submit" value="'.$applianceID.'" name="loadViewPageByID" class="btn btn-primary p-3">View Page</button>
                        </form>
                    </div>
                ');
            }

            //method used to render a view button, which will call GET, and send value for serialNumber
            function showRedirectToViewPageBySerialNumber($serial)
            {
                echo
                ('
                    <div class="container-md w-50 p-3">
                        <form action="addAppliance.php" method="GET">
                            <button type="submit" value="'.$serial.'" name="loadViewPageBySerialNumber" class="btn btn-primary p-3">View Page</button>
                        </form>
                    </div>
                ');
            }

            //method used to render a addUser button, which will call GET
            function showRedirectToAddUserPage()
            {
                echo
                ('
                    <div class="container-md w-50 p-3">
                        <form action="addAppliance.php" method="GET">
                            <button type="submit" value="" name="loadAddUserPage" class="btn btn-primary p-3">Add User Page</button>
                        </form>
                    </div>
                ');
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
                        <form action="addAppliance.php" method="GET">
                            <button type="submit" value="Submit" name="loadHomePage" class="btn btn-primary p-3">Home Page</button>
                        </form>
                    </div>
                ');
            }
        ?>
    </body>
</html>