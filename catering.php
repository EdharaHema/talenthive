<?php
session_start();

// Enable error reporting for debugging (but suppress display in production)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Log errors to a file for debugging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\talenthive\error_log.txt');

// Include database connection
require_once 'config.php';

// Handle booking request via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'book_now') {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Customer') {
        $response['message'] = 'Please log in as a customer to book a service.';
        echo json_encode($response);
        exit;
    }

    $business_id = $_POST['business_id'] ?? 0;
    $customer_id = $_SESSION['user']['id'];

    try {
        // Fetch the service ID for "Catering"
        $stmt = $pdo->prepare("SELECT id FROM services WHERE name = ?");
        $stmt->execute(['Catering']);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$service) {
            $response['message'] = 'Service not found.';
            echo json_encode($response);
            exit;
        }
        $service_id = $service['id'];

        // Create a booking
        $booking_date = date('Y-m-d H:i:s', strtotime('+1 day')); // Example: Booking for tomorrow
        $stmt = $pdo->prepare("INSERT INTO bookings (customer_id, business_id, service_id, booking_date, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$customer_id, $business_id, $service_id, $booking_date]);
        $response['success'] = true;
        $response['message'] = 'Booking created successfully. Check your bookings for details.';
    } catch (PDOException $e) {
        $response['message'] = 'Error creating booking: ' . $e->getMessage();
        error_log("Booking error: " . $e->getMessage());
    }

    echo json_encode($response);
    exit;
}

// Fetch catering providers from the businesses table
try {
    $stmt = $pdo->prepare("
        SELECT b.id, b.business_name, b.location, b.profile_picture, b.about_me, b.ratings, b.phone
        FROM businesses b
        JOIN business_services bs ON b.id = bs.business_id
        JOIN services s ON bs.service_id = s.id
        WHERE s.name = ?
    ");
    $stmt->execute(['Catering']);
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching catering providers: " . $e->getMessage();
    error_log("Error fetching catering providers: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TalentHive - Catering</title>
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

    .hero {
      background-image: url('h_catering.jpg');
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

    .explore {
      background-color: #f76c5e;
      color: white;
      padding: 0.75rem 1.5rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 1rem;
    }

    .talent-section {
      padding: 2rem;
      background-color: #ffffff;
      text-align: center;
    }

    .talent-section h2 {
      margin-bottom: 1.5rem;
    }

    .talent-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Reduced min width to 150px */
      gap: 1.5rem;
      padding: 1rem;
    }

    .talent-card {
      background-color: #f2f6f5;
      border-radius: 10px;
      padding: 1rem;
      text-align: left;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      aspect-ratio: 1 / 1; /* Ensures the card is a square */
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      width: 300px; /* Corrected width to match the image height for a smaller square */
    }

    .talent-card img {
      width: 100%;
      height: 150px; /* Reverted to original height */
      object-fit: cover;
      border-radius: 5px;
      margin-bottom: 1rem;
    }

    .talent-card h3 {
      font-size: 1.2rem;
      color: #1f7166;
      margin-bottom: 0.5rem;
    }

    .talent-card p {
      font-size: 0.9rem;
      color: #4A5568;
      flex-grow: 1;
    }

    .talent-card .button-group {
      display: flex;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }

    .talent-card button {
      background-color: #1f7166;
      color: white;
      padding: 0.5rem 1rem;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      flex: 1;
    }

    .talent-card .book-now {
      background-color: #f76c5e;
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

      .talent-grid {
        grid-template-columns: 1fr;
      }

      .talent-card .button-group {
        flex-direction: column;
      }

      .talent-card button {
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
      <?php if (isset($_SESSION['user'])): ?>
        <a href="dashboard.php"><button>Dashboard</button></a>
        <a href="logout.php"><button>Logout</button></a>
      <?php else: ?>
        <a href="signin.php"><button>Login</button></a>
        <a href="signup.php"><button>Sign Up</button></a>
      <?php endif; ?>
    </div>
  </header>

  <section class="hero">
    <div class="hero-overlay">
      <h1>Discover Skilled Caterers</h1>
      <p>Find talented catering providers in India for your needs.</p>
      <button class="explore" onclick="document.querySelector('.talent-section').scrollIntoView({ behavior: 'smooth' })">Explore Caterers</button>
    </div>
  </section>

  <section class="talent-section">
    <h2>Featured Caterers</h2>
    <?php if (isset($error)): ?>
      <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php elseif (empty($providers)): ?>
      <p>No catering providers found in this category.</p>
    <?php else: ?>
      <div class="talent-grid">
        <?php foreach ($providers as $provider): ?>
          <div class="talent-card">
            <?php if (!empty($provider['profile_picture'])): ?>
              <img src="uploads/<?php echo htmlspecialchars($provider['profile_picture']); ?>" alt="<?php echo htmlspecialchars($provider['business_name']); ?>">
            <?php else: ?>
              <img src="images/default-profile.jpg" alt="Default Profile">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($provider['business_name']); ?></h3>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($provider['location']); ?></p>
            <p><?php echo htmlspecialchars($provider['about_me']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($provider['phone'] ?? 'Not provided'); ?></p>
            <p><strong>Ratings:</strong> <?php echo htmlspecialchars($provider['ratings']); ?>/5</p>
            <div class="button-group">
              <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'Customer'): ?>
                <button class="book-now" onclick="bookNow(<?php echo htmlspecialchars($provider['id']); ?>)">Book Now</button>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <footer>
    <div class="footer-buttons">
      <a href="about.php"><button>About</button></a>
      <a href="contact.php"><button>Contact</button></a>
    </div>
  </footer>

  <script>
    function bookNow(businessId) {
      fetch('catering.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=book_now&business_id=${businessId}`
      })
      .then(response => response.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          window.location.href = 'view_bookings.php';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error booking: ' + error.message);
      });
    }
  </script>
</body>
</html>