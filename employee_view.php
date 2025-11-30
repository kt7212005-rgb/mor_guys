<?php
require 'config.php';
require 'header.php';

if (!isset($_GET['id'])) exit("No employee selected.");

$stmt = $pdo->prepare("SELECT * FROM employees WHERE id=?");
$stmt->execute([$_GET['id']]);
$e = $stmt->fetch();

if (!$e) exit("Employee not found.");
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <h2>View Employee</h2>
  <div class="card shadow-sm p-3">
    <?php 
    // Determine photo path
    $photo_path = 'uploads/' . ($e['profile_image'] ?? 'default.png');
    $photo_exists = !empty($e['profile_image']) && file_exists($photo_path);
    ?>
    <?php if($photo_exists): ?>
    <div style="text-align:center; margin-bottom:20px;">
      <img src="<?= $photo_path ?>" 
           alt="Employee Photo" 
           style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:3px solid #1e3a2a;">
    </div>
    <?php endif; ?>
    <p><strong>Employee No.: </strong><?= htmlspecialchars($e['employee_no']) ?></p>
    <p><strong>Name: </strong><?= htmlspecialchars($e['first_name'].' '.$e['last_name']) ?></p>
    <p><strong>Email: </strong><?= htmlspecialchars($e['email']) ?></p>
    <p><strong>Phone: </strong><?= htmlspecialchars($e['phone']) ?></p>
    <p><strong>Position: </strong><?= htmlspecialchars($e['position']) ?></p>
    <p><strong>Department: </strong><?= htmlspecialchars($e['department']) ?></p>
    <p><strong>Hire Date: </strong><?= htmlspecialchars($e['hire_date']) ?></p>
    <p><strong>Salary: </strong>₱<?= number_format($e['salary'],2) ?></p>
    <p><strong>Status: </strong><?= htmlspecialchars(ucfirst($e['status'])) ?></p>
  </div>
  <div class="mt-3">
    <a href="print_employee.php?id=<?= $e['id'] ?>" target="_blank" class="btn btn-primary">
      <i class="bi bi-printer me-2"></i>Print
    </a>
    <a href="employees.php" class="btn btn-secondary">Back</a>
  </div>
</div>

<?php require 'footer.php'; ?>
