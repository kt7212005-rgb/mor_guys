<?php
require 'config.php';
require 'header.php';

$emps = $pdo->query("SELECT * FROM employees ORDER BY id DESC")->fetchAll();
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Employee Management</h2>
    <a href="employee_add.php" class="btn btn-success">
      <i class="bi bi-plus-circle me-2"></i> Add Employee
    </a>
  </div>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>#</th>
        <th>Employee No.</th>
        <th>Name</th>
        <th>Position</th>
        <th>Department</th>
        <th>Email</th>
        <th>Contact</th>
        <th>Date Hired</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($emps as $e): ?>
        <tr>
          <td><?= $e['id'] ?></td>
          <td><?= htmlspecialchars($e['employee_no']) ?></td>
          <td><?= htmlspecialchars($e['first_name'].' '.$e['last_name']) ?></td>
          <td><?= htmlspecialchars($e['position']) ?></td>
          <td><?= htmlspecialchars($e['department']) ?></td>
          <td><?= htmlspecialchars($e['email']) ?></td>
          <td><?= htmlspecialchars($e['phone']) ?></td>
          <td><?= htmlspecialchars($e['hire_date']) ?></td>
          <td>
            <div class="btn-group btn-group-sm">
              <a href="employee_view.php?id=<?= $e['id'] ?>" class="btn btn-outline-success" title="View">
                <i class="bi bi-eye"></i>
              </a>
              <a href="employee_edit.php?id=<?= $e['id'] ?>" class="btn btn-outline-primary" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <a href="employee_delete.php?id=<?= $e['id'] ?>" 
                 class="btn btn-outline-danger"
                 onclick="return confirm('Delete this employee?');"
                 title="Delete">
                <i class="bi bi-trash"></i>
              </a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require 'footer.php'; ?>
