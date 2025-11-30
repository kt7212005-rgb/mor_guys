<?php
require 'config.php';
require 'header.php';

if (!isset($_GET['id'])) {
    echo "No invoice selected.";
    exit;
}

$id = $_GET['id'];

// Fetch invoice
$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id=?");
$stmt->execute([$id]);
$inv = $stmt->fetch();

if (!$inv) {
    echo "Invoice not found.";
    exit;
}

// Fetch students
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
    $payment_method = $_POST['payment_method'] ?? null;
    $payment_date = $_POST['payment_date'] ?? null;

    // Validation
    if (empty($invoice_no)) $errors[] = "Invoice number is required.";
    if ($student_id <= 0) $errors[] = "Please select a student.";
    if (empty($invoice_date)) $errors[] = "Invoice date is required.";
    if ($amount <= 0) $errors[] = "Amount must be greater than 0.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            $update = $pdo->prepare("UPDATE invoices SET invoice_no=?, student_id=?, invoice_date=?, amount=?, status=?, description=?, due_date=?, payment_method=?, payment_date=? WHERE id=?");
            $update->execute([
                $invoice_no,
                $student_id,
                $invoice_date,
                $amount,
                $status,
                $description,
                $due_date,
                $payment_method,
                $payment_date,
                $id
            ]);

            $pdo->commit();
            header("Location: invoices.php?success=1");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // Repopulate form values on error
    $inv = array_merge($inv, [
        'invoice_no' => $invoice_no,
        'student_id' => $student_id,
        'invoice_date' => $invoice_date,
        'amount' => $amount,
        'status' => $status,
        'description' => $description,
        'due_date' => $due_date,
        'payment_method' => $payment_method,
        'payment_date' => $payment_date
    ]);
}
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <h2>Edit Invoice</h2>
  
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="invoice_edit.php?id=<?= $id ?>" id="invoiceEditForm">
    <input type="hidden" name="id" value="<?= $id ?>">
    
    <div class="mb-3">
      <label>Invoice Number <span class="text-danger">*</span></label>
      <input name="invoice_no" class="form-control" value="<?=htmlspecialchars($inv['invoice_no'])?>" required>
    </div>
    
    <div class="mb-3">
      <label>Student <span class="text-danger">*</span></label>
      <select name="student_id" class="form-control" required>
        <option value="">Select Student</option>
        <?php foreach($students as $s): ?>
          <option value="<?=$s['id']?>" <?=$s['id']==$inv['student_id']?'selected':''?>>
            <?=htmlspecialchars($s['last_name'])?>, <?=htmlspecialchars($s['first_name'])?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <div class="mb-3">
      <label>Invoice Date <span class="text-danger">*</span></label>
      <input type="date" name="invoice_date" class="form-control" value="<?=htmlspecialchars($inv['invoice_date'])?>" required>
    </div>
    
    <div class="mb-3">
      <label>Due Date</label>
      <input type="date" name="due_date" class="form-control" value="<?=htmlspecialchars($inv['due_date'] ?? '')?>">
    </div>
    
    <div class="mb-3">
      <label>Amount <span class="text-danger">*</span></label>
      <input type="number" step="0.01" min="0" name="amount" class="form-control" value="<?=htmlspecialchars($inv['amount'])?>" required>
    </div>
    
    <div class="mb-3">
      <label>Description</label>
      <textarea name="description" class="form-control" rows="3"><?=htmlspecialchars($inv['description'] ?? '')?></textarea>
    </div>
    
    <div class="mb-3">
      <label>Status <span class="text-danger">*</span></label>
      <select name="status" class="form-control" id="statusSelect" required>
        <option value="pending" <?=$inv['status']=='pending'?'selected':''?>>Pending</option>
        <option value="paid" <?=$inv['status']=='paid'?'selected':''?>>Paid</option>
        <option value="overdue" <?=$inv['status']=='overdue'?'selected':''?>>Overdue</option>
        <option value="cancelled" <?=$inv['status']=='cancelled'?'selected':''?>>Cancelled</option>
      </select>
    </div>
    
    <div class="mb-3" id="paymentFields" style="display:<?= $inv['status'] == 'paid' ? 'block' : 'none' ?>;">
      <div class="card bg-light p-3">
        <h6>Payment Information</h6>
        <div class="mb-3">
          <label>Payment Method</label>
          <select name="payment_method" class="form-control">
            <option value="">Select Payment Method</option>
            <option value="Cash" <?=($inv['payment_method'] ?? '') == 'Cash' ? 'selected' : ''?>>Cash</option>
            <option value="Bank Transfer" <?=($inv['payment_method'] ?? '') == 'Bank Transfer' ? 'selected' : ''?>>Bank Transfer</option>
            <option value="Credit Card" <?=($inv['payment_method'] ?? '') == 'Credit Card' ? 'selected' : ''?>>Credit Card</option>
            <option value="Check" <?=($inv['payment_method'] ?? '') == 'Check' ? 'selected' : ''?>>Check</option>
            <option value="Online Payment" <?=($inv['payment_method'] ?? '') == 'Online Payment' ? 'selected' : ''?>>Online Payment</option>
          </select>
        </div>
        <div class="mb-3">
          <label>Payment Date</label>
          <input type="date" name="payment_date" class="form-control" value="<?=htmlspecialchars($inv['payment_date'] ?? '')?>">
        </div>
      </div>
    </div>
    
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary" id="updateBtn">
        <i class="bi bi-save me-2"></i>Update Invoice
      </button>
      <a href="invoice_view.php?id=<?= $id ?>" class="btn btn-info">
        <i class="bi bi-eye me-2"></i>View
      </a>
      <a href="invoices.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const statusSelect = document.getElementById('statusSelect');
  const paymentFields = document.getElementById('paymentFields');
  const form = document.getElementById('invoiceEditForm');
  const updateBtn = document.getElementById('updateBtn');
  
  // Show/hide payment fields based on status
  if(statusSelect && paymentFields) {
    statusSelect.addEventListener('change', function(){
      if(this.value === 'paid') {
        paymentFields.style.display = 'block';
      } else {
        paymentFields.style.display = 'none';
      }
    });
  }
  
  // Form submission handler
  if(form && updateBtn) {
    form.addEventListener('submit', function(e) {
      const invoiceNo = document.querySelector('input[name="invoice_no"]').value.trim();
      const studentId = document.querySelector('select[name="student_id"]').value;
      const amount = parseFloat(document.querySelector('input[name="amount"]').value);
      
      if(!invoiceNo) {
        e.preventDefault();
        alert('Please enter invoice number.');
        return false;
      }
      
      if(!studentId || studentId <= 0) {
        e.preventDefault();
        alert('Please select a student.');
        return false;
      }
      
      if(!amount || amount <= 0) {
        e.preventDefault();
        alert('Please enter a valid amount greater than 0.');
        return false;
      }
      
      // Disable button to prevent double submission
      updateBtn.disabled = true;
      updateBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
      
      return true;
    });
  }
});
</script>

<?php require 'footer.php'; ?>
