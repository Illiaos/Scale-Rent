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
            <img src="pics/logo-removebg-preview.png" alt="site-logo">
        </div>
        <div id="menuButton">
            ☰
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="index.html">Home</a></li>
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
        <button type="submit">Login</button>
    </div>
    <main>
        <div class="main-site">
            <img src="pics/juu.jpg" alt="">
        </div>
        <div class="cities">
            <div id="slides">
                <img src="pics/Dublin.jpg" alt="Dublin">
                <h3>Dublin</h3>
               
            </div>
            <div id="slides">
                <img src="pics/cork.jpg" alt="Cork">
                <h3>Cork</h3>
                
            </div>
            <div id="slides">
                <img src="pics/limerick.jpg" alt="Limerick">
                <h3>Limerick</h3>
               
            </div>
            <div id="slides">
                <img src="pics/slifo.jpg" alt="Sligo">
                <h3>Sligo</h3>
            </div>
        </div>
        <div class="apartment">
            <h3>Explore <span>Apartment</span> Types</h3>
            <p>Feel Free To Choose From The Best</p>
        </div>
        <div id="types">
            <div id="type">
                <img  src="pics/villa.jpg" alt="Office" >
                <h3>Villas</h3>
            </div>
            <div id="type">
                <img src="pics/Apartment.jpg" alt="Apartment">
                <h3>Apartment</h3>
            </div>
            <div id="type">
                <img src="pics/shared-rooms.jpg" alt="House">
                <h3>Shared Rooms</h3>
            </div>
        </div>
        <div id="display">
            <div class="image">
                <img src="pics/keys.jpg" alt="keys">
            </div>
            <div class="figures">
                <h3>Trusted By Best Exclusive Agents</h3>
                <ul>
                    <li>Find excellent Deals</li>
                    <li>Friendly host and Fast support</li>
                    <li>List your own property</li>
                </ul>
            </div>
        </div>
        <div id="guestbook">
            <h3>Hear from our trustees</h3>
            <div class="reviewers">
                <div class="review">
                    <img src="pics/Frame-1.png" class="apros" alt="aprostrophe">
                    <p>Finding a rental property in Dublin was daunting until I stumbled upon this website. Their user-friendly interface and comprehensive listings made my search incredibly smooth. Thanks to their helpful team, I found the perfect apartment in no time!</p>
                    <div class="reviewer">
                        <img src="pics/Ellipse 10-1.png" alt="Kin Min Jon">
                    </div>
                </div>
                    <div class="review2">
                        <img src="pics/Frame-1.png" class="apros" alt="aprostrophe">
                        <p>I can't recommend this website enough! As a busy professional, I needed a hassle-free way to find a rental property in Dublin, and this site delivered. Their responsive customer service team answered all my questions promptly, and I'm now happily settled in my new home.</p>
                        <div class="reviewer">
                            <img src="pics/Ellipse 10-2.png" alt="Kin Min Jon">
                        </div>
                    </div>
                    <div class="review3">
                            <img src="pics/Frame-1.png" class="apros" alt="aprostrophe">
                            <p>After months of searching for the right place, I finally found my dream home through this website. The detailed property listings and virtual tours gave me a clear picture of each property, making it easy to narrow down my options. Thanks to this site, I'm now enjoying life in my ideal Dublin apartment!</p>
                            <div class="reviewer">
                                <img src="pics/Ellipse 10.png" alt="Kin Min Jon">
                            </div>
                    </div>
            </div>
        </div>  
        <div class="ref">
            <div class="fig">
                <h4>20k</h4>
                <p>Award Winning</p>
            </div>
            <div class="fig">
                <h4>14k</h4>
                <p>Property ready</p>
            </div>
            <div class="fig">
                <h4>2m</h4>
                <p>Customers</p>
            </div>
        </div>
        <div id="dream-house">
            <h3>Get Your Dream House</h3>
            <p>We'll reach out to you</p>
            <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if name and email are entered
    if (!empty($_POST['name']) && !empty($_POST['email'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];

        // Check if the email is valid
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // Email is valid, show success message
            echo "<p>Thank you for reaching out, $name! One of our agents will attend to you very shortly.</p>";
        } else {
            // Email is invalid, show error message
            echo "<p>Error: Please enter a valid email address.</p>";
        }
    } else {
        // Name or email is empty, show error message
        echo "<p>Error: Please enter your name and email address.</p>";
    }
}
?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="Name">Enter your name</label>
                <input type="text">
                <label for="E-mail">Enter your Email address</label>
                <input type="email">
                <button type="submit">Submit</button>
            </form>
        </div>
        
    </main>
    <footer>
        <div class="newsletter">
            <div>
                <h3>Subscribe to our newsletter</h3>
            </div>
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
                    <li><a href="#">AAbout Developer</a></li>
                    <li><a href="Career.html">Careers</a></li>
                </ul>
            </div>
        </div>
    </footer>
   
</body>
</html>
>>>>>>> origin/homepage-testing
