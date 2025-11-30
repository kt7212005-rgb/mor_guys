<?php
// Database configuration
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dbname = 'mor_system';

// Function to create a PDO connection
function getPdoConnection($db = null) {
    global $host, $user, $pass, $charset;
    
    $dsn = "mysql:host=$host" . ($db ? ";dbname=$db" : '') . ";charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    return new PDO($dsn, $user, $pass, $options);
}

try {
    // First, try to connect to MySQL server without database
    $pdo = getPdoConnection();
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '$dbname'");
    
    if ($stmt->rowCount() === 0) {
        // Database doesn't exist, create it
        echo "<div class='alert alert-warning'>Database '$dbname' not found. Creating it now...</div>";
        $pdo->exec("CREATE DATABASE `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<div class='alert alert-success'>✅ Database '$dbname' created successfully!</div>";
        
        // Now include the database setup file to create tables
        echo "<div class='alert alert-info'>🔄 Setting up database tables...</div>";
        require_once 'database_setup.php';
        echo "<div class='alert alert-success'>✅ Database setup completed successfully!</div>";
    } else {
        // Database exists, just test the connection
        $pdo = getPdoConnection($dbname);
        echo "<div class='alert alert-success'>✅ Successfully connected to database '$dbname'!</div>";
        
        // Test query
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "<div class='alert alert-info'>📊 Found " . count($tables) . " tables in the database.</div>";
        } else {
            echo "<div class='alert alert-warning'>⚠️ No tables found in the database. Running setup...</div>";
            require_once 'database_setup.php';
            echo "<div class='alert alert-success'>✅ Database setup completed successfully!</div>";
        }
    }
    
    echo "<div class='alert alert-info mt-4'>You can now <a href='index.php' class='alert-link'>go to the application</a>.</div>";
    
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>❌ Error: " . $e->getMessage() . "</div>";
    
    if (strpos($e->getMessage(), 'Connection refused') !== false) {
        echo "<div class='alert alert-warning mt-2'>Please make sure XAMPP's MySQL service is running.</div>";
    } elseif (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "<div class='alert alert-warning mt-2'>Check your MySQL username and password in config.php</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; }
        .alert { max-width: 800px; margin: 20px auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Database Connection Test</h1>
        <?php if (isset($tables) && count($tables) > 0): ?>
            <div class="card mt-4">
                <div class="card-header">
                    Database Tables
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <?php foreach ($tables as $table): ?>
                            <li class="list-group-item"><?php echo htmlspecialchars($table); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
