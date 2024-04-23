<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
<script src="script.js"></script>
    <title>Nassdam Solid Ventures</title>
</head>
<body>
    <div id="side-head">
        <div class="site-logo">
            <img src="logo-removebg-preview.png" alt="site-logo">
        </div>
        <div id="menuButton">
            ☰
        </div>
        <nav>
            <ul>
                <li><a href="index.html">Home</a></li>
                <li><a href="about.html">About Us</a></li>
                <li><a href="team.html">Meet the team</a></li>
                <li><a href="contact-page.html">Contact Us</a></li>
            </ul>
        </nav>
    </div>
    <main class="contact-page">
        <div class="contact-image">
            <img src="pics/contact.jpg" alt="">
        </div>
        <div class="contact-text">
            <h3>We're here for you!</h3>
            <p>Monday - Friday</p>
            <p>9am - 5pm</p>
            <p>Saturdays 10am - 3pm</p>
        
            <h3>Chat Us</h3>
            <p>Call us <a href="#">0830785102</a></p>
            <p>facebook <a href="#">https://Nassadamventures/facebook.ng</a></p>
            <p>Twitter <a href="#">https://Nassadamventures/Twitter.ng</a></p>
        </div>
    </main>
    <footer>
        <div>
            <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Check if email is entered
                if (!empty($_POST['email'])) {
                    $email = $_POST['email'];
            
                    // Print success message
                    echo "<p>You have successfully subscribed to our newsletter. You will be amongst the people to hear about us firsthand.</p>";
                } else {
                    // Email is empty, show error message
                    echo "<p>Error: Please enter your email address.</p>";
                }
            }
            ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="email" placeholder="Enter Your E-mail">
            <button type="submit">Subscribe</button>
        </form>
        </div>
    </div>
        <div class="site-footer">
            <div class="opening-time">
                <ul>
                    <li> Monday - Friday 8am - 5pm</li>
                    <li>Sat 10am - 5pm</li>
                    <li>Sun- closed</li>
                    <li> Bank Holidays - closed</li>
                </ul>
            </div>
            <hr>
                <div class="useful-link">
                    <ul>
                            <li><a href="terms.html">Terms & Conditions</a></li>
                            <li><a href="privacy.html">Privacy Policy</a></li>
                            <li><a href="blog.html">Blog</a></li>
                            <li><a href="#">Testimony</a></li>
                            <li><a href="Career.html">Careers</a></li>
                    </ul>
                </div>
        </div>
    </footer>
</body>
</html>