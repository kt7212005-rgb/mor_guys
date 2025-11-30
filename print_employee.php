<?php
require 'config.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo "Employee ID missing.";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    $e = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$e) {
        echo "Employee not found.";
        exit;
    }
} catch (PDOException $e) {
    echo "Database error.";
    exit;
}

function formatCurrency($v){ return '₱' . number_format($v ?: 0, 2); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Print Employee Record - <?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial, sans-serif;background:#f5f5f5;padding:20px}
    .print-container{max-width:800px;margin:0 auto;background:#fff;padding:30px;border-radius:4px;box-shadow:0 2px 10px rgba(0,0,0,0.08)}
    .header{text-align:center;border-bottom:3px solid #1e3a2a;padding-bottom:15px;margin-bottom:25px}
    .header h1{color:#1e3a2a;margin-bottom:5px;font-size:28px}
    .header .subtitle{color:#666;font-size:14px}
    .section{margin-bottom:20px}
    .section-title{background:#1e3a2a;color:#fff;padding:8px 15px;margin-bottom:12px;font-weight:600;border-radius:4px}
    .row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #eee}
    .row:last-child{border-bottom:none}
    .label{font-weight:600;color:#444;width:40%}
    .value{text-align:right;color:#333;width:55%}
    .info-box{background:#f8f9fa;padding:15px;border-radius:6px;margin-top:15px;border-left:4px solid #1e3a2a}
    @media print{ 
      body{padding:0;background:#fff} 
      .print-actions{display:none} 
      .print-container{box-shadow:none;padding:20px}
      .no-print{display:none}
    }
    .print-actions{text-align:center;margin-bottom:15px}
    .btn{display:inline-block;padding:10px 20px;border-radius:6px;text-decoration:none;color:#fff;background:#1e3a2a;margin:0 5px;cursor:pointer;border:none;font-size:14px}
    .btn:hover{background:#2d5a3d}
    .footer{text-align:center;color:#666;font-size:12px;margin-top:30px;padding-top:20px;border-top:1px solid #eee}
    .photo-section{text-align:center;margin-bottom:20px}
    .photo-section img{width:120px;height:120px;object-fit:cover;border-radius:50%;border:3px solid #1e3a2a}
    @media print{ .photo-section img{width:100px;height:100px} }
  </style>
</head>
<body>
  <div class="print-container">
    <div class="print-actions">
      <button onclick="window.print();return false;" class="btn">Print Employee Record</button>
      <a href="employee_view.php?id=<?= $e['id'] ?>" class="btn" style="background:#6c757d">Back to View</a>
    </div>

    <div class="header">
      <h1>Employee Record</h1>
      <div class="subtitle">School Management System</div>
    </div>

    <?php 
    $photo_path = 'uploads/' . ($e['profile_image'] ?? 'default.png');
    $photo_exists = !empty($e['profile_image']) && file_exists($photo_path);
    ?>
    <?php if($photo_exists): ?>
    <div class="photo-section">
      <img src="<?= $photo_path ?>" alt="Employee Photo">
    </div>
    <?php endif; ?>

    <div class="section">
      <div class="section-title">Personal Information</div>
      <div class="row">
        <div class="label">Employee Number:</div>
        <div class="value"><?= htmlspecialchars($e['employee_no']) ?></div>
      </div>
      <div class="row">
        <div class="label">Full Name:</div>
        <div class="value"><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></div>
      </div>
      <div class="row">
        <div class="label">Email Address:</div>
        <div class="value"><?= htmlspecialchars($e['email'] ?: 'N/A') ?></div>
      </div>
      <div class="row">
        <div class="label">Phone Number:</div>
        <div class="value"><?= htmlspecialchars($e['phone'] ?: 'N/A') ?></div>
      </div>
    </div>

    <div class="section">
      <div class="section-title">Employment Information</div>
      <div class="row">
        <div class="label">Position:</div>
        <div class="value"><?= htmlspecialchars($e['position'] ?: 'N/A') ?></div>
      </div>
      <div class="row">
        <div class="label">Department:</div>
        <div class="value"><?= htmlspecialchars($e['department'] ?: 'N/A') ?></div>
      </div>
      <div class="row">
        <div class="label">Hire Date:</div>
        <div class="value"><?= htmlspecialchars($e['hire_date'] ?: 'N/A') ?></div>
      </div>
      <div class="row">
        <div class="label">Monthly Salary:</div>
        <div class="value"><?= formatCurrency($e['salary']) ?></div>
      </div>
      <div class="row">
        <div class="label">Status:</div>
        <div class="value"><?= htmlspecialchars(ucfirst($e['status'] ?: 'Active')) ?></div>
      </div>
    </div>

    <div class="footer">
      <div>Generated on <?= date('F d, Y \a\t h:i A') ?></div>
      <div style="margin-top:5px;">This is a computer generated document.</div>
    </div>
  </div>
</body>
</html>

