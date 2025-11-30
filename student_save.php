<?php
require 'config.php';  
// After successful student save
$activity = "New student registered: $first_name $last_name";
$user = "Admin"; // Replace with the logged-in user if you have authentication
$status = "Completed";

$stmtLog = $pdo->prepare("INSERT INTO activity_log (activity, user, status) VALUES (?, ?, ?)");
$stmtLog->execute([$activity, $user, $status]);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Basic student info
    $student_no = $_POST['student_no'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];

    // Medical info
    $allergies = $_POST['allergies'];
    $guardian_contact = $_POST['guardian_contact'];
    $emergency_action = $_POST['emergency_action'];

    // Handle image upload
    $profile_image = null;

    if (!empty($_FILES['profile_image']['name'])) {

        $targetDir = "uploads/";
        $fileName = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $targetFile = $targetDir . $fileName;

        // Only image allowed
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExt, $allowed)) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $targetFile)) {
                $profile_image = $fileName;
            }
        }
    }

    // Insert into STUDENTS TABLE
    $stmt = $pdo->prepare("
        INSERT INTO students 
            (student_no, first_name, last_name, contact, address, profile_image)
        VALUES 
            (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $student_no,
        $first_name,
        $last_name,
        $contact,
        $address,
        $profile_image
    ]);

    // Get last inserted student ID
    $student_id = $pdo->lastInsertId();

    // Insert into STUDENT_MEDICAL table
    $stmt2 = $pdo->prepare("
        INSERT INTO student_medical (student_id, allergies, guardian_contact, emergency_action)
        VALUES (?, ?, ?, ?)
    ");
    $stmt2->execute([
        $student_id,
        $allergies,
        $guardian_contact,
        $emergency_action
    ]);

    header("Location: students.php?success=1");
    exit;
}
?>
