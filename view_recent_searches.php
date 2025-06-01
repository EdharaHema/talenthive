<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Customer') {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$stmt = $pdo->prepare("SELECT * FROM recent_searches WHERE customer_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$user['id']]);
$searches = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Searches - TalentHive</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f2f6f5; margin: 0; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #1f7166; margin-bottom: 1rem; }
        .search { border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .back { display: inline-block; margin-top: 1rem; color: #1f7166; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recent Searches</h2>
        <?php if (empty($searches)): ?>
            <p>No recent searches found.</p>
        <?php else: ?>
            <?php foreach ($searches as $search): ?>
                <div class="search">
                    <p><strong>Search Query:</strong> <?php echo htmlspecialchars($search['search_query']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($search['created_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="index.php" class="back">Back to Home</a>
    </div>
</body>
</html>