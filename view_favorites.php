<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Customer') {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("
    SELECT f.*, b.business_name, b.location 
    FROM favorites f 
    JOIN businesses b ON f.business_id = b.id 
    WHERE f.customer_id = ?
");
$stmt->execute([$user['id']]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Favorites - TalentHive</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f2f6f5; margin: 0; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #1f7166; margin-bottom: 1rem; }
        .favorite { border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .back { display: inline-block; margin-top: 1rem; color: #1f7166; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Favorites</h2>
        <?php if (empty($favorites)): ?>
            <p>No favorites found.</p>
        <?php else: ?>
            <?php foreach ($favorites as $favorite): ?>
                <div class="favorite">
                    <p><strong>Business:</strong> <?php echo htmlspecialchars($favorite['business_name']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($favorite['location']); ?></p>
                    <p><strong>Added On:</strong> <?php echo htmlspecialchars($favorite['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="index.php" class="back">Back to Home</a>
    </div>
</body>
</html>