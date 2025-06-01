<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];

// Handle booking status updates (Accept/Decline)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['accept', 'decline'])) {
    header('Content-Type: application/json');
    $response = ['success' => false, 'message' => ''];

    if ($user['role'] !== 'Business') {
        $response['message'] = 'Only businesses can update booking status.';
        echo json_encode($response);
        exit;
    }

    $booking_id = $_POST['booking_id'] ?? 0;
    $new_status = $_POST['action'] === 'accept' ? 'accepted' : 'declined';

    try {
        // Verify the booking belongs to this business
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND business_id = ? AND status = 'pending'");
        $stmt->execute([$booking_id, $user['id']]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            $response['message'] = 'Booking not found or already processed.';
            echo json_encode($response);
            exit;
        }

        // Update the booking status
        $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $booking_id]);
        $response['success'] = true;
        $response['message'] = "Booking $new_status successfully.";
    } catch (PDOException $e) {
        $response['message'] = 'Error updating booking: ' . $e->getMessage();
        error_log("Booking update error: " . $e->getMessage());
    }

    echo json_encode($response);
    exit;
}

// Fetch bookings
$bookings = [];
if ($user['role'] === 'Business') {
    $stmt = $pdo->prepare("
        SELECT b.*, c.name as customer_name, s.name as service_name 
        FROM bookings b 
        JOIN customers c ON b.customer_id = c.id 
        JOIN services s ON b.service_id = s.id 
        WHERE b.business_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user['id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("
        SELECT b.*, b2.business_name, s.name as service_name 
        FROM bookings b 
        JOIN businesses b2 ON b.business_id = b2.id 
        JOIN services s ON b.service_id = s.id 
        WHERE b.customer_id = ?
        ORDER BY b.created_at DESC
    ");
    $stmt->execute([$user['id']]);
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bookings - TalentHive</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f2f6f5; margin: 0; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #1f7166; margin-bottom: 1rem; }
        .booking { border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .booking .status { font-weight: bold; }
        .booking .status.pending { color: #f76c5e; }
        .booking .status.accepted { color: #1f7166; }
        .booking .status.declined { color: #888; }
        .action-buttons { display: flex; gap: 0.5rem; margin-top: 0.5rem; }
        .action-buttons button { padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; }
        .action-buttons .accept { background-color: #1f7166; color: white; }
        .action-buttons .decline { background-color: #f76c5e; color: white; }
        .back { display: inline-block; margin-top: 1rem; color: #1f7166; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Bookings</h2>
        <?php if (empty($bookings)): ?>
            <p>No bookings found.</p>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <div class="booking">
                    <?php if ($user['role'] === 'Business'): ?>
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($booking['customer_name']); ?></p>
                    <?php else: ?>
                        <p><strong>Business:</strong> <?php echo htmlspecialchars($booking['business_name']); ?></p>
                    <?php endif; ?>
                    <p><strong>Service:</strong> <?php echo htmlspecialchars($booking['service_name']); ?></p>
                    <p><strong>Booking Date:</strong> <?php echo htmlspecialchars($booking['booking_date']); ?></p>
                    <p><strong>Status:</strong> <span class="status <?php echo htmlspecialchars($booking['status']); ?>"><?php echo htmlspecialchars(ucfirst($booking['status'])); ?></span></p>
                    <?php if ($user['role'] === 'Business' && $booking['status'] === 'pending'): ?>
                        <div class="action-buttons">
                            <button class="accept" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'accept')">Accept</button>
                            <button class="decline" onclick="updateBookingStatus(<?php echo $booking['id']; ?>, 'decline')">Decline</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="index.php" class="back">Back to Home</a>
    </div>

    <script>
        function updateBookingStatus(bookingId, action) {
            fetch('view_bookings.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=${action}&booking_id=${bookingId}`
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.success) {
                    location.reload(); // Refresh the page to show updated status
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating booking: ' + error.message);
            });
        }
    </script>
</body>
</html>