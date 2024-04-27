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
        <title>View Property</title>
    </head>
    <body>
        <?php
            include('../header/header.php');
        ?>
        <div class="container-md">
            <h1 class="mt-4 mb-4">Property Listings</h1>
                <form action="viewProperty.php" method="POST">

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
                    $data = array();
                    if(isset($_GET['property_id']))
                    {
                        showImages($db_connection, $_GET['property_id']);
                        //echo(getEmailOwner($db_connection, $_GET['property_id'])['email']);
                        show($db_connection, $_GET['property_id']);
                        showPropertyItems($db_connection, $_GET['property_id']);
                    }
                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    $filter = [];
                    if(isset($_POST['post_code']) && !empty($_POST['post_code']))
                    {
                        $filter['post_code'] = validate_form_input(strtoupper($_POST['post_code']));

                        if(isset($_COOKIE['post_code']))
                        {
                            $_COOKIE['post_code'] = $filter['post_code'];
                        }
                    }

                    if(isset($_POST['price']) && !empty($_POST['price']))
                    {
                        $filter['price'] = validate_form_input($_POST['price']);
                        if(isset($_COOKIE['price']))
                        {
                            $_COOKIE['price'] = $filter['price'];
                        }
                    }

                    if(isset($_POST['number_of_beds']) && !empty($_POST['number_of_beds']))
                    {
                        $filter['number_of_beds'] = validate_form_input($_POST['number_of_beds']);
                        if(isset($_COOKIE['number_of_beds']))
                        {
                            $_COOKIE['number_of_beds'] = $filter['number_of_beds'];
                        }
                    }

                    if(isset($_POST['rent_period']) && !empty($_POST['rent_period']))
                    {
                        $filter['rent_period'] = validate_form_input($_POST['rent_period']);
                        if(isset($_COOKIE['rent_period']))
                        {
                            $_COOKIE['rent_period'] = $filter['rent_period'];
                        }
                    }

                    if(isset($_COOKIE['userLevel']) == true && ($_COOKIE['userLevel'] == "Landlord"))
                    {
                        $filter['userID'] = $_COOKIE['userID'];
                    }
                    else if(isset($_COOKIE['userLevel']) == true && ($_COOKIE['userLevel'] == "Admin"))
                    {
                        if(isset($_POST['user_search']) && !empty($_POST['user_search']))
                        {
                            $filter['userID'] = validate_form_input($_POST['user_search']);
                        }
                    }

                    show($db_connection, $filter);

                    /*if(isset($_COOKIE['userLevel']) == false)
                    {
                        showForPublic($db_connection, $filter);
                    }
                    else
                    {
                        $userType = $_COOKIE['userLeve'];
                        if($userType == "Admin")
                        {
                            showForAdmin();
                        }
                        else if($userType == "Landlord")
                        {
                            showForLandlord();
                        }
                        else
                        {
                            showForPublic($db_connection, $filter);
                        }
                    }*/
                }

                /*function showForPublic($db_connection,  $filter)
                {
                }

                function showForLandlord()
                {

                }

                function showForAdmin()
                {
                    
                }*/

                function show($db_connection, $property_id)
                {
                    // Your SQL query to select data 
                    $sql = getSqlQuerry($property_id);
                    $stmt = $db_connection->prepare($sql);
                    //$stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    
                    $data = $result->fetch_assoc();
                    $userEmail = getEmailOwner($db_connection, $property_id)['email'];
                    echo
                    (
                        '
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-body">
                                        <h5 class="card-title">Apartment Details</h5>
                                            <p>Address: '.$data['address'].'</p>
                                            <p>Post Code: '.$data['post_code'].'</p>
                                            <p>Number of Beds: '.$data['number_of_beds'].'</p>
                                            <p>Rent Period: '.$data['rent_period'].'</p>
                                            <p>Price: '.$data['price'].'</p>
                                            <p>Description: '.$data['description'].'</p>
                                            <p>Contact: '.$userEmail.'</p>
                                        </div>
                                    </div>
                                </div>
                        '
                    );
                }
                
                function showPropertyItems($db_connection, $property_id)
                {
                    $sql = getSqlQuerryForItems($property_id);
                    $stmt = $db_connection->prepare($sql);
                    //$stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();

                    echo
                    (
                        '
                        <div class="col-md-8">
                            <h2>Property Inventory</h2>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                        '
                    );

                    while($row = $result->fetch_assoc())
                    {
                        echo 
                        (
                            "<tr>
                                <td>".$row["title"]."</td>
                                <td>".$row["description"]."</td>
                                <td>".$row["quantity"]."</td>
                            </tr>"
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

                function getEmailOwner($db_connection, $property_id)
                {
                    $sql = getSqlQuerryEmail($property_id);
                    $stmt = $db_connection->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc();
                }

                function showImages($db_connection, $property_id)
                {
                    // Your SQL query to select data 
                    $sql = getSqlQuerryForImages($property_id);
                    $stmt = $db_connection->prepare($sql);
                    //$stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    
                    $counter = 0;
                    echo('<div class="image-container">');
                    while($row = $result->fetch_assoc()) 
                    {
                        $img_upload_path = '../../uploads/'.$row['image_path'];
                        if ($counter % 2 == 0 && $counter > 0) 
                        {
                            echo '</div> <div class="image-container">';
                        }
                        echo 
                        (
                        '
                            <img src="' . $img_upload_path . '" alt="Image" style="width: 400px; height: auto;">
                        '
                        );
                    } 
                    echo('</div>');               
                }

                function getSqlQuerry($property_id) : string
                {

                    return "SELECT property.*, property_image.*
                    FROM property
                    LEFT JOIN property_image ON property.property_id = property_image.property_id
                    WHERE property.property_id = " .$property_id;
                }

                function getSqlQuerryForImages($property_id) : string
                {
                    return "SELECT property_image.*
                    FROM property_image
                    WHERE property_image.property_id=".$property_id;
                }

                function getSqlQuerryEmail($property_id) : string
                {
                    return "SELECT user.email FROM user JOIN property ON user.user_id=property.user_id WHERE property.property_id = " .$property_id;
                }

                function getSqlQuerryForItems($property_id) : string
                {
                    return "SELECT * FROM property_inventory WHERE property_inventory.property_id=".$property_id;
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
            ?>
        </div>
    </body>
</html>