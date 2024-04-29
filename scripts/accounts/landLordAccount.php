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
                    $path_to_mysql_connect = '../../../../../mysql_connect.php';
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

                    if(isset($_GET['cancelPropertyRent']))
                    {
                        $property_id = $_GET['cancelPropertyRent'];
                        cancelRentInPropertyDB($db_connection, $property_id);
                        deleteRentForTenent($db_connection, $property_id);
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit; // Make sure to exit after the header redirection
                    }
                    showAccountRentData($db_connection, $user_id);
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {

                }

                function cancelRentInPropertyDB($db_connection, $property_id)
                {
                    $cancelRent = false;
                    $stmt = $db_connection->prepare("UPDATE property SET isRent = ? WHERE property_id = ?");
                    //pass parameters
                    $stmt->bind_param("ss", $cancelRent, $property_id);
                    if($stmt->execute())
                    {
                        $stmt->close();
                    }
                }

                function deleteRentForTenent($db_connection, $property_id)
                {
                    $sql = "DELETE FROM tenant_account WHERE property_id = $property_id";
                    $db_connection->query($sql);
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
                    $sql = "SELECT * from landlord_account WHERE landlord_account.user_id = $user_id";
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
                                <!--<a href="/~s3098121/scripts/ass03/scripts/property/listProperty.php?loadPage=true" class="btn btn-primary">View Property</a>-->
                            '
                        );
                        return;
                    }

                    // Output data of each row
                    while($row = $result->fetch_assoc()) 
                    {
                        $propertyRented = checkIfIsRent($db_connection, $row["property_id"]);
                        echo
                        ('
                            <div class="property card">
                                <div class="card-body">


                        ');

                        if($propertyRented)
                        {
                            echo
                            (
                                '
                                    <h5 class="card-title"> RENTED '.getPostCodeByPropertyId($db_connection, $row["property_id"]).'</h5>
                                '
                            );
                        }
                        else
                        {
                            echo
                            (
                                '
                                    <h5 class="card-title"> NOT RENTED '.getPostCodeByPropertyId($db_connection, $row["property_id"]).'</h5>
                                '
                            );
                        }

                        echo
                        (
                            '
                                <p class="card-text">Income: '.$row["income"].'</p>
                                <p class="card-text">Fee: '.$row["fee"].'</p>
                            '
                        );

                        if($propertyRented)
                        {
                            echo
                            (
                                '
                                    <form action="landLordAccount.php" method="GET">
                                        <button type="submit" name="cancelPropertyRent" id="cancelPropertyRent" value="'.$row["property_id"].'" class="btn btn-primary">Cancel Rent</button>
                                    </form>
                                '
                            );
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

                function checkIfIsRent($db_connection, $property_id)
                {
                    // Your SQL query to select data 
                    $sql = "SELECT * FROM property WHERE property.property_id=".$property_id;
                    $stmt = $db_connection->prepare($sql);
                    //$stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc()['isRent'];
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