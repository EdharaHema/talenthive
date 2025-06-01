<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TalentHive</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f6f5;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Header */
        .header {
            background-color: #1f7166;
            padding: 15px 0;
        }

        .header nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
        }

        .header .logo {
            font-size: 24px;
            color: #fff;
        }

        .header ul {
            list-style: none;
            display: flex;
        }

        .header ul li {
            margin-left: 20px;
        }

        .header ul li a {
            color: #fff;
            font-size: 16px;
            text-transform: uppercase;
        }

        .header ul li a.active {
            font-weight: bold;
            color: #f76c5e;
        }

        /* About Section */
        .about-section {
            padding: 40px 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-section h1 {
            color: #1f7166;
            text-align: center;
            font-size: 36px;
            margin-bottom: 20px;
        }

        .intro {
            font-size: 18px;
            text-align: center;
            margin-bottom: 40px;
            color: #333;
        }

        .why-choose-us, .how-it-works, .benefits {
            margin-bottom: 40px;
        }

        h2 {
            color: #1f7166;
            font-size: 28px;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .content-box {
            background-color: #1f7166;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .content-box h3 {
            color: #fff;
            font-size: 22px;
        }

        .content-box p {
            color: #fff;
        }

        /* Meet the Team */
        .meet-the-team {
            margin-bottom: 40px;
            text-align: center;
        }

        .meet-the-team img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* Footer */
        .footer {
            background-color: #1f7166;
            text-align: center;
            color: #fff;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
   <header class="header">
<a  class="logo">TALENTHIVE</a>
</header>
    <!-- Main About Section -->
    <section class="about-section">
        <h1>About TalentHive</h1>
        <p class="intro">
            Welcome to <strong>TalentHive</strong>, the ultimate platform designed to connect skilled professionals in fields like <strong>baking</strong>, <strong>crafts</strong>, and <strong>catering</strong> with employers and clients. Our mission is to empower talented individuals to showcase their skills, build their businesses, and unlock new opportunities.
        </p>

        <div class="why-choose-us">
            <h2>Why Choose TalentHive?</h2>
            <ul>
                <li>Create a Personalized Profile</li>
                <li>Easy Talent Discovery</li>
                <li>Increase Your Visibility</li>
                <li>Link to Social Media</li>
                <li>Flexible Communication</li>
            </ul>
        </div>

        <div class="how-it-works">
            <h2>How TalentHive Works</h2>
            <div class="content-box">
                <h3>For Skilled Professionals</h3>
                <p>Create a custom profile, upload photos of your work, and provide contact details to attract employers.</p>
            </div>
            <div class="content-box">
                <h3>For Employers/Clients</h3>
                <p>Browse profiles and find the right fit for your project needs. Reach out directly through the platform.</p>
            </div>
        </div>

        <div class="benefits">
            <h2>Benefits of Using TalentHive</h2>
            <ul>
                <li>User-friendly and intuitive design</li>
                <li>Expand your professional network</li>
                <li>Showcase your work with ease</li>
                <li>Find the right match for your project needs</li>
            </ul>
        </div>

        <div class="meet-the-team">
            <h2>Meet the Team</h2>
            <img src="about.jpg" alt="Team at TalentHive">
            <p>Our team is dedicated to helping you succeed. We're here to provide support, improve our services, and ensure that your experience is seamless.</p>
        </div>

        <div class="contact-us">
            <h2>Contact Us</h2>
            <p>Have any questions or need assistance? Visit our <a href="contact.html">Contact Page</a> to get in touch with our support team. We’re happy to help!</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 TalentHive. All Rights Reserved.</p>
    </footer>

    <!-- JavaScript -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Mark the current page as active in the navigation
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.header ul li a');
            
            navLinks.forEach(link => {
                if (link.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>
