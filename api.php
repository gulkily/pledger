<?php
// api.php - Backend API for flight pledge system

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Pledger-Session');

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
        session_token TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
');

// Ensure legacy databases gain required columns
$column_check = $db->query('PRAGMA table_info(pledges)');
$hasEmailColumn = false;
$hasSessionColumn = false;
while ($info = $column_check->fetchArray(SQLITE3_ASSOC)) {
    if (!isset($info['name'])) {
        continue;
    }
    if ($info['name'] === 'email') {
        $hasEmailColumn = true;
    }
    if ($info['name'] === 'session_token') {
        $hasSessionColumn = true;
    }
}

if (!$hasEmailColumn) {
    $db->exec('ALTER TABLE pledges ADD COLUMN email TEXT');
}

if (!$hasSessionColumn) {
    $db->exec('ALTER TABLE pledges ADD COLUMN session_token TEXT');
}

$db->exec('
    CREATE TABLE IF NOT EXISTS sessions (
        session_token TEXT PRIMARY KEY,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )
');

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
$sessionToken = ensureSession($db);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_pledges':
        getPledges($db, $sessionToken);
        break;

    case 'add_pledge':
        addPledge($db, $sessionToken);
        break;

    case 'get_config':
        getConfig($db, $sessionToken);
        break;

    case 'update_config':
        updateConfig($db);
        break;

    case 'update_pledge':
        updatePledge($db, $sessionToken);
        break;

    case 'delete_pledge':
        deletePledge($db, $sessionToken);
        break;

    default:
        echo json_encode(['error' => 'Invalid action']);
        break;
}

function getPledges($db, $sessionToken) {
    $result = $db->query('SELECT id, name, percentage, email, session_token, created_at FROM pledges ORDER BY created_at DESC');
    
    $pledges = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $owned = isset($row['session_token']) && $row['session_token'] === $sessionToken;
        unset($row['session_token']);
        if (!$owned) {
            unset($row['email']);
        }
        $row['owned_by_session'] = $owned;
        $pledges[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'pledges' => $pledges,
        'total_percentage' => array_sum(array_column($pledges, 'percentage')),
        'session_token' => $sessionToken
    ]);
}

function addPledge($db, $sessionToken) {
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
    $stmt = $db->prepare('INSERT INTO pledges (name, percentage, email, session_token) VALUES (:name, :percentage, :email, :session_token)');
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':percentage', $percentage, SQLITE3_FLOAT);
    if (!empty($email)) {
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    } else {
        $stmt->bindValue(':email', null, SQLITE3_NULL);
    }
    $stmt->bindValue(':session_token', $sessionToken, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Pledge added successfully',
            'pledge_id' => $db->lastInsertRowID(),
            'session_token' => $sessionToken
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add pledge']);
    }
}

function getConfig($db, $sessionToken) {
    $result = $db->query('SELECT key, value FROM config');
    
    $config = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $config[$row['key']] = $row['value'];
    }
    
    echo json_encode([
        'success' => true,
        'config' => $config,
        'session_token' => $sessionToken
    ]);
}

function updatePledge($db, $sessionToken) {
    $input = json_decode(file_get_contents('php://input'), true);

    $pledgeId = intval($input['pledge_id'] ?? $input['id'] ?? 0);
    $name = trim($input['name'] ?? '');
    $percentage = floatval($input['percentage'] ?? 0);
    $email = trim($input['email'] ?? '');

    if ($pledgeId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid pledge identifier']);
        return;
    }

    $pledge = fetchPledgeById($db, $pledgeId);
    if (!$pledge) {
        echo json_encode(['success' => false, 'error' => 'Pledge not found']);
        return;
    }

    if (empty($pledge['session_token']) || $pledge['session_token'] !== $sessionToken) {
        echo json_encode(['success' => false, 'error' => 'You are not authorized to update this pledge']);
        return;
    }

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

    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    $stmt = $db->prepare('UPDATE pledges SET name = :name, percentage = :percentage, email = :email WHERE id = :id');
    $stmt->bindValue(':name', $name, SQLITE3_TEXT);
    $stmt->bindValue(':percentage', $percentage, SQLITE3_FLOAT);
    if (!empty($email)) {
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
    } else {
        $stmt->bindValue(':email', null, SQLITE3_NULL);
    }
    $stmt->bindValue(':id', $pledgeId, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Pledge updated successfully',
            'session_token' => $sessionToken
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update pledge']);
    }
}

