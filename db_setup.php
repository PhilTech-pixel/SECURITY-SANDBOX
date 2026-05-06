<?php
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password

try {
    // Connect without a specific database first
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $sql = "CREATE DATABASE IF NOT EXISTS security_demo";
    $conn->exec($sql);
    
    // Switch to the newly created database
    $conn->exec("USE security_demo");
    
    // 1. Create the products table (Used for SQL Injection demo)
    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        category VARCHAR(100) NOT NULL,
        price DECIMAL(10, 2) NOT NULL
    )";
    $conn->exec($sql);

    // 2. Create the newsletter_members table (Used for Email validation demo)
    $sql = "CREATE TABLE IF NOT EXISTS newsletter_members (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(150) NOT NULL UNIQUE
    )";
    $conn->exec($sql);

    // Clear existing data to ensure a fresh state
    $conn->exec("TRUNCATE TABLE products");
    $conn->exec("TRUNCATE TABLE newsletter_members");

    // Insert sample data for products
    $sql = "INSERT INTO products (name, category, price) VALUES 
            ('Laptop Pro X', 'Electronics', 1299.99),
            ('Wireless Mouse', 'Accessories', 29.99),
            ('Mechanical Keyboard', 'Accessories', 89.50),
            ('Gaming Monitor 27\"', 'Electronics', 349.00),
            ('USB-C Hub', 'Accessories', 45.00),
            ('Ergonomic Chair', 'Furniture', 210.00),
            ('Standing Desk', 'Furniture', 450.00),
            ('Bluetooth Headphones', 'Audio', 150.00),
            ('Smartwatch Series 5', 'Wearables', 199.99),
            ('Portable SSD 1TB', 'Storage', 120.00)";
    $conn->exec($sql);
    
    $message = "Database 'security_demo' and tables ('products', 'newsletter_members') created and populated successfully!";
    $status = "success";

} catch(PDOException $e) {
    $message = "Database Setup Failed: " . $e->getMessage() . ". Ensure MySQL is running in XAMPP control panel.";
    $status = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - Security Sandbox</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #eef2f5; color: #1a2a3a; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background-color: white; padding: 2rem; border-radius: 0.5rem; border: 1px solid #b9c4d1; box-shadow: 2px 2px 6px rgba(0,0,0,0.05); text-align: center; max-width: 500px; }
        .success { color: #1c6d2f; background-color: #c8f0d1; padding: 10px; border-radius: 5px; border: 1px solid #2b7a3e; }
        .error { color: #b13e3e; background-color: #ffe0db; padding: 10px; border-radius: 5px; border: 1px solid #c25c5c; }
        .btn { display: inline-block; margin-top: 1.5rem; padding: 0.75rem 1.5rem; background-color: #1e6f9f; color: white; text-decoration: none; border-radius: 0.375rem; font-weight: bold; transition: background-color 0.2s; }
        .btn:hover { background-color: #144a66; }
    </style>
</head>
<body>
    <div class="card">
        <h2 style="color: #144a66;">Setup Status</h2>
        <p class="<?php echo $status; ?>"><strong><?php echo $message; ?></strong></p>
        <?php if ($status === 'success'): ?>
            <a href="index.php" class="btn">Go to Sandbox</a>
        <?php endif; ?>
    </div>
</body>
</html>
