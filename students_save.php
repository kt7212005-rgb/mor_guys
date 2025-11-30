<?php
require 'config.php';

// Collect POST data
$student_no       = $_POST['student_no'];
$first_name       = $_POST['first_name'];
$last_name        = $_POST['last_name'];
$grade_level      = $_POST['immunizations']; // textarea for grade_level
$contact          = $_POST['contact'];
$address          = $_POST['address'];
$allergies        = $_POST['allergies'];
$guardian_contact = $_POST['guardian_contact'];
$emergency_action = $_POST['emergency_action'];

try {
    $pdo->beginTransaction();

    // Handle photo upload
    $profile_image = null;
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $profile_image = $student_no . '_' . time() . '.' . $ext; // unique filename
        $upload_dir = 'uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_dir . $profile_image);
    }

    // Insert student info including profile_image
    $stmt = $pdo->prepare("INSERT INTO students (student_no, first_name, last_name, grade_level, contact, address, profile_image, enrollment_date) VALUES (?,?,?,?,?,?,?,NOW())");
    $stmt->execute([$student_no, $first_name, $last_name, $grade_level, $contact, $address, $profile_image]);

    $student_id = $pdo->lastInsertId();

    // Insert medical info
    $stmtMed = $pdo->prepare("INSERT INTO student_medical (student_id, allergies, guardian_contact, emergency_action) VALUES (?,?,?,?)");
    $stmtMed->execute([$student_id, $allergies, $guardian_contact, $emergency_action]);

    $pdo->commit();

    header("Location: students.php?success=1");
    exit;

} catch (PDOException $e) {
    $pdo->rollBack();
    die("Error saving student: " . $e->getMessage());
}
