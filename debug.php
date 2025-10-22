<?php
// debug.php - Diagnostic script to check database connection and data

header('Content-Type: text/html; charset=utf-8');

echo "<h1>Database Debug Information</h1>";

$config = require __DIR__ . '/config/app.php';
$db_file = $config['db_path'];

// Check if file exists
echo "<h2>File Check</h2>";
echo "Database file: $db_file<br>";
echo "File exists: " . (file_exists($db_file) ? 'YES' : 'NO') . "<br>";
echo "File readable: " . (is_readable($db_file) ? 'YES' : 'NO') . "<br>";
echo "File path: " . realpath($db_file) . "<br>";

try {
    $db = new SQLite3($db_file);
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check tables
    echo "<h2>Tables</h2>";
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
    while ($row = $tables->fetchArray()) {
        echo "- " . $row['name'] . "<br>";
    }
    
    // Check pledges table schema
    echo "<h2>Pledges Table Schema</h2>";
    $schema = $db->query("PRAGMA table_info(pledges)");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Not Null</th><th>Default</th></tr>";
    while ($row = $schema->fetchArray(SQLITE3_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['cid'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['type'] . "</td>";
        echo "<td>" . $row['notnull'] . "</td>";
        echo "<td>" . $row['dflt_value'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Count pledges
    echo "<h2>Pledge Count</h2>";
    $count = $db->querySingle('SELECT COUNT(*) FROM pledges');
    echo "Total pledges: <strong>$count</strong><br>";
    
    // Show all pledges with different query methods
    echo "<h2>Pledges (Method 1: fetchArray SQLITE3_ASSOC)</h2>";
    $result = $db->query('SELECT id, name, percentage, created_at FROM pledges ORDER BY created_at DESC');
    echo "<pre>";
    $pledges1 = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $pledges1[] = $row;
    }
    print_r($pledges1);
    echo "</pre>";
    
    // Try another method
    echo "<h2>Pledges (Method 2: fetchArray default)</h2>";
    $result = $db->query('SELECT * FROM pledges');
    echo "<pre>";
    while ($row = $result->fetchArray()) {
        print_r($row);
    }
    echo "</pre>";
    
    // Test the exact API function
    echo "<h2>API Function Test</h2>";
    function testGetPledges($db) {
        $result = $db->query('SELECT id, name, percentage, created_at FROM pledges ORDER BY created_at DESC');
        
        $pledges = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $pledges[] = $row;
        }
        
        return [
            'success' => true,
            'pledges' => $pledges,
            'total_percentage' => array_sum(array_column($pledges, 'percentage'))
        ];
    }
    
    $apiResult = testGetPledges($db);
    echo "<pre>";
    print_r($apiResult);
    echo "</pre>";
    
    $db->close();
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='api.php?action=get_pledges'>Test actual API</a></p>";
?>
