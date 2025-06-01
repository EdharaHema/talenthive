<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Business') {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("
    SELECT i.*, c.name as customer_name 
    FROM inquiries i 
    JOIN customers c ON i.customer_id = c.id 
    WHERE i.business_id = ?
");
$stmt->execute([$user['id']]);
$inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inquiries - TalentHive</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f2f6f5; margin: 0; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #1f7166; margin-bottom: 1rem; }
        .inquiry { border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .back { display: inline-block; margin-top: 1rem; color: #1f7166; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Inquiries</h2>
        <?php if (empty($inquiries)): ?>
            <p>No inquiries found.</p>
        <?php else: ?>
            <?php foreach ($inquiries as $inquiry): ?>
                <div class="inquiry">
                    <p><strong>From:</strong> <?php echo htmlspecialchars($inquiry['customer_name']); ?></p>
                    <p><strong>Message:</strong> <?php echo htmlspecialchars($inquiry['message']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($inquiry['status']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($inquiry['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="index.php" class="back">Back to Home</a>
    </div>
</body>
</html>