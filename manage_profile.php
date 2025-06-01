<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'Business') {
    header('Location: index.php');
    exit;
}

$user = $_SESSION['user'];
$message = '';

// Handle portfolio item addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_portfolio'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (empty($title)) {
        $message = 'Title is required.';
    } else {
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $upload_dir = 'C:/xampp/htdocs/talenthive/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $file_name = uniqid() . '_' . basename($_FILES['image']['name']);
            $target_file = $upload_dir . $file_name;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $file_name;
            } else {
                $message = 'Failed to upload image.';
            }
        }

        if (!$message) {
            try {
                $stmt = $pdo->prepare("INSERT INTO portfolio (business_id, title, description, image_path) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user['id'], $title, $description, $image_path]);
                $message = 'Portfolio item added successfully.';
            } catch (PDOException $e) {
                $message = 'Error: ' . $e->getMessage();
            }
        }
    }
}

// Handle portfolio item deletion
if (isset($_GET['delete'])) {
    $portfolio_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM portfolio WHERE id = ? AND business_id = ?");
        $stmt->execute([$portfolio_id, $user['id']]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            if ($item['image_path'] && file_exists('C:/xampp/htdocs/talenthive/uploads/' . $item['image_path'])) {
                unlink('C:/xampp/htdocs/talenthive/uploads/' . $item['image_path']);
            }
            $stmt = $pdo->prepare("DELETE FROM portfolio WHERE id = ? AND business_id = ?");
            $stmt->execute([$portfolio_id, $user['id']]);
            $message = 'Portfolio item deleted successfully.';
        }
    } catch (PDOException $e) {
        $message = 'Error: ' . $e->getMessage();
    }
}

// Fetch portfolio items
$stmt = $pdo->prepare("SELECT * FROM portfolio WHERE business_id = ? ORDER BY created_at DESC");
$stmt->execute([$user['id']]);
$portfolio_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Portfolio - TalentHive</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f2f6f5; margin: 0; padding: 2rem; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        h2 { color: #1f7166; margin-bottom: 1rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: #1f7166; }
        input, textarea { width: 100%; padding: 0.75rem; border: 1px solid #ccc; border-radius: 5px; }
        textarea { resize: vertical; min-height: 100px; }
        button { background-color: #1f7166; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #f76c5e; }
        .message { color: #f76c5e; margin-bottom: 1rem; }
        .portfolio-item { border: 1px solid #ccc; padding: 1rem; margin-bottom: 1rem; border-radius: 5px; }
        .portfolio-item img { max-width: 200px; border-radius: 5px; }
        .delete-btn { background-color: #f76c5e; color: white; padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer; }
        .back { display: inline-block; margin-top: 1rem; color: #1f7166; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Portfolio</h2>
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description (Optional)</label>
                <textarea id="description" name="description"></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image (Optional)</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" name="add_portfolio">Add Portfolio Item</button>
        </form>
        <h3>Your Portfolio Items</h3>
        <?php if (empty($portfolio_items)): ?>
            <p>No portfolio items yet.</p>
        <?php else: ?>
            <?php foreach ($portfolio_items as $item): ?>
                <div class="portfolio-item">
                    <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                    <p><?php echo htmlspecialchars($item['description'] ?? 'No description'); ?></p>
                    <?php if ($item['image_path']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php endif; ?>
                    <form method="GET" style="margin-top: 1rem;">
                        <button type="submit" name="delete" value="<?php echo $item['id']; ?>" class="delete-btn">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        <a href="index.php" class="back">Back to Home</a>
    </div>
</body>
</html>