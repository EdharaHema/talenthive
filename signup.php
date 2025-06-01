<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

// Enable error reporting for debugging (but suppress display to avoid breaking JSON)
ini_set('display_errors', 0); // Disable displaying errors in the output
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Log errors to a file for debugging
ini_set('log_errors', 1);
ini_set('error_log', 'C:\xampp\htdocs\talenthive\error_log.txt');

// Include database connection
try {
    require_once 'config.php';
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }
    die("Database connection failed. Please try again later.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json'); // Ensure JSON response
    $role = $_POST['role'] ?? '';
    $response = ['success' => false, 'message' => ''];

    error_log("Received POST request with role: $role");

    if ($role === 'customer') {
        $name = trim($_POST['customer-name'] ?? '');
        $email = trim($_POST['customer-email'] ?? '');
        $password = password_hash(trim($_POST['customer-password'] ?? ''), PASSWORD_DEFAULT);
        $location = trim($_POST['location'] ?? '');

        error_log("Customer sign-up attempt: Name=$name, Email=$email");

        if (empty($name) || empty($email) || empty($_POST['customer-password']) || empty($location)) {
            $response['message'] = 'All fields are required.';
            echo json_encode($response);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Invalid email format.';
            echo json_encode($response);
            exit;
        }

        try {
            $stmt = $pdo->prepare("SELECT id FROM customers WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $response['message'] = 'Email already registered.';
                echo json_encode($response);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO customers (name, email, location, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $location, $password]);
            $_SESSION['user'] = [
                'id' => $pdo->lastInsertId(),
                'name' => $name,
                'role' => 'Customer',
                'location' => $location
            ];
            $response['success'] = true;
            $response['message'] = 'Customer sign-up successful.';
        } catch (PDOException $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log("Customer sign-up error: " . $e->getMessage());
        }
    } elseif ($role === 'business') {
        $business_name = trim($_POST['business-name'] ?? '');
        $email = trim($_POST['business-email'] ?? '');
        $password = password_hash(trim($_POST['business-password'] ?? ''), PASSWORD_DEFAULT);
        $category = trim($_POST['business-category'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $phone = trim($_POST['phone-number'] ?? '');
        $about_me = trim($_POST['about-me'] ?? '');
        $ratings = !empty($_POST['ratings']) ? floatval($_POST['ratings']) : 0;
        $instagram = trim($_POST['instagram'] ?? '');
        $youtube = trim($_POST['youtube'] ?? '');
        $pinterest = trim($_POST['pinterest'] ?? '');
        $other_social = trim($_POST['other-social'] ?? '');

        // Handle profile picture upload (optional)
        $profile_picture = null;
        if (isset($_FILES['profile-picture']) && $_FILES['profile-picture']['size'] > 0) {
            $upload_dir = 'C:/xampp/htdocs/talenthive/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = uniqid() . '_' . basename($_FILES['profile-picture']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['profile-picture']['tmp_name'], $target_file)) {
                $profile_picture = $file_name;
            } else {
                $response['message'] = 'Failed to upload profile picture.';
                echo json_encode($response);
                exit;
            }
        }

        // Validate inputs
        if (empty($business_name) || empty($email) || empty($_POST['business-password']) || empty($category) || empty($location) || empty($phone) || empty($about_me)) {
            $response['message'] = 'All required fields must be filled.';
            echo json_encode($response);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Invalid email format.';
            echo json_encode($response);
            exit;
        }

        if (!preg_match('/(\+91)?[6-9]\d{9}/', str_replace([' ', '-'], '', $phone))) {
            $response['message'] = 'Invalid Indian phone number.';
            echo json_encode($response);
            exit;
        }

        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM businesses WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $response['message'] = 'Email already registered.';
                echo json_encode($response);
                exit;
            }

            // Look up the service ID for the category
            $stmt = $pdo->prepare("SELECT id FROM services WHERE name = ?");
            $stmt->execute([$category]);
            $service = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$service) {
                $response['message'] = 'Invalid category.';
                echo json_encode($response);
                exit;
            }
            $service_id = $service['id'];

            // Insert new business
            $stmt = $pdo->prepare("INSERT INTO businesses (business_name, email, password, location, phone, profile_picture, about_me, ratings, instagram, youtube, pinterest, other_social) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$business_name, $email, $password, $location, $phone, $profile_picture, $about_me, $ratings, $instagram, $youtube, $pinterest, $other_social]);
            $business_id = $pdo->lastInsertId();

            // Link the business to the service
            $stmt = $pdo->prepare("INSERT INTO business_services (business_id, service_id) VALUES (?, ?)");
            $stmt->execute([$business_id, $service_id]);

            $_SESSION['user'] = [
                'id' => $business_id,
                'name' => $business_name,
                'role' => 'Business',
                'location' => $location,
                'phone' => $phone
            ];
            $response['success'] = true;
            $response['message'] = 'Business sign-up successful.';
        } catch (PDOException $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            error_log("Business sign-up error: " . $e->getMessage());
        }
    } else {
        $response['message'] = 'Invalid role.';
        error_log("Invalid role received: $role");
    }

    echo json_encode($response);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentHive - Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), 
                        url('https://i.pinimg.com/736x/6d/1d/09/6d1d09c5846efdcbdd7a90a5824c3323.jpg') no-repeat center center/cover;
            background-attachment: fixed;
            padding: 20px;
        }

        .signup-container {
            background: rgba(255, 255, 255, 0.664);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        h2 {
            font-size: 24px;
            font-weight: 600;
            color: #1A3C5E;
            margin-bottom: 10px;
        }

        p {
            font-size: 14px;
            color: #4A5568;
            margin-bottom: 20px;
        }

        .role-selection {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .role-card {
            background: #dce9f1;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 20px;
            width: 45%;
            cursor: pointer;
            transition: transform 0.3s, border-color 0.3s;
        }

        .role-card:hover {
            transform: translateY(-5px);
            border-color: #2C7A7B;
        }

        .role-card h3 {
            font-size: 18px;
            color: #1f7166;
            margin-bottom: 10px;
        }

        .role-card p {
            font-size: 12px;
            color: #4A5568;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        label {
            font-size: 14px;
            color: #1A3C5E;
            display: block;
            margin-bottom: 5px;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
            color: #4A5568;
            transition: border-color 0.3s;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #2C7A7B;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .error {
            color: #F28C82;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #ee8e33;
            border: none;
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: #1A3C5E;
        }

        .signin-link {
            font-size: 14px;
            color: #4A5568;
            margin-top: 15px;
        }

        .signin-link a {
            color: #F28C82;
            text-decoration: none;
            font-weight: 600;
        }

        .signin-link a:hover {
            text-decoration: underline;
        }

        .hidden {
            display: none;
        }

        .file-upload {
            padding: 10px;
            border: 2px dashed #E2E8F0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
        }

        .file-upload:hover {
            border-color: #2C7A7B;
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }

        .portfolio-item {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 10px;
            text-align: center;
        }

        .social-links {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .social-links input {
            flex: 1;
            min-width: 200px;
        }

        .ratings-placeholder {
            margin-top: 10px;
            font-size: 14px;
            color: #4A5568;
        }

        @media (max-width: 480px) {
            .signup-container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            .role-selection {
                flex-direction: column;
                gap: 10px;
            }

            .role-card {
                width: 100%;
            }

            button {
                font-size: 14px;
            }

            .social-links input {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div id="role-selection">
            <h2>Join TalentHive</h2>
            <p>Choose your role to get started.</p>
            <div class="role-selection">
                <div class="role-card" onclick="showForm('customer')">
                    <h3>Customer</h3>
                    <p>Find and hire talented professionals for your needs.</p>
                </div>
                <div class="role-card" onclick="showForm('business')">
                    <h3>Business</h3>
                    <p>Showcase your skills and connect with clients.</p>
                </div>
            </div>
            <div class="signin-link">
                Already have an account? <a href="signin.php">Sign In</a>
            </div>
        </div>
        <div id="customer-form" class="hidden">
            <h2>Sign Up as a Customer</h2>
            <p>Create an account to discover and hire talent.</p>
            <form id="customer-signup-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="customer-name">Full Name</label>
                    <input type="text" id="customer-name" name="customer-name" placeholder="Enter your full name" required>
                    <div class="error" id="customer-name-error">Please enter your full name.</div>
                </div>
                <div class="form-group">
                    <label for="customer-email">Email Address</label>
                    <input type="email" id="customer-email" name="customer-email" placeholder="Enter your email" required>
                    <div class="error" id="customer-email-error">Please enter a valid email.</div>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="E.g., Mumbai, Maharashtra, India" required>
                    <div class="error" id="location-error">Please enter your location.</div>
                </div>
                <div class="form-group">
                    <label for="customer-password">Password</label>
                    <input type="password" id="customer-password" name="customer-password" placeholder="Enter your password" required>
                    <div class="error" id="customer-password-error">Password must be at least 6 characters.</div>
                </div>
                <input type="hidden" name="role" value="customer">
                <button type="submit">Sign Up</button>
            </form>
        </div>
        <div id="business-form" class="hidden">
            <h2>Sign Up as a Business</h2>
            <p>Create an account to showcase your skills.</p>
            <form id="business-signup-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="business-name">Business Name</label>
                    <input type="text" id="business-name" name="business-name" placeholder="Enter your business name" required>
                    <div class="error" id="business-name-error">Please enter your business name.</div>
                </div>
                <div class="form-group">
                    <label for="business-email">Email Address</label>
                    <input type="email" id="business-email" name="business-email" placeholder="Enter your email" required>
                    <div class="error" id="business-email-error">Please enter a valid email.</div>
                </div>
                <div class="form-group">
                    <label for="business-password">Password</label>
                    <input type="password" id="business-password" name="business-password" placeholder="Enter your password" required>
                    <div class="error" id="business-password-error">Password must be at least 6 characters.</div>
                </div>
                <div class="form-group">
                    <label for="business-category">Category</label>
                    <select id="business-category" name="business-category" required>
                        <option value="" disabled selected>Select a category</option>
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT name FROM services ORDER BY name");
                            while ($service = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . htmlspecialchars($service['name']) . '">' . htmlspecialchars($service['name']) . '</option>';
                            }
                        } catch (PDOException $e) {
                            error_log("Error fetching services: " . $e->getMessage());
                            echo '<option value="">Error loading categories</option>';
                        }
                        ?>
                    </select>
                    <div class="error" id="business-category-error">Please select a category.</div>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="E.g., Mumbai, Maharashtra, India" required>
                    <div class="error" id="location-error">Please enter your location.</div>
                </div>
                <div class="form-group">
                    <label for="phone-number">Phone Number</label>
                    <input type="tel" id="phone-number" name="phone-number" placeholder="+91 9876543210" pattern="(\+91)?[6-9]\d{9}" required>
                    <div class="error" id="phone-number-error">Please enter a valid Indian phone number (e.g., +91 9876543210).</div>
                </div>
                <div class="form-group">
                    <label for="profile-picture">Profile Picture (Optional)</label>
                    <input type="file" id="profile-picture" name="profile-picture" accept="image/*">
                    <div class="error" id="profile-picture-error">Please upload a valid image.</div>
                </div>
                <div class="form-group">
                    <label for="about-me">About Me</label>
                    <textarea id="about-me" name="about-me" placeholder="Tell us about yourself and your skills" required></textarea>
                    <div class="error" id="about-me-error">Please provide an About Me description.</div>
                </div>
                <div class="form-group">
                    <label for="ratings">Initial Rating (Optional)</label>
                    <input type="number" id="ratings" name="ratings" min="0" max="5" step="0.1" placeholder="E.g., 4.5">
                    <div class="ratings-placeholder">Ratings and reviews will be visible after client feedback.</div>
                </div>
                <div class="form-group">
                    <label>Social Media Links (Optional)</label>
                    <div class="social-links">
                        <input type="url" id="instagram" name="instagram" placeholder="Instagram URL">
                        <input type="url" id="youtube" name="youtube" placeholder="YouTube URL">
                        <input type="url" id="pinterest" name="pinterest" placeholder="Pinterest URL">
                        <input type="url" id="other-social" name="other-social" placeholder="Other Social Media URL">
                    </div>
                    <div class="error" id="social-links-error">Please provide at least one valid social media link.</div>
                </div>
                <input type="hidden" name="role" value="business">
                <button type="submit">Sign Up</button>
            </form>
        </div>
    </div>

    <script>
        function showForm(role) {
            document.getElementById('role-selection').classList.add('hidden');
            document.getElementById('customer-form').classList.add('hidden');
            document.getElementById('business-form').classList.add('hidden');
            document.getElementById(`${role}-form`).classList.remove('hidden');
            console.log('Showing form for role:', role);
        }

        async function submitForm(formId) {
            const form = document.getElementById(formId);
            const formData = new FormData(form);
            const errorElements = form.querySelectorAll('.error');
            errorElements.forEach(error => error.style.display = 'none');

            let isValid = true;

            // Validate required fields
            const requiredInputs = form.querySelectorAll('input[required], textarea[required], select[required]');
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    document.getElementById(`${input.id}-error`).style.display = 'block';
                    isValid = false;
                }
            });

            // Validate email
            const emailInput = form.querySelector('input[type="email"]');
            if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                document.getElementById(`${emailInput.id}-error`).style.display = 'block';
                isValid = false;
            }

            // Validate password
            const passwordInput = form.querySelector('input[type="password"]');
            if (passwordInput.value && passwordInput.value.length < 6) {
                document.getElementById(`${passwordInput.id}-error`).style.display = 'block';
                isValid = false;
            }

            // Validate phone number (for business form)
            const phoneInput = form.querySelector('input[type="tel"]');
            if (phoneInput && phoneInput.value && !/(\+91)?[6-9]\d{9}/.test(phoneInput.value.replace(/[\s-]/g, ''))) {
                document.getElementById('phone-number-error').style.display = 'block';
                isValid = false;
            }

            if (isValid) {
                console.log('Form is valid, submitting...');
                try {
                    const response = await fetch('signup.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const data = await response.json();
                    console.log('Response from signup.php:', data);
                    alert(data.message);
                    if (data.success) {
                        console.log('Sign-up successful, redirecting to dashboard...');
                        window.location.href = 'dashboard.php';
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    alert('Error: ' + error.message);
                }
            } else {
                console.log('Form validation failed');
            }
        }

        document.getElementById('customer-signup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Submitting customer form');
            submitForm('customer-signup-form');
        });

        document.getElementById('business-signup-form').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Submitting business form');
            submitForm('business-signup-form');
        });
    </script>
</body>
</html>