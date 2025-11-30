<?php
// Test database connection
$host = '127.0.0.1';
$db   = 'mor_system';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Database connection successful!\n";
    
    // Test if database exists and is accessible
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "Database exists but has no tables.\n";
    } else {
        echo "Database contains " . count($tables) . " tables:\n";
        foreach ($tables as $table) {
            echo "- $table\n";
        }
    }
    
} catch (\PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    
    // Try connecting to MySQL server without specifying database
    try {
        $dsn_server = "mysql:host=$host;charset=$charset";
        $pdo_server = new PDO($dsn_server, $user, $pass, $options);
        echo "Connected to MySQL server but database '$db' not found.\n";
        
        // Show available databases
        $stmt = $pdo_server->query("SHOW DATABASES");
        $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Available databases:\n";
        foreach ($databases as $database) {
            if ($database !== 'information_schema' && $database !== 'mysql' && $database !== 'performance_schema') {
                echo "- $database\n";
            }
        }
    } catch (\PDOException $e2) {
        echo "Cannot connect to MySQL server: " . $e2->getMessage() . "\n";
    }
}
?>
