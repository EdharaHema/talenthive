<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TalentHive</title>
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

    /* Dropdown Styles */
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

    .hero {
      background-image: url('cooking.jpg');
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
          <a href="categories/baking.html">Baking</a>
          <a href="categories/crafts.html">Crafts</a>
          <a href="categories/catering.html">Catering</a>
          <a href="categories/photography.html">Photography</a>
          <a href="categories/makeup.html">Makeup</a>
          <a href="categories/design.html">Design</a>
        </div>
      </div>
      <a href="signin.html"><button>Login</button></a>
      <a href="signup.html"><button>Sign Up</button></a>
    </div>
  </header>

  <section class="hero">
    <div class="hero-overlay">
      <h1>Find the Right Talent for Your Needs</h1>
      <button class="get-started">Get Started</button>
    </div>
  </section>

  <div class="search-bar">
    <input type="text" id="skill" placeholder="Search by skill" />
    <button onclick="searchTalent()">Search</button>
  </div>

  <section class="carousel-section">
    <h2>Discover Talents</h2>
    <div class="carousel" id="carousel">
      <img src="baking.jpg" alt="Baking">
      <img src="craft.jpg" alt="Crafts">
      <img src="catering.jpg" alt="Catering">
      <img src="photography.jpg" alt="Photography">
      <img src="makeup.jpg" alt="Makeup">
      <img src="design.jpg" alt="Design">
    </div>
  </section>

  <section class="testimonial">
    <p>
      At TalentHive, we believe that talent deserves to be seen. Our platform is designed to bridge the gap between skilled individuals and the clients or employers who need them. Whether you're a creative freelancer, a passionate hobbyist, or a growing small business, TalentHive helps you showcase your expertise and get discovered.
    </p>
  </section>

  <footer>
    <div class="footer-buttons">
      <a href="about.html"><button>About</button></a>
      <a href="contact.html"><button>Contact</button></a>
    </div>
    <div class="socials">
      <a href="#">FB</a>
      <a href="#">TW</a>
      <a href="#">IG</a>
    </div>
  </footer>

  <script>
    function searchTalent() {
      const skill = document.getElementById("skill").value;
      alert(`Searching for: ${skill}`);
    }

    // Bidirectional auto-scroll
    const carousel = document.getElementById('carousel');
    let scrollDirection = 1; // 1 = right, -1 = left
    const scrollSpeed = 1; // pixels per interval

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
  </script>
</body>
</html>