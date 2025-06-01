<?php session_start(); if(!isset($_SESSION['user'])){header('Location: signin.php'); exit; } ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TalentHive - Dashboard</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f2f6f5;
    }

    header {
      background-color: #1f7166;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: bold;
    }

    .header-buttons {
      position: relative;
      display: flex;
      align-items: center;
    }

    .header-buttons button {
      margin-left: 1rem;
      background-color: white;
      color: #1f7166;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
      transition: background-color 0.3s;
    }

    .header-buttons button:hover {
      background-color: #f76c5e;
      color: white;
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 100%;
      left: 0;
      background-color: white;
      min-width: 160px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
      border-radius: 5px;
      z-index: 1;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    .dropdown-content a {
      color: #1f7166;
      padding: 0.75rem 1rem;
      text-decoration: none;
      display: block;
      font-size: 0.9rem;
    }

    .dropdown-content a:hover {
      background-color: #f2f6f5;
      color: #f76c5e;
    }

    .user-dropdown .dropdown-content {
      right: 0;
      left: auto;
    }

    .hero {
      background-image: url('background.jpg');
      background-size: cover;
      background-position: center;
      color: white;
      text-align: left;
      padding: 5rem 2rem;
      position: relative;
    }

    .hero-overlay {
      max-width: 600px;
      background-color: rgba(0, 0, 0, 0.5);
      padding: 2rem;
      border-radius: 10px;
    }

    .hero h1 {
      font-size: 2.5rem;
      margin-bottom: 1rem;
    }

    .get-started {
      background-color: #f76c5e;
      color: white;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
    }

    .search-bar {
      margin: 2rem auto;
      display: flex;
      justify-content: center;
      gap: 1rem;
      max-width: 600px;
    }

    .search-bar input {
      padding: 0.75rem;
      font-size: 1rem;
      flex-grow: 1;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .search-bar button {
      background-color: #1f7166;
      color: white;
      border: none;
      padding: 0.75rem 1rem;
      border-radius: 5px;
      cursor: pointer;
    }

    .carousel-section {
      padding: 2rem;
      background-color: #ffffff;
    }

    .carousel-section h2 {
      text-align: center;
      margin-bottom: 1.5rem;
    }

    .carousel {
      display: flex;
      overflow-x: auto;
      gap: 1rem;
      padding: 1rem;
      scroll-behavior: smooth;
      scrollbar-width: none;
    }

    .carousel::-webkit-scrollbar {
      display: none;
    }

    .carousel img {
      flex: 0 0 auto;
      width: 200px;
      height: 150px;
      border-radius: 10px;
      object-fit: cover;
    }

    .testimonial {
      background-color: #fff;
      padding: 2rem;
      text-align: center;
    }

    footer {
      background-color: #1f7166;
      display: flex;
      justify-content: space-around;
      padding: 1rem;
      align-items: center;
      flex-wrap: wrap;
      color: #ffffff;
    }

    .footer-buttons button {
      background-color: white;
      color: #1f7166;
      border: none;
      padding: 0.5rem 1rem;
      margin: 0.25rem;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }

    .socials a {
      margin: 0 0.5rem;
      font-size: 1.2rem;
      color: white;
      text-decoration: none;
    }

    @media (max-width: 480px) {
      .header-buttons {
        flex-wrap: wrap;
      }

      .header-buttons button {
        margin: 0.25rem;
      }

      .dropdown-content {
        min-width: 120px;
      }

      .hero h1 {
        font-size: 1.8rem;
      }

      .search-bar {
        flex-direction: column;
      }

      .search-bar input, .search-bar button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">TALENTHIVE</div>
    <div class="header-buttons">
      <div class="dropdown">
        <button>Services</button>
        <div class="dropdown-content">
          <a href="baking.php">Baking</a>
          <a href="artandcraft.php">Crafts</a>
          <a href="catering.php">Catering</a>
          <a href="photography.php">Photography</a>
          <a href="makeup.php">Makeup</a>
          <a href="mehndi.php">Mehndi Design</a>
        </div>
      </div>
      <div class="dropdown user-dropdown">
        <button id="user-name"><?php echo htmlspecialchars($_SESSION['user']['name']); ?></button>
        <div class="dropdown-content" id="user-dropdown-content">
          <?php if ($_SESSION['user']['role'] === 'Business'): ?>
            <a href="#" onclick="manageProfile()">Manage Profile</a>
            <a href="#" onclick="managePortfolio()">Update Portfolio</a>
            <a href="#" onclick="viewBookings()">View Bookings</a>
            <a href="logout.php">Logout</a>
          <?php else: ?>
            <a href="#" onclick="viewProfile()">View Profile</a>
            <a href="#" onclick="viewBookings()">Bookings</a>
            <a href="#" onclick="viewRecentSearches()">Recent Searches</a>
            <a href="logout.php">Logout</a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </header>

  <section class="hero">
    <div class="hero-overlay">
      <h1 id="hero-title">Welcome Back, <span id="user-name-hero"><?php echo htmlspecialchars($_SESSION['user']['name']); ?>!</span></h1>
      <p id="hero-text">Loading personalized content...</p>
      <button class="get-started" id="hero-button">Get Started</button>
    </div>
  </section>

  <div class="search-bar">
    <input type="text" id="skill" placeholder="Search by skill" />
    <button onclick="searchTalent()">Search</button>
  </div>

  <section class="carousel-section">
    <h2 id="carousel-title">Discover Talents</h2>
    <div class="carousel" id="carousel">
      <img src="c_baking.jpg" alt="Baking">
      <img src="c_craft.jpg" alt="Crafts">
      <img src="c_catering.jpg" alt="Catering">
      <img src="c_photography.jpg" alt="Photography">
      <img src="c_makeup.jpg" alt="Makeup">
      <img src="c_mehndi.jpg" alt="Mehndi Design">
    </div>
  </section>

  <section class="testimonial">
    <p id="testimonial-text">
      At TalentHive, we believe that talent deserves to be seen. Our platform is designed to bridge the gap between skilled individuals and the clients or employers who need them. Whether you're a creative freelancer, a passionate hobbyist, or a growing small business, TalentHive helps you showcase your expertise and get discovered.
    </p>
  </section>

  <footer>
    <div class="footer-buttons">
      <a href="about.php"><button>About</button></a>
      <a href="contact.php"><button>Contact</button></a>
    </div>
  </footer>

  <script>
    // Fetch user data from backend
    fetch('dashboard.php')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const user = data.data;
          document.getElementById('user-name').textContent = user.name;
          document.getElementById('user-name-hero').textContent = user.name;

          const heroTitle = document.getElementById('hero-title');
          const heroText = document.getElementById('hero-text');
          const heroButton = document.getElementById('hero-button');
          const searchInput = document.getElementById('skill');
          const carouselTitle = document.getElementById('carousel-title');
          const testimonialText = document.getElementById('testimonial-text');

          if (user.role === 'Business') {
            heroTitle.textContent = `Welcome Back, ${user.name}!`;
            heroText.textContent = `Showcase your ${user.category} skills to clients across India, like in ${user.location}.`;
            heroButton.textContent = 'Manage Your Portfolio';
            heroButton.onclick = managePortfolio;
            searchInput.placeholder = `Search for clients or inquiries in ${user.location}`;
            carouselTitle.textContent = 'Explore Other Talents';
            testimonialText.textContent = `Grow your ${user.category} business with TalentHive. Connect with clients in cities like Mumbai, Delhi, and Bengaluru, and let your talent shine.`;
          } else {
            heroTitle.textContent = `Welcome Back, ${user.name}!`;
            heroText.textContent = `Find top talents for your events in ${user.location}, from Mehndi designers to caterers.`;
            heroButton.textContent = 'Browse Talents';
            heroButton.onclick = browseTalents;
            searchInput.placeholder = `Search for talents in ${user.location}`;
            carouselTitle.textContent = 'Recommended Talents';
            testimonialText.textContent = `Discover skilled professionals in ${user.location} for your next event. TalentHive connects you with the best Mehndi designers, photographers, and more.`;
          }
        } else {
          alert(data.message);
          window.location.href = 'signin.php';
        }
      })
      .catch(error => {
        alert('Error: ' + error.message);
      });

    function searchTalent() {
      const skill = document.getElementById('skill').value;
      alert(`Searching for: ${skill}`);
    }

    const carousel = document.getElementById('carousel');
    let scrollDirection = 1;
    const scrollSpeed = 1;

    function autoScroll() {
      carousel.scrollLeft += scrollSpeed * scrollDirection;
      if (carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth) {
        scrollDirection = -1;
      }
      if (carousel.scrollLeft <= 0) {
        scrollDirection = 1;
      }
    }

    setInterval(autoScroll, 20);

    function manageProfile() {
        window.location.href = 'manage_profile.php';
    }

    function managePortfolio() {
        window.location.href = 'manage_portfolio.php';
    }

    function viewBookings() {
        window.location.href = 'view_bookings.php';
    }

    function viewInquiries() {
        window.location.href = 'view_inquiries.php';
    }

    function viewProfile() {
        window.location.href = 'view_profile.php';
    }

    function viewFavorites() {
        window.location.href = 'view_favorites.php';
    }

    function viewRecentSearches() {
        window.location.href = 'view_recent_searches.php';
    }
    function browseTalents() {
      alert('Browsing talents...');
      window.location.href = 'categories/mehndi.php';
    }
  </script>
</body>
</html>