function deletePledge($db, $sessionToken) {
    $input = json_decode(file_get_contents('php://input'), true);
    $pledgeId = intval($input['pledge_id'] ?? $input['id'] ?? 0);

    if ($pledgeId <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid pledge identifier']);
        return;
    }

    $pledge = fetchPledgeById($db, $pledgeId);
    if (!$pledge) {
        echo json_encode(['success' => false, 'error' => 'Pledge not found']);
        return;
    }

    if (empty($pledge['session_token']) || $pledge['session_token'] !== $sessionToken) {
        echo json_encode(['success' => false, 'error' => 'You are not authorized to delete this pledge']);
        return;
    }

    $stmt = $db->prepare('DELETE FROM pledges WHERE id = :id');
    $stmt->bindValue(':id', $pledgeId, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Pledge removed',
            'session_token' => $sessionToken
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete pledge']);
    }
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
function ensureSession($db) {
    $cookieName = 'pledger_session';
    $cookieToken = $_COOKIE[$cookieName] ?? '';
    $headerToken = $_SERVER['HTTP_X_PLEDGER_SESSION'] ?? '';

    if (!empty($headerToken) && !isHexToken($headerToken)) {
        $headerToken = '';
    }

    $token = '';

    if (!empty($headerToken) && isSessionKnown($db, $headerToken)) {
        $token = $headerToken;
    } elseif (!empty($cookieToken) && isHexToken($cookieToken) && isSessionKnown($db, $cookieToken)) {
        $token = $cookieToken;
    }

    if (empty($token) && !empty($headerToken)) {
        // Header token not known but well-formed: adopt it
        $token = $headerToken;
        storeSessionToken($db, $token);
    }

    if (empty($token)) {
        $token = generateSessionToken();
        storeSessionToken($db, $token);
    }

    // Refresh cookie with consistent flags
    $cookieOptions = [
        'expires' => time() + (60 * 60 * 24 * 90),
        'path' => '/',
        'domain' => '',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => false,
        'samesite' => 'Lax'
    ];
    setcookie($cookieName, $token, $cookieOptions);

    storeSessionToken($db, $token);

    return $token;
}

function generateSessionToken() {
    try {
        return bin2hex(random_bytes(32));
    } catch (Exception $e) {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }
}

function storeSessionToken($db, $token) {
    if (empty($token)) {
        return;
    }
    $stmt = $db->prepare('INSERT OR IGNORE INTO sessions (session_token) VALUES (:token)');
    $stmt->bindValue(':token', $token, SQLITE3_TEXT);
    $stmt->execute();
}

function isSessionKnown($db, $token) {
    $stmt = $db->prepare('SELECT 1 FROM sessions WHERE session_token = :token LIMIT 1');
    $stmt->bindValue(':token', $token, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result ? $result->fetchArray(SQLITE3_NUM) : false;
    return $row !== false;
}

function isHexToken($token) {
    return is_string($token) && preg_match('/^[a-f0-9]{64}$/', $token) === 1;
}

function fetchPledgeById($db, $pledgeId) {
    $stmt = $db->prepare('SELECT id, session_token FROM pledges WHERE id = :id LIMIT 1');
    $stmt->bindValue(':id', $pledgeId, SQLITE3_INTEGER);
    $result = $stmt->execute();
    return $result ? $result->fetchArray(SQLITE3_ASSOC) : false;
}
$db->close();
?>
