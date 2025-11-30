<?php
require 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: payroll.php?error=invalid_id');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM payroll WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: payroll.php?deleted=1');
    exit;
} catch (PDOException $e) {
    header('Location: payroll.php?error=' . urlencode($e->getMessage()));
    exit;
}
