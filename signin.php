<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'config.php';

// Handle sign-in request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $response = ['success' => false, 'message' => ''];

    if (empty($email) || empty($password)) {
        $response['message'] = 'Email and password are required.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit;
    }

    try {
        // Check if the user exists in the customers table
        $stmt = $pdo->prepare("SELECT id, name, password FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($customer && password_verify($password, $customer['password'])) {
            $_SESSION['user'] = [
                'id' => $customer['id'],
                'name' => $customer['name'],
                'role' => 'Customer'
            ];
            $response['success'] = true;
            $response['message'] = 'Sign-in successful.';
            echo json_encode($response);
            exit;
        }

        // Check if the user exists in the businesses table
        // Check if the user exists in the businesses table
$stmt = $pdo->prepare("SELECT id, business_name, password, location, phone FROM businesses WHERE email = ?");
$stmt->execute([$email]);
$business = $stmt->fetch(PDO::FETCH_ASSOC);

if ($business && password_verify($password, $business['password'])) {
    $_SESSION['user'] = [
        'id' => $business['id'],
        'name' => $business['business_name'],
        'role' => 'Business',
        'location' => $business['location'],
        'phone' => $business['phone']
    ];
    $response['success'] = true;
    $response['message'] = 'Sign-in successful.';
    echo json_encode($response);
    exit;
}

        // If neither customer nor business matched
        $response['message'] = 'Invalid email or password.';
        echo json_encode($response);
        exit;
    } catch (PDOException $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
        echo json_encode($response);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TalentHive - Sign In</title>
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

        .signin-container {
            background: rgba(255, 255, 255, 0.664);
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 400px;
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

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
            color: #4A5568;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #2C7A7B;
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

        .forgot-password {
            display: block;
            font-size: 14px;
            color: #2C7A7B;
            text-decoration: none;
            margin: 15px 0;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .signup-link {
            font-size: 14px;
            color: #4A5568;
        }

        .signup-link a {
            color: #F28C82;
            text-decoration: none;
            font-weight: 600;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .signin-container {
                padding: 20px;
            }

            h2 {
                font-size: 20px;
            }

            button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="signin-container">
        <h2>Sign In to TalentHive</h2>
        <p>Access your account to connect with talent or showcase your skills.</p>
        <form id="signin-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <div class="error" id="email-error">Please enter a valid email.</div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <div class="error" id="password-error">Password must be at least 6 characters.</div>
            </div>
            <button type="submit">Sign In</button>
            <a href="#" class="forgot-password">Forgot Password?</a>
        </form>
        <div class="signup-link">
            Don’t have an account? <a href="signup.php">Sign Up</a>
        </div>
    </div>

    <script>
        document.getElementById('signin-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const emailError = document.getElementById('email-error');
            const passwordError = document.getElementById('password-error');
            
            let isValid = true;

            if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }

            if (!password || password.length < 6) {
                passwordError.style.display = 'block';
                isValid = false;
            } else {
                passwordError.style.display = 'none';
            }

            if (isValid) {
                try {
                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('password', password);

                    const response = await fetch('signin.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    console.log('Response from signin.php:', data); // Debug log

                    if (data.success) {
                        window.location.href = 'dashboard.php';
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                    alert('Error: ' + error.message);
                }
            }
        });
    </script>
</body>
</html>