<?php
require 'config.php';
require 'header.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    header('Location: payroll.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM payroll WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p) {
        header('Location: payroll.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: payroll.php');
    exit;
}

function formatCurrency($v){ return '₱' . number_format($v ?: 0, 2); }
?>
<link rel="stylesheet" href="style.css">

<div class="container-fluid p-4" style="margin-left:280px; max-width: calc(100% - 280px);">
  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between">
        <h5 class="card-title">Payroll Details</h5>
        <div class="btn-group">
          <a href="print_payroll.php?id=<?= $p['id'] ?>" target="_blank" class="btn btn-secondary"><i class="bi bi-printer"></i> Print</a>
          <a href="payroll_edit.php?id=<?= $p['id'] ?>" class="btn btn-warning"><i class="bi bi-pencil"></i> Edit</a>
          <a href="payroll_delete.php?id=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this payroll record?');"><i class="bi bi-trash"></i> Delete</a>
        </div>
      </div>

      <div class="mt-3">
        <div><strong>Employee:</strong> <?= htmlspecialchars($p['employee_name']) ?> (<?= htmlspecialchars($p['employee_id']) ?>)</div>
        <div><strong>Position:</strong> <?= htmlspecialchars($p['position']) ?></div>
        <div><strong>Email:</strong> <?= htmlspecialchars($p['email']) ?></div>
        <hr>
        <div><strong>Monthly Salary:</strong> <?= formatCurrency($p['monthly_salary']) ?></div>
        <div><strong>Bi-Weekly Rate:</strong> <?= formatCurrency($p['bi_weekly_rate']) ?></div>
        <div><strong>Allowances:</strong> <?= formatCurrency($p['allowances']) ?></div>
        <div><strong>SSS:</strong> <?= formatCurrency($p['sss']) ?> | <strong>Pag-IBIG:</strong> <?= formatCurrency($p['pagibig']) ?> | <strong>PhilHealth:</strong> <?= formatCurrency($p['philhealth']) ?></div>
        <div><strong>Other Deductions:</strong> <?= formatCurrency($p['other_deductions']) ?></div>
        <hr>
        <div><strong>Net Pay:</strong> <span class="fw-bold"><?= formatCurrency($p['net_pay']) ?></span></div>
        <div><strong>Pay Date:</strong> <?= htmlspecialchars($p['pay_date']) ?></div>
      </div>

      <div class="mt-3">
        <a href="payroll.php" class="btn btn-secondary">
          <i class="bi bi-arrow-left me-2"></i>Back to Payroll List
        </a>
      </div>

    </div>
  </div>
</div>

<?php require 'footer.php'; ?>
