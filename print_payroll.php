<?php
require 'config.php';
// no header (we want a clean print), but if your header contains auth check, include it:
// require 'header.php';

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    echo "Payroll ID missing.";
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM payroll WHERE id = ?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$p) {
        echo "Payroll not found.";
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
  <title>Print Payslip - <?= htmlspecialchars($p['employee_name']) ?></title>
  <style>
    *{box-sizing:border-box}
    body{font-family:Arial, sans-serif;background:#f5f5f5;padding:20px}
    .print-container{max-width:800px;margin:0 auto;background:#fff;padding:30px;border-radius:4px;box-shadow:0 2px 10px rgba(0,0,0,0.08)}
    .header{text-align:center;border-bottom:3px solid #1fb9aa;padding-bottom:10px;margin-bottom:20px}
    .header h1{color:#159488;margin-bottom:5px}
    .section{margin-bottom:15px}
    .row{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #eee}
    .label{font-weight:600;color:#444}
    .value{text-align:right}
    .total{background:#1fb9aa;color:#fff;padding:10px;border-radius:6px;margin-top:10px;font-size:18px}
    @media print{ body{padding:0;background:#fff} .print-actions{display:none} .print-container{box-shadow:none} }
    .print-actions{text-align:center;margin-bottom:15px}
    .btn{display:inline-block;padding:10px 20px;border-radius:6px;text-decoration:none;color:#fff;background:#1fb9aa;margin:0 5px;cursor:pointer;border:none;font-size:14px}
    .btn:hover{background:#159488}
    .footer{text-align:center;color:#666;font-size:12px;margin-top:30px;padding-top:20px;border-top:1px solid #eee}
  </style>
</head>
<body>
  <div class="print-container">
    <div class="print-actions">
      <button onclick="window.print();return false;" class="btn">Print Payslip</button>
      <a href="payroll_view.php?id=<?= $p['id'] ?>" class="btn" style="background:#6c757d">Back to View</a>
    </div>

    <div class="header">
      <h1>Payslip</h1>
      <div>School Management System</div>
    </div>

    <div class="section">
      <div class="row"><div class="label">Employee</div><div class="value"><?= htmlspecialchars($p['employee_name']) ?> (<?= htmlspecialchars($p['employee_id']) ?>)</div></div>
      <div class="row"><div class="label">Position</div><div class="value"><?= htmlspecialchars($p['position']) ?></div></div>
      <div class="row"><div class="label">Email</div><div class="value"><?= htmlspecialchars($p['email']) ?></div></div>
      <div class="row"><div class="label">Pay Date</div><div class="value"><?= htmlspecialchars($p['pay_date']) ?></div></div>
    </div>

    <div class="section">
      <div class="row"><div class="label">Monthly Salary</div><div class="value"><?= formatCurrency($p['monthly_salary']) ?></div></div>
      <div class="row"><div class="label">Bi-Weekly Rate</div><div class="value"><?= formatCurrency($p['bi_weekly_rate']) ?></div></div>
      <div class="row"><div class="label">Allowances</div><div class="value"><?= formatCurrency($p['allowances']) ?></div></div>
      <div class="row"><div class="label">SSS</div><div class="value"><?= formatCurrency($p['sss']) ?></div></div>
      <div class="row"><div class="label">Pag-IBIG</div><div class="value"><?= formatCurrency($p['pagibig']) ?></div></div>
      <div class="row"><div class="label">PhilHealth</div><div class="value"><?= formatCurrency($p['philhealth']) ?></div></div>
      <div class="row"><div class="label">Other Deductions</div><div class="value"><?= formatCurrency($p['other_deductions']) ?></div></div>

      <div class="total">
        <div style="display:flex;justify-content:space-between">
          <div>NET PAY</div>
          <div><?= formatCurrency($p['net_pay']) ?></div>
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
