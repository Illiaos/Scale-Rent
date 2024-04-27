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
        <title>New Property Register</title>
        <style>
            body {
    margin: 0;
    padding-left: 1rem;
    font-family: Arial, sans-serif;
    width: 60%;
    margin-left: auto;
    margin-right: auto;
    background-color: #ffff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
td:first-child
{
    display: none;
  }

.change-password-container {
    width: 50%;
    margin: 50px auto;
    background-color: #fff;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
    text-align: center;
    color: #085F63;
}

.form-group label {
    display: block;
    padding-left: 1rem;
    color: #085F63;
    margin:.6rem;
    margin-bottom:0;

}

.form-group input,
.form-group select {
    width: 70%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    /* margin-left: auto;
    margin-right: auto;
    display:block; */
    margin:.6rem;
    padding-left: 2rem;
}
.upload{
    margin:1rem;
}
button[type="submit"] {
    width: 50%;
    padding: 10px;
    border: none;
    background-color: #085F63;
    color: #fff;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.5s;
    margin-left: auto;
    margin-right: auto;
    display:block;
    margin-top:1.2rem;
}

button[type="submit"]:hover {
    background-color: #49BEB7;
}

.alert {
    text-align: center;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
    padding: .75rem 1.25rem;
    margin-top: 10px;
    border: 1px solid transparent;
    border-radius: .25rem;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
    padding: .75rem 1.25rem;
    margin-top: 10px;
    border: 1px solid transparent;
    border-radius: .25rem;
}

.alert-dismissible {
    position: relative;
    padding-right: 2.5rem;
    margin-bottom: 1rem;
}

.alert-dismissible .close {
    position: absolute;
    top: 0;
    right: 0;
    padding: .75rem 1.25rem;
    color: inherit;
}

        </style>
    </head>
    <body>
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
                if(isset($_COOKIE['editPropertyID']))
                {
                    unset($_COOKIE['editPropertyID']);
                    setcookie("editPropertyID", $property_id, time() + (86400 * 30), "/");
                    //$_COOKIE['editPropertyID'] = $property_id;
                    //echo("COOKIE: " . $_COOKIE['editPropertyID']);
                }
                else
                {
                    setcookie("editPropertyID", $property_id, time() + (86400 * 30), "/");
                }
                getPropertyData($db_connection, $property_id);
                getPropertyItems($db_connection, $property_id);
            }
            
            
            function getPropertyData($db_connection, $property_id)
            {
                // Your SQL query to select data 
                $sql = getSqlQuerry($property_id);
                $stmt = $db_connection->prepare($sql);
                //$stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                $data = $result->fetch_assoc();
                
                $_POST['numberOfBeds'] = $data['number_of_beds'] . "-Bed";
                $_POST['contractLength'] = $data['rent_period'] . "-Month";
                $_POST['propertyDescription'] = $data['description'];
                $_POST['propertyAddress'] = $data['address'];
                $_POST['propertyPostcode'] = $data['post_code'];
                $_POST['propertyPrice'] = $data['price'];
            }

            function getPropertyItems($db_connection, $property_id)
            {
                // Your SQL query to select data 
                $sql = getSqlQuerryForItems($property_id);
                $stmt = $db_connection->prepare($sql);
                //$stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();

                $data = array();
                while($row = $result->fetch_assoc())
                {
                    array_push($data, array($row['item_id'], $row['title'], $row['description'], $row['quantity']));
                }
                $_POST['data'] = $data;
            }

            function getSqlQuerry($property_id) : string
            {

                return "SELECT property.*
                FROM property
                WHERE property.property_id = " .$property_id;
            }
            
            function getSqlQuerryForItems($property_id) : string
            {
                return "SELECT * FROM property_inventory WHERE property_inventory.property_id=".$property_id;
            }         

            // Check if the "Add Row" button is clicked
            if (isset($_POST['add_row'])) 
            {
                // Add an empty row to the data array
                $data = $_POST['data'];
                $data[] = array("", "", "", "");
                $_POST['data'] = $data;
            }
        
            // Check if the "Delete" button is clicked for a specific row
            if (isset($_POST['delete_row'])) 
            {
                // Remove the row from the data array based on the row index
                $index = $_POST['delete_row'];
                $data = $_POST['data'];
                deleteItemElement($db_connection, $data[$index][0]);
                unset($data[$index]);
                // Reset array keys to maintain sequential indexing
                $data = array_values($data);
                $_POST['data'] = $data;
            }
            include('../header/header.php');
        ?>
        <div class="container-md w-50 p-3">
            <h1 class="mt-4 mb-4">Update Property</h1>
            <form action="editProperty.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label class="font-weight-bold">Number of Beds</label>
                    <select id="numberOfBeds" name="numberOfBeds" class="form-control">
                        <?php
                            $applianceOptions = array("", "1-Bed", "2-Bed", "3-Bed", "4-Bed");
                            foreach ($applianceOptions as $option) 
                            {
                                $selected = ($_POST["numberOfBeds"] == $option) ? 'selected' : '';
                                echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Rent Length</label>
                    <select id="contractLength" name="contractLength" class="form-control">
                        <?php
                            $applianceOptions = array("", "3-Month", "6-Month", "12-Month");
                            foreach ($applianceOptions as $option) 
                            {
                                $selected = ($_POST["contractLength"] == $option) ? 'selected' : '';
                                echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Description</label>
                    <input type="text" id="propertyDescription" name="propertyDescription" class="form-control" placeholder="Enter Property Description"
                    value="<?php if(isset($_POST['propertyDescription'])) echo $_POST['propertyDescription']; ?>">
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Address</label>
                    <input type="text" id="propertyAddress" name="propertyAddress" class="form-control" placeholder="Enter Property Address"
                    value="<?php if(isset($_POST['propertyAddress'])) echo $_POST['propertyAddress']; ?>">
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Postcode</label>
                    <input type="text" id="propertyPostcode" name="propertyPostcode" class="form-control" placeholder="Enter Property Postcode"
                    value="<?php if(isset($_POST['propertyPostcode'])) echo $_POST['propertyPostcode']; ?>">
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">Price</label>
                    <input type="text" id="propertyPrice" name="propertyPrice" class="form-control" placeholder="Enter Property Price"
                    value="<?php if(isset($_POST['propertyPrice'])) echo $_POST['propertyPrice']; ?>">
                </div>

                <table>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Quantity</th>
                    </tr>
                    <?php 
                        if(isset($_POST['data']))
                        {
                            $data = $_POST['data'];
                            foreach ($data as $index => $row)
                            {
                                echo('<tr>');                
                                foreach ($row as $key => $cell)
                                {
                                    echo
                                    (
                                        '
                                            <td><input type="text" name="data['.$index.']['.$key.']" value="'.$cell.'"></td>
                                        '
                                    );
                    
                                }
                                echo('<td><button type="submit" name="delete_row" value="'.$index.'">Delete</button></td>');
                                echo('</tr>');
                            }
                        }
                    ?>
                </table>
                <button type="submit" name="add_row">Add Row</button>

                <input type="file" name="uploadImageFiles[]" id="uploadImageFiles" multiple  class="upload">
                <!--<input type="submit" name="uploadImageCall" value="Upload"> -->

                <button type="submit" name="updateCall" value="" class="btn btn-primary p-3">Update property</button>
            </form>
        </div>

        <?php
                if ($_SERVER['REQUEST_METHOD'] == 'GET')
                {

                }

                // checking to see if the server has received a POST request.
                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    if(isset($_POST['updateCall']))
                    {
                        $imageArray = loadImages();
                        //assign user input from POST
                        $numberOfBeds = validate_form_input($_POST['numberOfBeds']);
                        $contractLength = validate_form_input($_POST['contractLength']);
                        $propertyDescription = validate_form_input($_POST['propertyDescription']);
                        $propertyAddress = validate_form_input($_POST['propertyAddress']);
                        $propertyPostcode = validate_form_input($_POST['propertyPostcode']);
                        $propertyPrice = validate_form_input($_POST['propertyPrice']);
                        $property_id = $_COOKIE['editPropertyID'];

                        //check correctness of user input
                        $input = validateInput($imageArray, $numberOfBeds, $contractLength, $propertyDescription, $propertyAddress, $propertyPostcode, $propertyPrice);

                        if($input == false) return;

                        if(checkIfPropertyExistByPostCode($db_connection, $property_id, $propertyPostcode))
                        {
                            showSingleError("Property with this Post Code already exist");
                            return;
                        }
                        
                        $result = updatePropertyInDB($db_connection, $property_id, $numberOfBeds, $contractLength, $propertyDescription, $propertyAddress, $propertyPostcode, $propertyPrice);            
                        if($result)
                        {
                            //$property_id = $db_connection->insert_id;
                            //addImagesToDB($db_connection, $property_id, $imageArray);
                            updateItemsToDB($db_connection, $property_id);
                            if($result)
                            {
                                showSuccess("Property Updated");
                            }
                            else
                            {
                                showError("Data not added to DB");
                            }
                        }
                        else
                        {
                            showError("Data not added to DB");
                        }

                    }
                }

                function loadImages() : array
                {
                    $imageArray = array();
                    if (isset($_FILES['uploadImageFiles'])) 
                    {
                        foreach( $_FILES[ 'uploadImageFiles' ]["name"] as $key => $name)
                        {
                            $img_name = $_FILES['uploadImageFiles']['name'][$key];
                            $img_size = $_FILES['uploadImageFiles']['size'][$key];
                            $tmp_name = $_FILES['uploadImageFiles']['tmp_name'][$key];
                            $error = $_FILES['uploadImageFiles']['error'][$key];

                            if ($error === 0) 
                            {
                                if ($img_size > 125000 * 4) 
                                {
                                    $em = "Sorry, your file is too large.";
                                    showError($em);
                                    return array();
                                }
                                else 
                                {
                                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                                    $img_ex_lc = strtolower($img_ex);
                        
                                    $allowed_exs = array("jpg", "jpeg", "png"); 
                        
                                    if (in_array($img_ex_lc, $allowed_exs)) 
                                    {
                                        $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
                                        $img_upload_path = '../../uploads/'.$new_img_name;
                                        move_uploaded_file($tmp_name, $img_upload_path);
                                        array_push($imageArray, $new_img_name);
                                        // Insert into Database
                                        //$sql = "INSERT INTO images(image_url) VALUES('$new_img_name')";
                                        //mysqli_query($conn, $sql);
                                    }
                                    else 
                                    {
                                        $em = "You can't upload files of this type";
                                        showError($em);
                                        return array();
                                    }
                                }
                            }
                            else 
                            {
                                return array();
                            }
                        } 
                    }
                    return $imageArray;
                }

                //check correctness of user input
                function validateInput($imageArray, $numberOfBeds, $contractLength, $propertyDescription, $propertyAddress, $propertyPostcode, $propertyPrice)
                {
                    //patter for email
                    $emailPattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
                    $eirCodePattern = "/^[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z]$/";

                    $errors = array();

                    //if(empty($imageArray))
                    //{
                        //array_push($errors, "Select at least one image");
                    //}

                    if(empty($numberOfBeds))
                    {
                        array_push($errors, "Number of beds is not selected");
                    }

                    if(empty($contractLength))
                    {
                        array_push($errors, "Contract length is not selected");
                    }

                    if(empty($propertyDescription))
                    {
                        array_push($errors, "Property Description is not entered");                        
                    }

                    if(empty($propertyAddress))
                    {
                        array_push($errors, "Property Address is not entered");                        
                    }

                    if(empty($propertyPostcode))
                    {
                        array_push($errors, "Property Postcode is not entered");                        
                    }
                    else if(!preg_match($eirCodePattern, $propertyPostcode)) //check if email math pattern
                    {
                        array_push($errors, "Wrong EirCode. Right Format: A00A00A");
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
                        echo('</div>');
                        return false;
                    }
                    return true;
                }

                function getUserIdByEmail($db_connection, $userEmail) : string
                {
                    $stmt = $db_connection->prepare("SELECT * FROM user WHERE email=?");
                    $stmt->bind_param("s", $userEmail);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    
                    if($result->num_rows == 0)
                    {
                        return "";
                    }
                    $userData = $result->fetch_assoc();
                    return $userData['user_id'];
                }

                function checkIfPropertyExistByPostCode($db_connection, $property_id, $postCode) : bool
                {
                    $stmt = $db_connection->prepare("SELECT * FROM property WHERE post_code=? AND property_id!=?");
                    $stmt->bind_param("ss", $postCode, $property_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $stmt->close();
                    
                    if($result->num_rows == 0)
                    {
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

                function updatePropertyInDB($db_connection, $propertyID, $numberOfBeds, $contractLength, $propertyDescription, $propertyAddress, $propertyPostcode, $propertyPrice)
                {
                    //slq query to update data
                    $stmt = $db_connection->prepare("UPDATE property SET number_of_beds = ?, rent_period = ?, description = ?, address = ?, post_code = ?, price = ? WHERE property_id = ?");
                    //pass parameters
                    $stmt->bind_param("sssssss", $numberOfBeds, $contractLength, $propertyDescription, $propertyAddress, $propertyPostcode, $propertyPrice, $propertyID);
                    if($stmt->execute())
                    {
                        $stmt->close();
                        return 1;
                    }
                    $stmt->close();
                    return 0;
                }

                function addImagesToDB($db_connection, $property_id, $imageArray)
                {
                    foreach($imageArray as $item)
                    {
                        //sql query that adds data to DB
                        $sql = "INSERT INTO property_image (property_id, image_path) 
                        VALUES ('$property_id', '$item')";
                        $result = mysqli_query($db_connection, $sql);
                        //mysqli_close($db_connection);
                        if(!$result)
                        {
                            return $result;
                        }
                    }
                    return 1;
                }

                function updateItemsToDB($db_connection, $property_id)
                {
                    if(isset($_POST['data']) == false) return 1;
                    $data = $_POST['data'];
                    foreach($data as $item)
                    {
                        if($item['0'] == "" || $item['0'] == null)
                        {
                            if($item['1'] != null)
                            {
                                $title = $item['1'];
                                $description = $item['2'];
                                $quantity = $item['3'];
                                $sql = "INSERT INTO property_inventory (property_id, title, description, quantity) 
                                VALUES ('$property_id', '$title', '$description', '$quantity')";
                                $result = mysqli_query($db_connection, $sql);
                            }
                        }
                        else
                        {
                            $userId = $item['0'];
                            $title = $item['1'];
                            $description = $item['2'];
                            $quantity = $item['3'];
                            $stmt = $db_connection->prepare("UPDATE property_inventory SET property_id = ?, title = ?, description = ?, quantity = ? WHERE item_id = ?");
                            //pass parameters
                            $stmt->bind_param("sssss", $property_id, $title, $description, $quantity, $userId);
                            if($stmt->execute())
                            {
                                $stmt->close();
                            }
                        }
                    }
                    return 1;
                }

                function deleteItemElement($db_connection, $item_id)
                {
                    $sql = "DELETE FROM property_inventory WHERE item_id = $item_id";
                    $db_connection->query($sql);
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
                    //showHomeButton();
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
            ?>
    </body>
</html>