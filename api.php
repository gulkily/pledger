<?php
// api.php - Backend API for flight pledge system

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database setup
$db_file = 'pledges.db';
$db = new SQLite3($db_file);

// Create tables if they don't exist
$db->exec('
    CREATE TABLE IF NOT EXISTS pledges (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        percentage REAL NOT NULL,
        email TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
');

// Ensure legacy databases gain the email column
$column_check = $db->query('PRAGMA table_info(pledges)');
$hasEmailColumn = false;
while ($info = $column_check->fetchArray(SQLITE3_ASSOC)) {
    if (isset($info['name']) && $info['name'] === 'email') {
        $hasEmailColumn = true;
        break;
    }
}

if (!$hasEmailColumn) {
    $db->exec('ALTER TABLE pledges ADD COLUMN email TEXT');
}

$db->exec('
    CREATE TABLE IF NOT EXISTS config (
        key TEXT PRIMARY KEY,
        value TEXT NOT NULL
    )
');

// Initialize default config if empty
$config_count = $db->querySingle('SELECT COUNT(*) FROM config');
if ($config_count == 0) {
    $db->exec("INSERT INTO config (key, value) VALUES ('min_price', '300')");
    $db->exec("INSERT INTO config (key, value) VALUES ('max_price', '600')");
    $db->exec("INSERT INTO config (key, value) VALUES ('deadline', '2025-10-23')");
}

// Route handling
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_pledges':
        getPledges($db);
        break;
    
    case 'add_pledge':
        addPledge($db);
        break;
    
    case 'get_config':
        getConfig($db);
        break;
    
    case 'update_config':
        updateConfig($db);
        break;
    
    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getPledges($db) {
    $result = $db->query('SELECT id, name, percentage, created_at FROM pledges ORDER BY created_at DESC');
    
    $pledges = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $pledges[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'pledges' => $pledges,
        'total_percentage' => array_sum(array_column($pledges, 'percentage'))
    ]);
}

function addPledge($db) {
    $input = json_decode(file_get_contents('php://input'), true);

    $name = trim($input['name'] ?? '');
    $percentage = floatval($input['percentage'] ?? 0);
    $email = trim($input['email'] ?? '');

    // Validation
    if (empty($name)) {
        echo json_encode(['success' => false, 'error' => 'Name is required']);
        return;
    }

    if ($percentage <= 0 || $percentage > 100) {
        echo json_encode(['success' => false, 'error' => 'Percentage must be between 1 and 100']);
        return;
    }

    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Please enter a valid email address']);
        return;
    }

    // Sanitize name
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    // Insert pledge
    $stmt = $db->prepare('INSERT INTO pledges (name, percentage, email) VALUES (:name, :percentage, :email)');
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':percentage', $percentage, SQLITE3_FLOAT);
    if (!empty($email)) {
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    } else {
        $stmt->bindValue(':email', null, SQLITE3_NULL);
    }
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Pledge added successfully',
            'pledge_id' => $db->lastInsertRowID()
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add pledge']);
    }
}

function getConfig($db) {
    $result = $db->query('SELECT key, value FROM config');
    
    $config = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $config[$row['key']] = $row['value'];
    }
    
    echo json_encode([
        'success' => true,
        'config' => $config
    ]);
}

function updateConfig($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $allowed_keys = ['min_price', 'max_price', 'deadline'];
    
    foreach ($input as $key => $value) {
        if (in_array($key, $allowed_keys)) {
            $stmt = $db->prepare('UPDATE config SET value = :value WHERE key = :key');
            $stmt->bindValue(':key', $key, SQLITE3_TEXT);
            $stmt->bindValue(':value', $value, SQLITE3_TEXT);
            $stmt->execute();
        }
    }
    
    echo json_encode(['success' => true, 'message' => 'Config updated']);
}

$db->close();
?>
