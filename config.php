<?php
// config.php - Simple admin page to manage configuration
// In production, you'd want to add authentication here

$config = require __DIR__ . '/config/app.php';
$db = new SQLite3($config['db_path']);
$result = $db->query('SELECT key, value FROM config');

$config = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $config[$row['key']] = $row['value'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $min_price = $_POST['min_price'] ?? 300;
    $max_price = $_POST['max_price'] ?? 600;
    $deadline = $_POST['deadline'] ?? '2025-10-23';
    
    $db->exec("UPDATE config SET value = '$min_price' WHERE key = 'min_price'");
    $db->exec("UPDATE config SET value = '$max_price' WHERE key = 'max_price'");
    $db->exec("UPDATE config SET value = '$deadline' WHERE key = 'deadline'");
    
    header('Location: config.php?updated=1');
    exit;
}

$db->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Manager</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #5568d3;
        }
        .success {
            background: #4caf50;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>⚙️ Configuration Manager</h1>
        
        <?php if (isset($_GET['updated'])): ?>
            <div class="success">✓ Configuration updated successfully!</div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Minimum Flight Price ($)</label>
                <input type="number" name="min_price" value="<?php echo htmlspecialchars($config['min_price'] ?? 300); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Maximum Flight Price ($)</label>
                <input type="number" name="max_price" value="<?php echo htmlspecialchars($config['max_price'] ?? 600); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Booking Deadline</label>
                <input type="date" name="deadline" value="<?php echo htmlspecialchars($config['deadline'] ?? '2025-10-23'); ?>" required>
            </div>
            
            <button type="submit">Save Configuration</button>
        </form>
        
        <p style="margin-top: 20px; color: #666;">
            <a href="index.php" style="color: #667eea;">← Back to Pledge Page</a>
        </p>
    </div>
</body>
</html>
