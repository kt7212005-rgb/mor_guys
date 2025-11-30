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
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        header('Location: payroll.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: payroll.php');
    exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employee_id = trim($_POST['employee_id'] ?? '');
    $employee_name = trim($_POST['employee_name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $monthly_salary = floatval($_POST['monthly_salary'] ?? 0);
    $bi_weekly_rate = round($monthly_salary / 2, 2);
    $sss = floatval($_POST['sss'] ?? 0);
    $pagibig = floatval($_POST['pagibig'] ?? 0);
    $philhealth = floatval($_POST['philhealth'] ?? 0);
    $other_deductions = floatval($_POST['other_deductions'] ?? 0);
    $allowances = floatval($_POST['allowances'] ?? 0);
    $pay_date = $_POST['pay_date'] ?? null;

    $total_deductions = $sss + $pagibig + $philhealth + $other_deductions;
    $net_pay = round($bi_weekly_rate + $allowances - $total_deductions, 2);
    if ($net_pay < 0) $net_pay = 0.00;

    if ($employee_name === '') $errors[] = "Employee name is required.";
    if ($monthly_salary <= 0) $errors[] = "Monthly salary must be greater than 0.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            $update = $pdo->prepare("UPDATE payroll SET
                employee_id = ?, employee_name = ?, position = ?, email = ?,
                monthly_salary = ?, bi_weekly_rate = ?, sss = ?, pagibig = ?, philhealth = ?, other_deductions = ?, allowances = ?, pay_date = ?, net_pay = ?
                WHERE id = ?");
            $update->execute([
                $employee_id, $employee_name, $position, $email,
                $monthly_salary, $bi_weekly_rate, $sss, $pagibig, $philhealth, $other_deductions, $allowances, $pay_date, $net_pay,
                $id
            ]);
            
            $pdo->commit();
            header('Location: payroll.php?success=1');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    // refresh $row values used in form after POST (to show user input if errors)
    $row = array_merge($row, [
        'employee_id' => $employee_id,
        'employee_name' => $employee_name,
        'position' => $position,
        'email' => $email,
        'monthly_salary' => $monthly_salary,
        'bi_weekly_rate' => $bi_weekly_rate,
        'sss' => $sss,
        'pagibig' => $pagibig,
        'philhealth' => $philhealth,
        'other_deductions' => $other_deductions,
        'allowances' => $allowances,
        'pay_date' => $pay_date,
        'net_pay' => $net_pay
    ]);
}

?>
<link rel="stylesheet" href="style.css">

<div class="container-fluid p-4" style="margin-left:280px; max-width: calc(100% - 280px);">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Edit Payroll</h5>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><ul><?php foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>"; ?></ul></div>
      <?php endif; ?>

      <form method="post" action="payroll_edit.php?id=<?= $id ?>">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Employee ID</label>
            <input name="employee_id" class="form-control" value="<?= htmlspecialchars($row['employee_id'] ?? '') ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input name="employee_name" class="form-control" value="<?= htmlspecialchars($row['employee_name'] ?? '') ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Position</label>
            <input name="position" class="form-control" value="<?= htmlspecialchars($row['position'] ?? '') ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" value="<?= htmlspecialchars($row['email'] ?? '') ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Monthly Salary <span class="text-danger">*</span></label>
            <input name="monthly_salary" type="number" step="0.01" min="0" class="form-control" value="<?= htmlspecialchars($row['monthly_salary'] ?? 0) ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Bi-Weekly Rate (Auto-calculated)</label>
            <input name="bi_weekly_rate" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars(round($row['bi_weekly_rate'] ?? 0, 2)) ?>" readonly style="background-color:#f8f9fa;">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">SSS</label>
            <input name="sss" type="number" step="0.01" min="0" class="form-control" value="<?= htmlspecialchars($row['sss'] ?? 0) ?>">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Pag-IBIG</label>
            <input name="pagibig" type="number" step="0.01" min="0" class="form-control" value="<?= htmlspecialchars($row['pagibig'] ?? 0) ?>">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">PhilHealth</label>
            <input name="philhealth" type="number" step="0.01" min="0" class="form-control" value="<?= htmlspecialchars($row['philhealth'] ?? 0) ?>">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Other Deductions</label>
            <input name="other_deductions" type="number" step="0.01" min="0" class="form-control" value="<?= htmlspecialchars($row['other_deductions'] ?? 0) ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Allowances</label>
            <input name="allowances" type="number" step="0.01" min="0" class="form-control" value="<?= htmlspecialchars($row['allowances'] ?? 0) ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Pay Date</label>
            <input name="pay_date" type="date" class="form-control" value="<?= htmlspecialchars($row['pay_date'] ?? '') ?>">
          </div>
          
          <div class="col-md-12 mb-3">
            <div class="card bg-light">
              <div class="card-body">
                <h6 class="card-title">Net Pay Calculation</h6>
                <p class="mb-0">
                  <strong>Bi-Weekly Rate:</strong> ₱<span id="calc-bi-weekly"><?= number_format($row['bi_weekly_rate'] ?? 0, 2) ?></span> + 
                  <strong>Allowances:</strong> ₱<span id="calc-allowances"><?= number_format($row['allowances'] ?? 0, 2) ?></span> - 
                  <strong>Total Deductions:</strong> ₱<span id="calc-deductions"><?= number_format(($row['sss'] ?? 0) + ($row['pagibig'] ?? 0) + ($row['philhealth'] ?? 0) + ($row['other_deductions'] ?? 0), 2) ?></span> = 
                  <strong class="text-success">Net Pay: ₱<span id="calc-net-pay"><?= number_format($row['net_pay'] ?? 0, 2) ?></span></strong>
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex gap-2">
          <a href="payroll.php" class="btn btn-secondary">Cancel</a>
          <a href="payroll_view.php?id=<?= $id ?>" class="btn btn-info">View</a>
          <button type="submit" class="btn btn-success">Update Payroll</button>
        </div>
      </form>

    </div>
  </div>
</div>

<?php require 'footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const monthlyInput = document.querySelector('input[name="monthly_salary"]');
  const biInput = document.querySelector('input[name="bi_weekly_rate"]');
  const sssInput = document.querySelector('input[name="sss"]');
  const pagibigInput = document.querySelector('input[name="pagibig"]');
  const philhealthInput = document.querySelector('input[name="philhealth"]');
  const otherDeductionsInput = document.querySelector('input[name="other_deductions"]');
  const allowancesInput = document.querySelector('input[name="allowances"]');
  
  function calculateNetPay() {
    const monthly = parseFloat(monthlyInput.value) || 0;
    const biWeekly = monthly / 2;
    const sss = parseFloat(sssInput.value) || 0;
    const pagibig = parseFloat(pagibigInput.value) || 0;
    const philhealth = parseFloat(philhealthInput.value) || 0;
    const otherDeductions = parseFloat(otherDeductionsInput.value) || 0;
    const allowances = parseFloat(allowancesInput.value) || 0;
    
    const totalDeductions = sss + pagibig + philhealth + otherDeductions;
    const netPay = Math.max(0, biWeekly + allowances - totalDeductions);
    
    if(biInput) {
      biInput.value = (Math.round(biWeekly * 100) / 100).toFixed(2);
    }
    
    // Update calculation display
    document.getElementById('calc-bi-weekly').textContent = biWeekly.toFixed(2);
    document.getElementById('calc-allowances').textContent = allowances.toFixed(2);
    document.getElementById('calc-deductions').textContent = totalDeductions.toFixed(2);
    document.getElementById('calc-net-pay').textContent = netPay.toFixed(2);
  }
  
  if(monthlyInput) {
    monthlyInput.addEventListener('input', calculateNetPay);
  }
  if(sssInput) sssInput.addEventListener('input', calculateNetPay);
  if(pagibigInput) pagibigInput.addEventListener('input', calculateNetPay);
  if(philhealthInput) philhealthInput.addEventListener('input', calculateNetPay);
  if(otherDeductionsInput) otherDeductionsInput.addEventListener('input', calculateNetPay);
  if(allowancesInput) allowancesInput.addEventListener('input', calculateNetPay);
  
  // Initial calculation
  calculateNetPay();
});
</script>
