<?php
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: inventory.php?error=invalid_id");
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("DELETE FROM inventory WHERE id=?");
    $stmt->execute([$id]);
    header("Location: inventory.php?deleted=1");
    exit;
} catch (PDOException $e) {
    header("Location: inventory.php?error=" . urlencode($e->getMessage()));
    exit;
}
