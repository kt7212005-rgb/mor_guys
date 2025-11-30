<?php
require 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = trim($_POST['item_name'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 0);
    $condition = $_POST['condition'] ?? 'Good';
    $room = trim($_POST['room'] ?? '');
    $technician = trim($_POST['technician'] ?? '');

    // Validation
    if (empty($item_name)) $errors[] = "Item name is required.";
    if ($quantity < 0) $errors[] = "Quantity cannot be negative.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("INSERT INTO inventory (item_name, quantity, `condition`, room, technician) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $item_name,
                $quantity,
                $condition,
                $room,
                $technician
            ]);

            $pdo->commit();
            header("Location: inventory.php?success=1");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // If errors, redirect back with error message
    if (!empty($errors)) {
        header("Location: inventory.php?error=" . urlencode(implode(', ', $errors)));
        exit;
    }
}
?>
