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
            setcookie("post_code", "", time() + (86400 * 30), "/");
            setcookie("price", "", time() + (86400 * 30), "/");
            setcookie("number_of_beds", "", time() + (86400 * 30), "/");
            setcookie("rent_period", "", time() + (86400 * 30), "/");
        ?>
        <div class="container-md w-50 p-3">
            <h1 class="mt-4 mb-4">Property Listings</h1>
            <h2>Property Filters</h2>
                <form action="listProperty.php" method="POST">
                    <div class="row">
                        <!-- Post Code -->
                        <div class="col-md-3 mb-3">
                            <label for="post_code">Post Code:</label>
                            <input type="text" class="form-control" id="post_code" name="post_code" placeholder="Enter post code"
                            value="<?php if(isset($_POST['post_code'])) echo $_POST['post_code']; ?>">
                        </div>

                        <!-- Rental Price -->
                        <div class="col-md-3 mb-3">
                            <label for="price">Rental Price (max):</label>
                            <input type="number" class="form-control" id="price" name="price" placeholder="Enter rental price"
                            value="<?php if(isset($_POST['price'])) echo $_POST['price']; ?>">
                        </div>

                        <!-- Number of Bedrooms -->
                        <div class="col-md-3 mb-3">
                            <label for="numBedrooms">Number of Bedrooms:</label>
                            <select class="form-control" id="number_of_beds" name="number_of_beds">
                                <?php
                                    $applianceOptions = array("", "1", "2", "3", "4");
                                    foreach ($applianceOptions as $option) 
                                    {
                                        $selected = ($_POST['number_of_beds'] == $option) ? 'selected' : '';
                                        echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                                    }
                                ?>
                            </select>
                        </div>

                        <!-- Length of Tenancy -->
                        <div class="col-md-3 mb-3">
                            <label for="lengthOfTenancy">Length of Tenancy:</label>
                            <select class="form-control" id="rent_period" name="rent_period">
                                <?php
                                    $applianceOptions = array("", "3", "6", "12");
                                    foreach ($applianceOptions as $option) 
                                    {
                                        $selected = '';
                                        if(isset($_POST['rent_period']))
                                        {
                                            $selected = ($_POST['rent_period'] == $option) ? 'selected' : '';
                                        }
                                        echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                                    }
                                ?>
                            </select>
                        </div>

                        <?php 
                            if(isset($_COOKIE['userLevel']) && $_COOKIE['userLevel'] == "Admin")
                            {
                                $userID = "";
                                if(isset($_POST['user_search']))
                                {
                                    $userID = $_POST['user_search'];
                                }

                                echo
                                ('
                                    <!-- Search for landlord -->
                                    <div class="col-md-3 mb-3">
                                        <label for="post_code">Landlord ID:</label>
                                        <input type="text" class="form-control" id="user_search" name="user_search" placeholder="Enter landlord id"
                                        value="'.$userID.'">
                                    </div>
                                ');
                            }
                        ?>
                    </div>

                    <!-- Submit Button -->
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" value="submit">Search</button>
                        </div>
                    </div>
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
                    if(isset($_GET['loadPage']))
                    {
                        $filter = [];
                        if(isset($_COOKIE['userLevel']) == true && ($_COOKIE['userLevel'] == "Landlord"))
                        {
                            $filter['userID'] = $_COOKIE['userID'];
                        }
                        show($db_connection, $filter);
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

                function show($db_connection, $filter)
                {
                    // Your SQL query to select data 
                    $sql = getSqlQuerry($filter);
                    $stmt = $db_connection->prepare($sql);
                    //$stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    
                    // Output data of each row
                    while($row = $result->fetch_assoc()) 
                    {
                        $img_upload_path = '../../uploads/'.$row['image_path'];
                        echo
                        ('
                            <div class="property card">
                                <img src="'.$img_upload_path.'" class="img-thumbnail" style="width: 100px; height: auto;" alt="Property Image">
                                <div class="card-body">
                                    <h5 class="card-title"> '.$row["address"].'</h5>
                                    <p class="card-text">Number of Beds: '.$row["number_of_beds"].'</p>
                                    <p class="card-text">Rent Period: '.$row["rent_period"].'</p>
                                    <p class="card-text">Description: '.$row["description"].'</p>
                                    <p class="card-text">Postcode: '.$row["post_code"].'</p>
                                    <p class="card-text">Price: '.$row["price"].'</p>
                        ');

                        if(isset($_COOKIE['userLevel']) == true && ($_COOKIE['userLevel'] == "Landlord" || $_COOKIE['userLevel'] == "Admin"))
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
                        }
                        echo
                        ('
                                </div>
                            </div>
                        ');
                    }
                }

                function getSqlQuerry($filter) : string
                {
                    $conditions = [];

                    if(isset($filter['post_code']))
                    {
                        $conditions[] = "p.post_code LIKE '{$filter['post_code']}%'";
                    }

                    if(isset($filter['price']))
                    {
                        $conditions[] = "p.price = '{$filter['price']}'";
                    }

                    if(isset($filter['number_of_beds']))
                    {
                        $conditions[] = "p.number_of_beds = '{$filter['number_of_beds']}'";
                    }

                    if(isset($filter['rent_period']))
                    {
                        $conditions[] = "p.rent_period = '{$filter['rent_period']}'";
                    }

                    if(isset($filter['userID']))
                    {
                        $conditions[] = "p.user_id = '{$filter['userID']}'";
                    }

                    if(empty($filter))
                    {
                        return 
                            "SELECT
                                p.property_id,
                                p.number_of_beds,
                                p.rent_period,
                                p.description,
                                p.address,
                                p.post_code,
                                p.price,
                                i.image_path
                            FROM 
                                property p
                            LEFT JOIN 
                                (SELECT property_id, MIN(image_path) as image_path FROM property_image GROUP BY property_id) i 
                            ON 
                                p.property_id = i.property_id;";
                    }

                    return 
                        "SELECT
                            p.property_id,
                            p.number_of_beds,
                            p.rent_period,
                            p.description,
                            p.address,
                            p.post_code,
                            p.price,
                            i.image_path
                        FROM 
                            property p
                        LEFT JOIN 
                            (SELECT property_id, MIN(image_path) as image_path FROM property_image GROUP BY property_id) i 
                        ON 
                            p.property_id = i.property_id
                        WHERE" . ' ' . implode(' AND ', $conditions);
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