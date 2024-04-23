<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">      
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../../style.css">
        <script src="script.js"></script>
        <title>Nassdam Solid Ventures</title>
    </head>
    <body>
        <div id="side-head">
            <div class="site-logo">
                <img src="../../pics/logo-removebg-preview.png" alt="site-logo">
            </div>
            <div id="menuButton">
                ☰
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="../../index.php">Home</a></li>
                    <li class="dropdown">
                        <a href="#">Rent</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Rent a Villa</a></li>
                            <li><a href="#">Rent an Apartment</a></li>
                            <li><a href="#">Rent a shared room</a></li>
                        </ul>
                    </li>
                    <li class="dropdown">
                        <a href="#">Sell</a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Sell a Villa</a></li>
                            <li><a href="#">Sell an Apartment</a></li>
                            <li><a href="#">Sell a shared room</a></li>
                        </ul>
                    </li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="contact-page.html">Contact</a></li>
                </ul>
            </nav>
            <?php
                if(isset($_COOKIE['userID']) == false)
                {
                    echo ('<button type="submit">Login</button>');
                }
                else
                {
                    echo ('<button type="submit">Log out</button>');
                }
            ?>
        </div>
    </body>
</html>