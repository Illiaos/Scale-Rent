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
        <title>New Property Register</title>  
    </head>
    <body>
        <?php 
            include('../header/header.php');
        ?>

        <div class="container-md w-50 p-3">
            <h1 class="mt-4 mb-4">Property Listings</h1>

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

                // Your SQL query to select data
                $sql = "SELECT
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
                            p.property_id = i.property_id";
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
                                <a href="edit_property.php?postcode='.$row["post_code"].'" class="btn btn-primary">Edit</a>
                            </div>
                        </div>
                    ');
                }


                function showForPublic()
                {

                }

                function showForLandlord()
                {

                }

                function showForAdmin()
                {
                    
                }
            ?>
        </div>
    </body>
</html>