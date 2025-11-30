<?php
require 'config.php';

// Handle POST first, before any output
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Auto-generate unique employee_no (like EMP001, EMP002, etc.)
    $lastEmpNo = $pdo->query("SELECT employee_no FROM employees ORDER BY id DESC LIMIT 1")->fetchColumn();
    if ($lastEmpNo) {
        // extract number part from last employee_no
        $num = (int) filter_var($lastEmpNo, FILTER_SANITIZE_NUMBER_INT);
        $newEmpNo = 'EMP' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    } else {
        $newEmpNo = 'EMP001';
    }

    // Handle photo upload
    $profile_image = null;
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $profile_image = $newEmpNo . '_' . time() . '.' . $ext; // unique filename
        $upload_dir = 'uploads/';
        if(!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_dir . $profile_image);
    }

    $stmt = $pdo->prepare("INSERT INTO employees (employee_no, first_name, last_name, email, phone, position, department, hire_date, salary, status, profile_image) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $newEmpNo,
        $_POST['first_name'],
        $_POST['last_name'],
        $_POST['email'],
        $_POST['phone'],
        $_POST['position'],
        $_POST['department'],
        $_POST['hire_date'],
        $_POST['salary'],
        $_POST['status'],
        $profile_image
    ]);

    // Redirect after insert
    header("Location: employees.php");
    exit;
}

require 'header.php';
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <h2>Add Employee</h2>
  <form method="post" enctype="multipart/form-data">
    <div class="mb-3">
      <label>Employee No.</label>
      <input name="employee_no" class="form-control" value="(Will be auto-generated)" disabled>
    </div>
    <div class="mb-3">
      <label>Employee Photo</label>
      <input type="file" name="profile_image" accept="image/*" class="form-control">
    </div>
    <div class="mb-3"><label>First Name</label><input name="first_name" class="form-control" required></div>
    <div class="mb-3"><label>Last Name</label><input name="last_name" class="form-control" required></div>
    <div class="mb-3"><label>Email</label><input name="email" type="email" class="form-control"></div>
    <div class="mb-3"><label>Phone</label><input name="phone" class="form-control"></div>
    <div class="mb-3"><label>Position</label><input name="position" class="form-control"></div>
    <div class="mb-3"><label>Department</label><input name="department" class="form-control"></div>
    <div class="mb-3"><label>Hire Date</label><input type="date" name="hire_date" class="form-control"></div>
    <div class="mb-3"><label>Salary</label><input type="number" step="0.01" name="salary" class="form-control"></div>
    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="active" selected>Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
    <button class="btn btn-success">Add Employee</button>
    <a href="employees.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>

<?php require 'footer.php'; ?>
