<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SFPRK System</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>
    <?php include_once 'header.php';?>
    <div class="container">
        <div class="picture">
            <img src="paye.jpg">
            <a href="studentlogin.php" class="center-link">
                <button class="login-button">Login</button>
            </a>
        </div>
    </div>

    <section class="about">
        <h2>About Us</h2>
        <p class="details">It's time for payments</p>
    </section>

    <section class="developers-section">
        <h2 class="topic">Developers</h2>
        <div class="developers-row">
            <div class="developer-card">
                <div class="image-container">
                    <img src="images/shipping.png" alt="Anjil Shrestha">
                </div>
                <div class="developer-details">
                    <h2>Anjil Shrestha</h2>
                    <span>Software Engineering</span>
                </div>
            </div>
            <div class="developer-card">
                <div class="image-container">
                    <img src="images/payment.png" alt="Roshan Chaulagain">
                </div>
                <div class="developer-details">
                    <h2>Roshan Chaulagain</h2>
                    <span>Software Engineering</span>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="contact">
            <span>Contact</span>
            <ul>
                <li>Email: contact@sfprk.com</li>
                <li>Phone: (123) 456-7890</li>
                <li>Address: KTM,Nepal</li>
            </ul>
        </div>
        <div class="copyright">
            &copy; 2024 All rights reserved
        </div>
    </footer>
</body>
</html>
