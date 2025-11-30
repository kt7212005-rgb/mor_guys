<?php
require 'config.php';
$log = $pdo->prepare("
    INSERT INTO activity_log (activity, user, status) 
    VALUES (?, ?, ?)
");
$log->execute([
    "Student deleted: ID $id",
    "Admin",
    "Deleted"
]);


if(!isset($_GET['id'])) {
    header("Location: students.php");
    exit;
}

$id = $_GET['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id=?");
    $stmt->execute([$id]);
    header("Location: students.php?deleted=1");
    exit;
} catch (Exception $e) {
    echo "ERROR: ".$e->getMessage();
}
