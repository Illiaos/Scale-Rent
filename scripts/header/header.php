<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">      
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/~s3098121/scripts/ass03/style.css">
        <script src="script.js"></script>
        <title>Nassdam Solid Ventures</title>
    </head>
    <body>
        <div id="side-head">
            <div class="site-logo">
                <img src="/~s3098121/scripts/ass03/pics/logo-removebg-preview.png" alt="site-logo">
            </div>

            <?php
                if($_SERVER['REQUEST_METHOD'] == 'POST')
                {
                    if(isset($_POST['logInPage']))
                    {
                        header("Location: /~s3098121/scripts/ass03/scripts/log_in/logIn.php");
                        exit();
                    }
                    else if(isset($_POST['logOutPage']))
                    {
                        header('Location: /~s3098121/scripts/ass03/scripts/logout/logout.php?cookieReset');
                        exit();
                    }
                }

                if(isset($_COOKIE['userLevel']) == false)
                {
                    showPuplicNavigation();
                }
                else
                {
                    $userType = $_COOKIE['userLevel']; 
                    if($userType == "Admin")
                    {
                        showAdminNavigation();
                    }
                    else if($userType == "Landlord")
                    {
                        showLandlordNavigation();
                    }
                    else if($userType == "Tenant")
                    {
                        showTenantNavigation();
                    }
                    else
                    {
                        showPuplicNavigation();
                    }
                }
                
                if(isset($_COOKIE['userID']) == false)
                {
                    echo '
                    <form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="POST">
                        <button type="submit" name="logInPage">Login</button>
                    </form>';
                }
                else
                {
                    echo '
                    <form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="POST">
                        <button type="submit" name="logOutPage">Logout</button>
                    </form>';
                }

                function showPuplicNavigation()
                {
                    echo
                    ('
                        <nav>
                            <ul class="nav-links">
                                <li><a href="/~s3098121/scripts/ass03/index.php">Home</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/property/listProperty.php?loadPage=true">View Property</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/testimonial/showTestimonial.php">Testimonial</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/contact/contact-page.php">Contact</a></li>
                            </ul>
                        </nav>
                    ');
                }

                function showLandlordNavigation()
                {
                    echo
                    ('
                        <nav>
                            <ul class="nav-links">
                                <li><a href="/~s3098121/scripts/ass03/index.php">Home</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/property/addProperty.php">Add New Property</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/property/listProperty.php?loadPage=true">View Own Property</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/accounts/landLordAccount.php?loadPage=true">Account</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/testimonial/addTestimonial.php?loadPage=true">Add Testimonial</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/testimonial/showTestimonial.php">Testimonial</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/contact/contact-page.php">Contact</a></li>
                            </ul>
                        </nav>
                    ');
                }

                function showTenantNavigation()
                {
                    echo
                    ('
                        <nav>
                            <ul class="nav-links">
                                <li><a href="/~s3098121/scripts/ass03/index.php">Home</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/property/listProperty.php?loadPage=true">View Property</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/accounts/tenantAccount.php?loadPage=true">Account</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/testimonial/addTestimonial.php?loadPage=true">Add Testimonial</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/testimonial/showTestimonial.php">Testimonial</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/contact/contact-page.php">Contact</a></li>
                            </ul>
                        </nav>
                    ');
                }

                function showAdminNavigation()
                {
                    echo
                    ('
                        <nav>
                            <ul class="nav-links">
                                <li><a href="/~s3098121/scripts/ass03/index.php">Home</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/property/addProperty.php">Add New Property</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/property/listProperty.php?loadPage=true">View Property</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/testimonial/showTestimonial.php">Testimonial</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/testimonial/manageTestimonial.php">Manage Testimonial</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/accounts/manageAccounts.php">Manage Accounts</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/contact/contact-page.php">Contact</a></li>
                                <li><a href="/~s3098121/scripts/ass03/scripts/contact/contact-page-manage.php">Contact Manager</a></li>
                            </ul>
                        </nav>
                    ');
                }
            ?>
        </div>
    </body>
</html>