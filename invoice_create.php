<?php
require 'config.php';
require 'header.php';

// Fetch all students for dropdown
$students = $pdo->query("SELECT id, first_name, last_name FROM students ORDER BY last_name")->fetchAll();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoice_no = trim($_POST['invoice_no'] ?? '');
    $student_id = intval($_POST['student_id'] ?? 0);
    $invoice_date = $_POST['invoice_date'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;

    // Validation
    if (empty($invoice_no)) $errors[] = "Invoice number is required.";
    if ($student_id <= 0) $errors[] = "Please select a student.";
    if (empty($invoice_date)) $errors[] = "Invoice date is required.";
    if ($amount <= 0) $errors[] = "Amount must be greater than 0.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO invoices (invoice_no, student_id, invoice_date, amount, status, description, due_date)
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $invoice_no,
                $student_id,
                $invoice_date,
                $amount,
                $status,
                $description,
                $due_date
            ]);

            header("Location: invoices.php?success=1");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <h2>Create Invoice</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <label>Invoice Number <span class="text-danger">*</span></label>
      <input name="invoice_no" class="form-control" value="<?= htmlspecialchars($_POST['invoice_no'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label>Student <span class="text-danger">*</span></label>
      <select name="student_id" class="form-control" required>
        <option value="">Select Student</option>
        <?php foreach($students as $s): ?>
          <option value="<?=$s['id']?>" <?=($_POST['student_id'] ?? '') == $s['id'] ? 'selected' : ''?>>
            <?=htmlspecialchars($s['last_name'])?>, <?=htmlspecialchars($s['first_name'])?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Invoice Date <span class="text-danger">*</span></label>
      <input type="date" name="invoice_date" class="form-control" required value="<?= htmlspecialchars($_POST['invoice_date'] ?? date('Y-m-d')) ?>">
    </div>

    <div class="mb-3">
      <label>Due Date</label>
      <input type="date" name="due_date" class="form-control" value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>">
    </div>

    <div class="mb-3">
      <label>Amount <span class="text-danger">*</span></label>
      <input type="number" step="0.01" min="0" name="amount" class="form-control" value="<?= htmlspecialchars($_POST['amount'] ?? '') ?>" required>
    </div>

    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label>Status</label>
      <select name="status" class="form-control">
        <option value="pending" <?=($_POST['status'] ?? 'pending') == 'pending' ? 'selected' : ''?>>Pending</option>
        <option value="paid" <?=($_POST['status'] ?? '') == 'paid' ? 'selected' : ''?>>Paid</option>
        <option value="overdue" <?=($_POST['status'] ?? '') == 'overdue' ? 'selected' : ''?>>Overdue</option>
      </select>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success">Create Invoice</button>
      <a href="invoices.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php require 'footer.php'; ?>
