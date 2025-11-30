<?php
require 'config.php';
require 'header.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div style='margin-left:280px; padding:20px;'><p style='color:red;'>No student ID provided.</p></div>";
    require 'footer.php';
    exit;
}

$student_id = $_GET['id'];

try {
    $stmt = $pdo->prepare("
        SELECT s.*, sm.allergies, sm.immunizations, sm.blood_type
        FROM students s
        LEFT JOIN student_medical sm ON s.id = sm.student_id
        WHERE s.id = ? LIMIT 1
    ");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo "<div style='margin-left:280px; padding:20px;'><p style='color:red;'>Student not found.</p></div>";
        require 'footer.php';
        exit;
    }

} catch (PDOException $e) {
    echo "<div style='margin-left:280px; padding:20px;'><p style='color:red;'>Error fetching student: " . $e->getMessage() . "</p></div>";
    require 'footer.php';
    exit;
}

// Determine photo path
$photo_path = 'uploads/' . ($student['profile_image'] ?? 'default.png');
?>

<div style="margin-left:280px; max-width: calc(100% - 280px); padding: 25px; font-family: Arial, sans-serif;">
    <h2 style="margin-bottom: 20px; color:#333;">Student Details</h2>

    <div style="
        background: #fff;
        border-radius: 10px;
        box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 30px;
    ">
        <div style="
            background: #0a5517ff;
            padding: 15px 20px;
            color: white;
            display: flex;
            align-items: center;
            gap: 20px;
        ">
            <img src="<?= $photo_path ?>" 
                 alt="Student Photo" 
                 style="width:80px; height:80px; object-fit:cover; border-radius:50%; border:2px solid #fff;">
            <span style="font-size:18px; font-weight:bold;">
                <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
            </span>
        </div>

        <div style="padding: 20px; font-size:15px; color:#444;">
            <p><strong>Student No:</strong> <?= htmlspecialchars($student['student_no']) ?></p>
            <p><strong>Contact:</strong> <?= htmlspecialchars($student['contact'] ?? 'N/A') ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($student['address'] ?? 'N/A') ?></p>
            <p><strong>Grade Level:</strong> <?= htmlspecialchars($student['grade_level'] ?? 'N/A') ?></p>
            <p><strong>Enrollment Date:</strong> <?= htmlspecialchars($student['enrollment_date'] ?? 'N/A') ?></p>

            <h4 style="margin-top: 30px; color:#0a5517ff;">Medical Information</h4>
            <p>
                <strong>Allergies:</strong> <?= htmlspecialchars($student['allergies'] ?? 'None') ?><br>
                <strong>Immunizations:</strong> <?= htmlspecialchars($student['immunizations'] ?? 'None') ?><br>
                <strong>Blood Type:</strong> <?= htmlspecialchars($student['blood_type'] ?? 'N/A') ?>
            </p>

            <div style="margin-top: 25px;">
                <a href="print_student.php?id=<?= $student['id'] ?>" target="_blank"
                   style="display:inline-block; background:#0a5517; padding:10px 18px; border-radius:6px; text-decoration:none; color:white; margin-right:10px;">
                    <i class="bi bi-printer me-2"></i>Print
                </a>
                <a href="students.php" 
                   style="display:inline-block; background:#6c757d; padding:10px 18px; border-radius:6px; text-decoration:none; color:white; margin-right:10px;">
                    Back to List
                </a>

                <a href="students_delete.php?id=<?= $student['id'] ?>" 
                   onclick="return confirm('Are you sure you want to delete this student?');"
                   style="display:inline-block; background:#dc3545; padding:10px 18px; border-radius:6px; text-decoration:none; color:white;">
                    Delete
                </a>
            </div>
        </div>
    </div>
</div>

<?php require 'footer.php'; ?>
