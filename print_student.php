<?php
require 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo "Student ID missing.";
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT s.*, sm.allergies, sm.immunizations, sm.blood_type
        FROM students s
        LEFT JOIN student_medical sm ON s.id = sm.student_id
        WHERE s.id = ? LIMIT 1
    ");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$student) {
        echo "Student not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Database error.";
    exit;
}

// Determine photo path
$photo_path = 'uploads/' . ($student['profile_image'] ?? 'default.png');
$photo_exists = file_exists($photo_path);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Print Student Record - <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial, sans-serif;background:#f5f5f5;padding:20px}
    .print-container{max-width:800px;margin:0 auto;background:#fff;padding:30px;border-radius:4px;box-shadow:0 2px 10px rgba(0,0,0,0.08)}
    .header{text-align:center;border-bottom:3px solid #0a5517;padding-bottom:15px;margin-bottom:25px}
    .header h1{color:#0a5517;margin-bottom:5px;font-size:28px}
    .header .subtitle{color:#666;font-size:14px}
    .photo-section{text-align:center;margin-bottom:20px}
    .photo-section img{width:120px;height:120px;object-fit:cover;border-radius:50%;border:3px solid #0a5517}
    .section{margin-bottom:20px}
    .section-title{background:#0a5517;color:#fff;padding:8px 15px;margin-bottom:12px;font-weight:600;border-radius:4px}
    .row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eee}
    .row:last-child{border-bottom:none}
    .label{font-weight:600;color:#444;width:40%}
    .value{text-align:right;color:#333;width:55%}
    .info-box{background:#f8f9fa;padding:15px;border-radius:6px;margin-top:15px;border-left:4px solid #0a5517}
    .medical-info{background:#fff3cd;padding:12px;border-radius:6px;margin-top:10px;border-left:4px solid #ffc107}
    @media print{ 
      body{padding:0;background:#fff} 
      .print-actions{display:none} 
      .print-container{box-shadow:none;padding:20px}
      .no-print{display:none}
      .photo-section img{width:100px;height:100px}
    }
    .print-actions{text-align:center;margin-bottom:15px}
    .btn{display:inline-block;padding:10px 20px;border-radius:6px;text-decoration:none;color:#fff;background:#0a5517;margin:0 5px;cursor:pointer;border:none;font-size:14px}
    .btn:hover{background:#0d6e1f}
    .footer{text-align:center;color:#666;font-size:12px;margin-top:30px;padding-top:20px;border-top:1px solid #eee}
  </style>
</head>
<body>
  <div class="print-container">
    <div class="print-actions">
      <button onclick="window.print();return false;" class="btn">Print Student Record</button>
      <a href="students_view.php?id=<?= $student['id'] ?>" class="btn" style="background:#6c757d">Back to View</a>
    </div>

    <div class="header">
      <h1>Student Record</h1>
      <div class="subtitle">School Management System</div>
    </div>

    <?php if($photo_exists): ?>
    <div class="photo-section">
      <img src="<?= $photo_path ?>" alt="Student Photo">
    </div>
    <?php endif; ?>

    <div class="section">
      <div class="section-title">Personal Information</div>
      <div class="row">
        <div class="label">Student Number:</div>
        <div class="value"><?= htmlspecialchars($student['student_no']) ?></div>
      </div>
      <div class="row">
        <div class="label">Full Name:</div>
        <div class="value"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></div>
      </div>
      <div class="row">
        <div class="label">Contact Number:</div>
        <div class="value"><?= htmlspecialchars($student['contact'] ?: 'N/A') ?></div>
      </div>
      <div class="row">
        <div class="label">Address:</div>
        <div class="value"><?= htmlspecialchars($student['address'] ?: 'N/A') ?></div>
      </div>
      <div class="row">
        <div class="label">Grade Level:</div>
        <div class="value"><?= htmlspecialchars($student['grade_level'] ?: 'N/A') ?></div>
      </div>
      <div class="row">
        <div class="label">Enrollment Date:</div>
        <div class="value"><?= htmlspecialchars($student['enrollment_date'] ?: 'N/A') ?></div>
      </div>
      <div class="row">
        <div class="label">Status:</div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">Medical Information</div>
      <div class="medical-info">
        <div class="row">
          <div class="label">Allergies:</div>
          <div class="value"><?= htmlspecialchars($student['allergies'] ?: 'None') ?></div>
        </div>
        <div class="row">
          <div class="label">Immunizations:</div>
          <div class="value"><?= htmlspecialchars($student['immunizations'] ?: 'None') ?></div>
        </div>
        <div class="row">
          <div class="label">Blood Type:</div>
          <div class="value"><?= htmlspecialchars($student['blood_type'] ?: 'N/A') ?></div>
        </div>
      </div>
    </div>

    <div class="footer">
      <div>Generated on <?= date('F d, Y \a\t h:i A') ?></div>
      <div style="margin-top:5px;">This is a computer generated document.</div>
    </div>
  </div>
</body>
</html>

