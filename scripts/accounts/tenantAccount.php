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
            <h1 class="mt-4 mb-4">Account</h1>
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
                    /*$property_id = $_GET['property_id'];
                    if(isset($_COOKIE['confirmPropertyID']))
                    {
                        unset($_COOKIE['confirmPropertyID']);
                        setcookie("confirmPropertyID", $property_id, time() + (86400 * 30), "/");
                    }
                    else
                    {
                        setcookie("confirmPropertyID", $property_id, time() + (86400 * 30), "/");
                    }*/
                    $user_id = $_COOKIE['userID'];

                    if(isset($_GET['agreementPath']))
                    {
                        $agreement_upload_path = '../../uploadsAgreement/'.$_GET['agreementPath'];
                        $file_name = basename($agreement_upload_path);
                        file_put_contents($file_name, file_get_contents($agreement_upload_path));
                    }
                    else if(isset($_GET['payForProperty']))
                    {
                        $property_id = $_GET['payForProperty'];
                        payForRent($db_connection, $property_id);
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit; // Make sure to exit after the header redirection
                    }
                    showAccountRentData($db_connection, $user_id);
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

                function showAccountRentData($db_connection, $user_id)
                {
                    // Your SQL query to select data 
                    $sql = "SELECT * from tenant_account WHERE tenant_account.user_id = $user_id";
                    $stmt = $db_connection->prepare($sql);
                    //$stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    
                    if($result->num_rows == 0)
                    {
                        echo
                        (
                            '
                                <h2 class="mt-3 mb-4">Property Is Not Rented</h2>
                                <a href="/Scale-Rent/scripts/property/listProperty.php?loadPage=true" class="btn btn-primary">View Property</a>
                            '
                        );
                        return;
                    }

                    // Output data of each row
                    while($row = $result->fetch_assoc()) 
                    {
                        echo
                        ('
                            <div class="property card">
                                <div class="card-body">
                                    <h5 class="card-title"> RENT '.getPostCodeByPropertyId($db_connection, $row["property_id"]).'</h5>
                                    <p class="card-text">Start Date: '.$row["start_date"].'</p>
                                    <p class="card-text">End Date: '.$row["end_date"].'</p>
                                    <p class="card-text">Paid: '.$row["paid"].'</p>
                                    <p class="card-text">Owed: '.$row["owed"].'</p>
                                    <a href="tenantAccount.php?agreementPath='.$row["agreement"].'" class="btn btn-primary">Download Agreement</a>
                        ');

                        $dayOfMonth = date('j');
                        $dayOfMonth = 3;
                        if ($dayOfMonth >= 1 && $dayOfMonth <= 10 && checkIfNeedPayment($db_connection, $row["property_id"])) 
                        {
                            echo 
                            ('
                            <form action="tenantAccount.php" method="GET">
                                <button type="submit" name="payForProperty" id="payForProperty" value="'.$row["property_id"].'" class="btn btn-primary">Pay</button>
                            </form>
                            ');
                        }

                        /*if(isset($_COOKIE['userLevel']) == true && ($_COOKIE['userLevel'] == "Landlord" || $_COOKIE['userLevel'] == "Admin"))
                        {
                            echo 
                            ('
                            <a href="editProperty.php?property_id='.$row["property_id"].'" class="btn btn-primary">Edit</a>
                            ');
                        }
                        else
                        {
                            echo 
                            ('
                            <a href="viewProperty.php?property_id='.$row["property_id"].'" class="btn btn-primary">View</a>
                            ');
                        }*/
                        echo
                        ('
                                </div>
                            </div>
                        ');
                    }
                }

                function getPostCodeByPropertyId($db_connection, $property_id)
                {
                    // Your SQL query to select data 
                    $sql = "SELECT post_code FROM property WHERE property.property_id=".$property_id;
                    $stmt = $db_connection->prepare($sql);
                    //$stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc()['post_code'];
                }

                function payForRent($db_connection, $property_id)
                {
                    $data = getRentData($db_connection, $property_id);
                    $paid = (int)$data['paid'];
                    $owed = (int)$data['owed'];
                    $monthPayment = (int)getMonthPayment($db_connection, $property_id);
                    $paid += $monthPayment;
                    $owed -= $monthPayment;

                    $stmt = $db_connection->prepare("UPDATE tenant_account SET paid = ?, owed = ? WHERE property_id = ?");
                    //pass parameters
                    $stmt->bind_param("sss", $paid, $owed, $property_id);
                    if($stmt->execute())
                    {
                        $stmt->close();
                    }
                    payForRentLandLord($db_connection, $property_id, $paid);
                }

                function payForRentLandLord($db_connection, $property_id, $paid)
                {
                    $data = getLandLordData($db_connection, $property_id);
                    $income = (int)$data['income'];
                    $fee = (int)$data['fee'];
                    
                    $fee += ($paid * 20) / 100;
                    $income += ($paid - $fee);


                    $stmt = $db_connection->prepare("UPDATE landlord_account SET income = ?, fee = ? WHERE property_id = ?");
                    //pass parameters
                    $stmt->bind_param("sss", $income, $fee, $property_id);
                    if($stmt->execute())
                    {
                        $stmt->close();
                    }
                }

                function getLandLordData($db_connection, $property_id) : array
                {
                    $sql = "SELECT * FROM landlord_account WHERE landlord_account.property_id=".$property_id;
                    $stmt = $db_connection->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc();
                }

                function getRentData($db_connection, $property_id) : array
                {
                    $sql = "SELECT * FROM tenant_account WHERE tenant_account.property_id=".$property_id;
                    $stmt = $db_connection->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc();
                }

                function getMonthPayment($db_connection, $property_id) : string
                {
                    $sql = "SELECT * FROM property WHERE property.property_id=".$property_id;
                    $stmt = $db_connection->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc()['price'];
                }

                function checkIfNeedPayment($db_connection, $property_id) : bool
                {
                    $monthlyPayment = getMonthPayment($db_connection, $property_id);

                    $sql = "SELECT * FROM tenant_account WHERE tenant_account.property_id=".$property_id;
                    $stmt = $db_connection->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();

                    $data = $result->fetch_assoc();
                    $startDate = $data['start_date'];
                    $endDate = $data['end_date'];
                    $alreadyPaid = $data['paid'];

                    $totalDueForMonth = $monthlyPayment;

                    // Check if the current month falls within the range and the total paid amount is equal to or greater than the total amount due for the month
                    if (isCurrentMonthInRange($startDate, $endDate) && $alreadyPaid >= $totalDueForMonth) 
                    {
                        return false;                        
                    } 
                    else 
                    {
                        return true;
                    }
                }

                function isCurrentMonthInRange($startDate, $endDate) 
                {
                    $currentDate = new DateTime();
                    $startData = new DateTime($startDate);
                    $endDate = new DateTime($endDate);
                    return ($currentDate >= $startDate && $currentDate <= $endDate);
                }
                
                // Function to calculate the number of months between two dates
                function getMonthDifference($startDate, $endDate) 
                {
                    $start = new DateTime($startDate);
                    $end = new DateTime($endDate);
                    $interval = $start->diff($end);
                    return ($interval->y * 12) + $interval->m;
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