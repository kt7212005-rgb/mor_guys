<?php
require 'config.php';

// Handle form submission first
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle photo upload
    $profile_image = $_POST['existing_profile_image'] ?? null;
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $employee_no = $_POST['employee_no'] ?? 'EMP';
        $profile_image = $employee_no . '_' . time() . '.' . $ext;
        $upload_dir = 'uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_dir . $profile_image);
    }

    $stmt = $pdo->prepare("UPDATE employees SET first_name=?, last_name=?, email=?, phone=?, position=?, department=?, hire_date=?, salary=?, status=?, profile_image=? WHERE id=?");
    $stmt->execute([
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['position'],
        $_POST['department'],
        $_POST['hire_date'],
        $_POST['salary'],
        $_POST['status'],
        $profile_image,
        $_POST['id']
    ]);

    header("Location: employees.php");
    exit;
}

// Fetch employee data for form
if (!isset($_GET['id'])) {
    die("Employee ID not provided.");
}
$id = $_GET['id'];
$empStmt = $pdo->prepare("SELECT * FROM employees WHERE id=?");
$empStmt->execute([$id]);
$emp = $empStmt->fetch();

if (!$emp) {
    die("Employee not found.");
}

require 'header.php';
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <h2>Edit Employee</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $emp['id'] ?>">
    <input type="hidden" name="employee_no" value="<?= htmlspecialchars($emp['employee_no']) ?>">
    <input type="hidden" name="existing_profile_image" value="<?= htmlspecialchars($emp['profile_image'] ?? '') ?>">
    
    <div class="mb-3">
      <label>Employee No.</label>
      <input class="form-control" value="<?= htmlspecialchars($emp['employee_no']) ?>" disabled>
    </div>
    
    <div class="mb-3">
      <label>Employee Photo</label>
      <?php if(!empty($emp['profile_image']) && file_exists('uploads/' . $emp['profile_image'])): ?>
        <div style="margin-bottom:10px;">
          <img src="uploads/<?= htmlspecialchars($emp['profile_image']) ?>" 
               alt="Current Photo" 
               style="width:80px; height:80px; object-fit:cover; border-radius:50%; border:2px solid #1e3a2a;">
        </div>
      <?php endif; ?>
      <input type="file" name="profile_image" accept="image/*" class="form-control">
      <small class="text-muted">Leave empty to keep current photo</small>
    </div>
    
    <div class="mb-3">
      <label>First Name</label>
      <input name="first_name" class="form-control" value="<?= htmlspecialchars($emp['first_name']) ?>" required>
    </div>
    
    <div class="mb-3">
      <label>Last Name</label>
      <input name="last_name" class="form-control" value="<?= htmlspecialchars($emp['last_name']) ?>" required>
    </div>
    
    <div class="mb-3">
      <label>Email</label>
      <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($emp['email']) ?>">
    </div>
    
    <div class="mb-3">
      <label>Phone</label>
      <input name="phone" class="form-control" value="<?= htmlspecialchars($emp['phone']) ?>">
    </div>
    
    <div class="mb-3">
      <label>Position</label>
      <input name="position" class="form-control" value="<?= htmlspecialchars($emp['position']) ?>">
    </div>
    
    <div class="mb-3">
      <label>Department</label>
      <input name="department" class="form-control" value="<?= htmlspecialchars($emp['department']) ?>">
    </div>
    
    <div class="mb-3">
      <label>Hire Date</label>
      <input type="date" name="hire_date" class="form-control" value="<?= htmlspecialchars($emp['hire_date']) ?>">
    </div>
    
    <div class="mb-3">
      <label>Salary</label>
      <input type="number" step="0.01" name="salary" class="form-control" value="<?= htmlspecialchars($emp['salary']) ?>">
    </div>
    
    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="active" <?= $emp['status']=='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= $emp['status']=='inactive'?'selected':'' ?>>Inactive</option>
      </select>
    </div>
    
    <button class="btn btn-primary">Save</button>
    <a href="employees.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require 'footer.php'; ?>
