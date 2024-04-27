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
        <link rel="stylesheet" href="../../style.css">
        <title>Rent</title>
    </head>
    <body>
        <?php
            include('../header/header.php');
        ?>
        <div class="container-md w-50 p-3">
            <h1 class="mt-4 mb-4">Rent Property Confirmation</h1>
                <form action="rentProperty.php" method="POST" enctype="multipart/form-data">
                
                    <div class="form-group">
                        <label class="font-weight-bold">Upload Agreement</label>
                        <input type="file" name="pdf_file" id="uploadAgreementFile" accept=".pdf" class="upload" class="form-control">
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Select Start Rent Date</label>
                        <input type="date" id="startRentDate" name="startRentDate" class="form-control" placeholder="Enter Start Rent Date">
                    </div>

                    <button type="submit" name="rentCall" class="btn btn-primary p-3">Confirm Rent</button>
                </form>
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
                    $property_id = $_GET['property_id'];
                    if(isset($_COOKIE['confirmPropertyID']))
                    {
                        unset($_COOKIE['confirmPropertyID']);
                        setcookie("confirmPropertyID", $property_id, time() + (86400 * 30), "/");
                    }
                    else
                    {
                        setcookie("confirmPropertyID", $property_id, time() + (86400 * 30), "/");
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    if(isset($_POST['rentCall']))
                    {                
                        $property_id = $_COOKIE['confirmPropertyID'];
                        $user_id = $_COOKIE['userID'];
                        if(checkIfAlreadyRent($db_connection, $property_id) == true)
                        {
                            showError("Property Already Registered");
                            return;
                        }
                        $agreement = loadAgreement($property_id, $user_id);
                        $start_date = "";
                        if(isset($_POST['startRentDate']))
                        {
                            $start_date = $_POST['startRentDate'];
                        }
                        else
                        {
                            showError("Start Rent Data is not selected");
                            return;
                        }
                        $end_date = new DateTime($start_date);
                        $numberOfMonthsToAdd = getEndRentDate($db_connection, $property_id);
                        // Create a DateInterval object to represent the interval to add
                        $dateInterval = new DateInterval('P' . $numberOfMonthsToAdd . 'M');
                        // Add the interval to the DateTime object
                        $end_date->add($dateInterval);
                        // Format the resulting date as a string
                        $end_date = $end_date->format('Y-m-d');
                        $price_per_month = (int)getEndRentPrice($db_connection, $property_id);
                        $price = $price_per_month * (int)$numberOfMonthsToAdd;
                        addRentToTentDB($db_connection, $user_id, $property_id, $agreement, $start_date, $end_date, 0, $price);
                        updatePropertyInDB($db_connection, $property_id);
                        showSuccess("Rent Succesful");
                    }
                }

                function checkIfAlreadyRent($db_connection, $property_id)
                {
                    $stmt = $db_connection->prepare("SELECT * FROM property WHERE isRent=true");
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();

                    if($result->num_rows == 0)
                    {
                        false;
                    }
                    return true;
                }
                
                function addRentToTentDB($db_connection, $user_id, $property_id, $agreement, $start_date, $end_date, $paid, $owed)
                {
                    //sql query that adds data to DB
                    $sql = "INSERT INTO tenant_account (user_id, property_id, agreement, start_date, end_date, paid, owed) 
                    VALUES ('$user_id', '$property_id', '$agreement', '$start_date', '$end_date', '$paid', '$owed')";
                                    
                    $result = mysqli_query($db_connection, $sql);
                    return $result;
                }

                function getEndRentDate($db_connection, $property_id) : string
                {
                    $stmt = $db_connection->prepare("SELECT * FROM property WHERE property_id=?");
                    $stmt->bind_param("s", $property_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();

                    if($result->num_rows == 0)
                    {
                        return "";
                    }
                    return $result->fetch_assoc()['rent_period'];
                }

                function getEndRentPrice($db_connection, $property_id) : string
                {
                    $stmt = $db_connection->prepare("SELECT * FROM property WHERE property_id=?");
                    $stmt->bind_param("s", $property_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();

                    if($result->num_rows == 0)
                    {
                        return "";
                    }
                    return $result->fetch_assoc()['price'];
                }

                function updatePropertyInDB($db_connection, $propertyID)
                {
                    //slq query to update data
                    $stmt = $db_connection->prepare("UPDATE property SET isRent = ? WHERE property_id = ?");
                    //pass parameters
                    $isRent = true;
                    $stmt->bind_param("ss", $isRent, $propertyID);
                    if($stmt->execute())
                    {
                        $stmt->close();
                        return 1;
                    }
                    $stmt->close();
                    return 0;
                }
                
                function loadAgreement($property_id, $user_id)
                {
                    if (isset($_FILES['pdf_file'])) 
                    {
                        $uploadDir = "../../uploadsAgreement/";
                        $filename = "user_" . $user_id . "_property_" . $property_id . "_" . basename($_FILES["pdf_file"]["name"]);
                        $uploadFile = $uploadDir . $filename;
                        $uploadOk = 1;
                        $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

                        // Check if the file is a PDF
                        if ($fileType !== "pdf")
                        {
                            showError("Only PDF files are allowed");
                            $uploadOk = 0;
                        }

                        // Check if $uploadOk is set to 0 by an error
                        if ($uploadOk == 0) 
                        {
                            showError("File was not uploaded.");
                        } 
                        else 
                        {
                            // Try to upload the file
                            if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $uploadFile)) 
                            {
                                //showSuccess("Agreement Uploaded");
                                return $filename;
                            } 
                            else 
                            {
                                showError("There was an error uploading your file.");
                            }
                        }
                    } 
                    else 
                    {
                        echo "No file uploaded.";
                    }
                    return "";
                }

                function getEmailOwner($db_connection, $property_id)
                {
                    $sql = getSqlQuerryEmail($property_id);
                    $stmt = $db_connection->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc();
                }

                function getSqlQuerry($property_id) : string
                {

                    return "SELECT property.*, property_image.*
                    FROM property
                    LEFT JOIN property_image ON property.property_id = property_image.property_id
                    WHERE property.property_id = " .$property_id;
                }

                function getSqlQuerryEmail($property_id) : string
                {
                    return "SELECT user.email FROM user JOIN property ON user.user_id=property.user_id WHERE property.property_id = " .$property_id;
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