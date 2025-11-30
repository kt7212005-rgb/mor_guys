<?php
require 'config.php';

if (!isset($_GET['id'])) exit("Missing employee ID.");

$stmt = $pdo->prepare("DELETE FROM employees WHERE id=?");
$stmt->execute([$_GET['id']]);

header("Location: employees.php");
exit;
?>
