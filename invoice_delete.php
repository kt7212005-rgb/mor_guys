<?php
require 'config.php';

if (!isset($_GET['id'])) {
    header("Location: invoices.php?error=invalid_id");
    exit;
}

$id = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("DELETE FROM invoices WHERE id=?");
    $stmt->execute([$id]);
    header("Location: invoices.php?deleted=1");
    exit;
} catch (PDOException $e) {
    header("Location: invoices.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>
