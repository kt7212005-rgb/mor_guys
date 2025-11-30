<?php
require 'config.php';
require 'header.php';

// fetch payroll records (latest first)
try {
    $stmt = $pdo->query("SELECT * FROM payroll ORDER BY pay_date DESC, created_at DESC");
    $payrolls = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $payrolls = [];
}
?>
<link rel="stylesheet" href="style.css">

<div class="container-fluid p-4" style="margin-left: 280px; max-width: calc(100% - 280px);">

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> Payroll record has been saved successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> Payroll record has been deleted successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> <?= htmlspecialchars($_GET['error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1">Payroll Management</h2>
      <p class="text-muted mb-0">
        <i class="bi bi-calendar3 me-2"></i>
        Today is <?php echo date('l, F j, Y'); ?> |
        <i class="bi bi-clock me-2"></i>
        <?php echo date('g:i A'); ?>
      </p>
    </div>

    <div>
      <a href="payroll_add.php" class="btn btn-success">
        <i class="bi bi-plus-circle me-2"></i> Add Payroll
      </a>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="bi bi-people-fill text-success" style="font-size: 2.5rem;"></i>
          <h5 class="card-title mt-2">Payroll Records</h5>
          <?php
          try {
              $count = $pdo->query("SELECT COUNT(*) FROM payroll")->fetchColumn();
              echo "<h3 class='text-success fw-bold'>" . ($count ?: 0) . "</h3>";
          } catch (PDOException $e) {
              echo "<h3 class='text-muted'>0</h3>";
          }
          ?>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="bi bi-cash-coin text-primary" style="font-size: 2.5rem;"></i>
          <h5 class="card-title mt-2">Total Net Paid</h5>
          <?php
          try {
              $sum = $pdo->query("SELECT SUM(net_pay) FROM payroll")->fetchColumn();
              echo "<h3 class='text-primary fw-bold'>₱" . number_format($sum ?: 0, 2) . "</h3>";
          } catch (PDOException $e) {
              echo "<h3 class='text-muted'>₱0.00</h3>";
          }
          ?>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="bi bi-hourglass-split text-warning" style="font-size: 2.5rem;"></i>
          <h5 class="card-title mt-2">Pending</h5>
          <?php
          try {
              $pending = $pdo->query("SELECT COUNT(*) FROM payroll WHERE COALESCE(pay_date, '0000-00-00') = '0000-00-00' OR pay_date > CURDATE() OR net_pay <= 0")->fetchColumn();
              // This is a heuristic; you might want to store explicit status column later.
              echo "<h3 class='text-warning fw-bold'>" . ($pending ?: 0) . "</h3>";
          } catch (PDOException $e) {
              echo "<h3 class='text-muted'>0</h3>";
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card">
    <div class="card-body">
      <h5 class="card-title mb-3">Payroll Records</h5>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Emp ID</th>
              <th>Name</th>
              <th>Position</th>
              <th>Bi-Weekly</th>
              <th>Allowances</th>
              <th>Deductions</th>
              <th>Net Pay</th>
              <th>Pay Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($payrolls) > 0): ?>
              <?php foreach ($payrolls as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row['employee_id']) ?></td>
                  <td><?= htmlspecialchars($row['employee_name']) ?></td>
                  <td><?= htmlspecialchars($row['position']) ?></td>
                  <td>₱<?= number_format($row['bi_weekly_rate'] ?: 0, 2) ?></td>
                  <td>₱<?= number_format($row['allowances'] ?: 0, 2) ?></td>
                  <td>
                    ₱<?= number_format(
                        ($row['sss'] + $row['pagibig'] + $row['philhealth'] + $row['other_deductions']) ?: 0
                      , 2) ?>
                  </td>
                  <td><strong>₱<?= number_format($row['net_pay'] ?: 0, 2) ?></strong></td>
                  <td><?= htmlspecialchars($row['pay_date']) ?></td>
                  <td>
                    <a href="payroll_view.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary" title="View">
                      <i class="bi bi-eye"></i>
                    </a>
                    <a href="payroll_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="payroll_delete.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this payroll record?');" title="Delete">
                      <i class="bi bi-trash"></i>
                    </a>
                    <a href="print_payroll.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary" target="_blank" title="Print">
                      <i class="bi bi-printer"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" class="text-center text-muted">No payroll records found</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

</div>

<?php require 'footer.php'; ?>