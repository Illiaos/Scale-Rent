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
            setcookie("account_type", "", time() + (86400 * 30), "/");
        ?>
        <div class="container-md w-50 p-3">
            <h1 class="mt-4 mb-4">Account Listings</h1>
            <h2>Account Filters</h2>
                <form action="manageAccounts.php" method="POST">
                    <div class="row">
                        <!-- Number of Bedrooms -->
                        <div class="col-md-3 mb-3">
                            <label for="account_type">Account Type</label>
                            <select class="form-control" id="account_type" name="account_type">
                                <?php
                                    $applianceOptions = array("", "Tenant", "Landlord");
                                    foreach ($applianceOptions as $option) 
                                    {
                                        $selected = ($_POST['account_type'] == $option) ? 'selected' : '';
                                        echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
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

                }

                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    if(isset($_POST['deleteAccount']))
                    {
                        deleteAccount($db_connection, $_POST['deleteAccount']);
                    }
                    $filter = [];

                    if(isset($_POST['account_type']) && !empty($_POST['account_type']))
                    {
                        $filter['account_type'] = validate_form_input($_POST['account_type']);
                        if(isset($_COOKIE['account_type']))
                        {
                            $_COOKIE['account_type'] = $filter['account_type'];
                        }
                    }

                    show($db_connection, $filter);
                }

                function deleteAccount($db_connection, $user_id)
                {
                    $sql = "DELETE FROM user_ass03 WHERE user_id = $user_id";
                    $db_connection->query($sql);
                }

                function getUserTypeId($db_connection, $filter)
                {
                    if($filter['account_type'] == 'Tenant')
                    {
                        return 2;
                    }
                    else if($filter['account_type'] == 'Landlord')
                    {
                        return 3;
                    }
                }

                function getUserType($db_connection, $typeId)
                {
                    // Your SQL query to select data 
                    $sql = "SELECT * FROM user_type WHERE type_id=".$typeId;
                    $stmt = $db_connection->prepare($sql);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    return $result->fetch_assoc()['type'];
                }

                function show($db_connection, $filter)
                {
                    // Your SQL query to select data 
                    $sql = "";
                    if(empty($filter))
                    {
                        $sql = "SELECT * FROM user_ass03";
                    }
                    else
                    {
                        $typeIndex = getUserTypeId($db_connection, $filter);
                        $sql = "SELECT * FROM user_ass03 WHERE type_id=".$typeIndex;
                    }
                    $stmt = $db_connection->prepare($sql);
                    //$stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    
                    // Output data of each row
                    while($row = $result->fetch_assoc()) 
                    {
                        if($row['type_id'] == 1)
                        {
                            continue;
                        }
                        $userType = getUserType($db_connection, $row['type_id']);
                        echo
                        ('
                            <div class="property card">
                                <div class="card-body">
                                    <p class="card-text">Name: '.$row["name"].'</p>
                                    <p class="card-text">Surname: '.$row["surname"].'</p>
                                    <p class="card-text">Email: '.$row["email"].'</p>
                                    <p class="card-text">Type: '.$userType.'</p>
                        ');

                        if(isset($_COOKIE['userLevel']) == true && $_COOKIE['userLevel'] == "Admin")
                        {
                            echo 
                            ('
                            <form action="manageAccounts.php" method="POST">
                                <button name="deleteAccount" id="deleteAccount" value="'.$row['user_id'].'" class="btn btn-primary">Delete</button>
                            </form>
                            ');
                        }
                        echo
                        ('
                                </div>
                            </div>
                        ');
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
            ?>
        </div>
    </body>
</html>