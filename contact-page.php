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
        </div>
    </main>
    <footer>
        <div class="contact-form">
            <?php
            // check if form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // define your admin email
                $admin_email = "admin@example.com";
                
                // get form data
                $name = $_POST["name"];
                $email = $_POST["email"];
                $phone = $_POST["phone"]; 
                $message = $_POST["message"];
                
                // validation
                if (empty($name) || empty($email) || empty($phone) || empty($message)) { 
                    echo "Please fill in all fields.";
                } else {
                    // send email to admin
                    $subject = "New message from Contact Form";
                    $body = "Name: $name\nEmail: $email\nPhone: $phone\n\n$message"; 
                    
                    // error to send message
                    if (mail($admin_email, $subject, $body)) {
                        echo "Message sent successfully. Thank you!";
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                }
            }
            ?>
            <form id="contact-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name"><br>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email"><br>
                <label for="phone">Phone:</label><br> 
                <input type="tel" id="phone" name="phone" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" required><br> 
                <label for="message">Message:</label><br>
                <textarea id="message" name="message" rows="4"></textarea><br>
                <input type="submit" value="Submit">
            </form>
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
