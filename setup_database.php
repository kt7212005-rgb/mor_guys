<?php
// Database setup script
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    // Connect to MySQL server without database
    $dsn = "mysql:host=$host;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS mor_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database 'mor_system' created/verified successfully!\n";
    
    // Connect to the database
    $pdo->exec("USE mor_system");
    
    // Create basic tables if they don't exist
    $tables = [
        "CREATE TABLE IF NOT EXISTS students (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE,
            phone VARCHAR(20),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS employees (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE,
            position VARCHAR(100),
            salary DECIMAL(10,2),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS inventory (
            id INT AUTO_INCREMENT PRIMARY KEY,
            item_name VARCHAR(255) NOT NULL,
            quantity INT DEFAULT 0,
            price DECIMAL(10,2),
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )",
        
        "CREATE TABLE IF NOT EXISTS invoices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            invoice_number VARCHAR(50) UNIQUE,
            customer_name VARCHAR(255),
            amount DECIMAL(10,2),
            status VARCHAR(50) DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    
    echo "Basic tables created successfully!\n";
    
    // Show created tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables in database:\n";
    foreach ($tables_list as $table) {
        echo "- $table\n";
    }
    
} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please ensure:\n";
    echo "1. XAMPP MySQL service is running\n";
    echo "2. MySQL username is 'root' with no password\n";
    echo "3. MySQL server is on localhost (127.0.0.1)\n";
}
?>
