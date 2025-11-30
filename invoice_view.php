<?php
require 'config.php';
require 'header.php';

if (!isset($_GET['id'])) {
    echo "No invoice selected.";
    exit;
}

$id = $_GET['id'];

// Fetch invoice with student info
$stmt = $pdo->prepare("SELECT i.*, s.first_name, s.last_name FROM invoices i LEFT JOIN students s ON i.student_id = s.id WHERE i.id=?");
$stmt->execute([$id]);
$inv = $stmt->fetch();

if (!$inv) {
    echo "Invoice not found.";
    exit;
}
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <h2>View Invoice</h2>
  <div class="card shadow-sm p-3">
    <p><strong>Invoice #: </strong><?=htmlspecialchars($inv['invoice_no'])?></p>
    <p><strong>Student: </strong><?=htmlspecialchars($inv['first_name'].' '.$inv['last_name'])?></p>
    <p><strong>Date: </strong><?=date('M d, Y', strtotime($inv['invoice_date']))?></p>
    <p><strong>Amount: </strong>₱<?=number_format($inv['amount'], 2)?></p>
    <p><strong>Description: </strong><?=htmlspecialchars($inv['description'] ?? 'N/A')?></p>
    <p><strong>Status: </strong>
      <?php
      $statusClass = $inv['status'] == 'paid' ? 'success' : ($inv['status'] == 'pending' ? 'warning' : ($inv['status'] == 'overdue' ? 'danger' : 'secondary'));
      ?>
      <span class="badge bg-<?=$statusClass?>"><?=ucfirst(htmlspecialchars($inv['status']))?></span>
    </p>
    <?php if(!empty($inv['due_date'])): ?>
      <p><strong>Due Date: </strong><?=date('M d, Y', strtotime($inv['due_date']))?></p>
    <?php endif; ?>
    <?php if($inv['status'] == 'paid' && !empty($inv['payment_date'])): ?>
      <p><strong>Payment Date: </strong><?=date('M d, Y', strtotime($inv['payment_date']))?></p>
      <?php if(!empty($inv['payment_method'])): ?>
        <p><strong>Payment Method: </strong><?=htmlspecialchars($inv['payment_method'])?></p>
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="mt-3">
    <a href="invoice_edit.php?id=<?= $inv['id'] ?>" class="btn btn-primary">
      <i class="bi bi-pencil me-2"></i>Edit
    </a>
    <a href="invoices.php" class="btn btn-secondary">Back to List</a>
  </div>
</div>

<?php require 'footer.php'; ?>
