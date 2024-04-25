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
        <title>Log Out</title>
        <style>
            body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container-md {
    width: 50%;
    margin: 0 auto;
    padding: 20px;
}

.alert {
    margin-top: 20px;
    padding: 15px;
    border-radius: 5px;
}

.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
}

.btn:hover {
    background-color: #0056b3;
}

        </style>
    </head>
    <body>
        <?php 
            include('../header/header.php');
        ?>
        <div class="container-md w-50 p-3">
        </div>

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
                    if(isset($_GET['cookieReset']))
                    {
                        if(isset($_COOKIE['userLevel']))
                        {
                            unset($_COOKIE['userLevel']);
                            setcookie('userLevel', '', -1, '/'); 
                        }
    
                        if(isset($_COOKIE['userID']))
                        {
                            unset($_COOKIE['userID']);
                            setcookie('userID', '', -1, '/'); 
                        }
                        header("Location: /Scale-Rent/scripts/logout/logout.php?logOutConfirmation");
                        exit();
                    }
                    else if(isset($_GET['logOutConfirmation']))
                    {
                        showSuccess("Log out successful");
                    }
                }

                // checking to see if the server has received a POST request.
                if ($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    
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
                    echo('</div>');
                }
            ?>
    </body>
</html>