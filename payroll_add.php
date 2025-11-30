<?php
require 'config.php';
require 'header.php';

$errors = [];
$values = [
    'employee_id' => '',
    'employee_name' => '',
    'position' => '',
    'email' => '',
    'monthly_salary' => '',
    'bi_weekly_rate' => '',
    'sss' => 0,
    'pagibig' => 0,
    'philhealth' => 0,
    'other_deductions' => 0,
    'allowances' => 0,
    'pay_date' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // simple sanitize helper
    $employee_id = trim($_POST['employee_id'] ?? '');
    $employee_name = trim($_POST['employee_name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $monthly_salary = floatval($_POST['monthly_salary'] ?? 0);
    // bi-weekly auto-calc
    $bi_weekly_rate = round($monthly_salary / 2, 2);
    $sss = floatval($_POST['sss'] ?? 0);
    $pagibig = floatval($_POST['pagibig'] ?? 0);
    $philhealth = floatval($_POST['philhealth'] ?? 0);
    $other_deductions = floatval($_POST['other_deductions'] ?? 0);
    $allowances = floatval($_POST['allowances'] ?? 0);
    $pay_date = $_POST['pay_date'] ?? null;

    // net pay calculation
    $total_deductions = $sss + $pagibig + $philhealth + $other_deductions;
    $net_pay = round($bi_weekly_rate + $allowances - $total_deductions, 2);
    if ($net_pay < 0) $net_pay = 0.00;

    // validate
    if ($employee_name === '') $errors[] = "Employee name is required.";
    if ($monthly_salary <= 0) $errors[] = "Monthly salary must be greater than 0.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO payroll 
            (employee_id, employee_name, position, email, monthly_salary, bi_weekly_rate, sss, pagibig, philhealth, other_deductions, allowances, pay_date, net_pay)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $employee_id,
                $employee_name,
                $position,
                $email,
                $monthly_salary,
                $bi_weekly_rate,
                $sss,
                $pagibig,
                $philhealth,
                $other_deductions,
                $allowances,
                $pay_date,
                $net_pay
            ]);
            header('Location: payroll.php?success=1');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }

    // repopulate form values
    $values = [
        'employee_id' => htmlspecialchars($employee_id),
        'employee_name' => htmlspecialchars($employee_name),
        'position' => htmlspecialchars($position),
        'email' => htmlspecialchars($email),
        'monthly_salary' => $monthly_salary,
        'bi_weekly_rate' => $bi_weekly_rate,
        'sss' => $sss,
        'pagibig' => $pagibig,
        'philhealth' => $philhealth,
        'other_deductions' => $other_deductions,
        'allowances' => $allowances,
        'pay_date' => $pay_date,
    ];
}
?>
<link rel="stylesheet" href="style.css">

<div class="container-fluid p-4" style="margin-left: 280px; max-width: calc(100% - 280px);">
  <div class="card">
    <div class="card-body">
      <h5 class="card-title">Add Payroll</h5>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <ul>
            <?php foreach ($errors as $err): ?>
              <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="payroll_add.php">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Employee ID</label>
            <input name="employee_id" class="form-control" value="<?= $values['employee_id'] ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Name</label>
            <input name="employee_name" class="form-control" value="<?= $values['employee_name'] ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Position</label>
            <input name="position" class="form-control" value="<?= $values['position'] ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input name="email" type="email" class="form-control" value="<?= $values['email'] ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Monthly Salary</label>
            <input name="monthly_salary" type="number" step="0.01" class="form-control" value="<?= $values['monthly_salary'] ?>" required>
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Bi-Weekly Rate (Auto)</label>
            <input name="bi_weekly_rate" type="number" step="0.01" class="form-control" value="<?= $values['bi_weekly_rate'] ?>" readonly>
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">SSS</label>
            <input name="sss" type="number" step="0.01" class="form-control" value="<?= $values['sss'] ?>">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Pag-IBIG</label>
            <input name="pagibig" type="number" step="0.01" class="form-control" value="<?= $values['pagibig'] ?>">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">PhilHealth</label>
            <input name="philhealth" type="number" step="0.01" class="form-control" value="<?= $values['philhealth'] ?>">
          </div>

          <div class="col-md-3 mb-3">
            <label class="form-label">Other Deductions</label>
            <input name="other_deductions" type="number" step="0.01" class="form-control" value="<?= $values['other_deductions'] ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Allowances</label>
            <input name="allowances" type="number" step="0.01" class="form-control" value="<?= $values['allowances'] ?>">
          </div>

          <div class="col-md-6 mb-3">
            <label class="form-label">Pay Date (Bi-Weekly)</label>
            <input name="pay_date" type="date" class="form-control" value="<?= $values['pay_date'] ?>">
          </div>
        </div>

        <div class="d-flex gap-2">
          <a href="payroll.php" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-success">Save Payroll</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require 'footer.php'; ?>

<script>
// simple client-side helper to auto-calc bi-weekly when salary changes (optional)
document.addEventListener('DOMContentLoaded', function(){
  const monthlyInput = document.querySelector('input[name="monthly_salary"]');
  const biInput = document.querySelector('input[name="bi_weekly_rate"]');
  if(monthlyInput && biInput){
    monthlyInput.addEventListener('input', function(){
      const m = parseFloat(this.value) || 0;
      biInput.value = (Math.round((m/2) * 100) / 100).toFixed(2);
    });
    // trigger initial calc
    monthlyInput.dispatchEvent(new Event('input'));
  }
});
</script>
