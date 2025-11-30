<?php
require 'config.php';
require 'header.php';
?>

<!-- Invoice Management Section -->
<div class="invoices-management">
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-left: 280px; max-width: calc(100% - 280px);">
      <strong>Success!</strong> Invoice has been saved successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-left: 280px; max-width: calc(100% - 280px);">
      <strong>Success!</strong> Invoice has been deleted successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin-left: 280px; max-width: calc(100% - 280px);">
      <strong>Error!</strong> <?= htmlspecialchars($_GET['error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-4" style="margin-left: 280px; max-width: calc(100% - 280px);">
    <div>
      <h2 class="mb-1">Invoice Management</h2>
      <p class="text-muted">Manage student invoices and payment tracking</p>
    </div>
    <a href="invoice_create.php" class="btn btn-success" >
      <i class="bi bi-plus-circle me-2"></i>Create Invoice
    </a>
  </div>

  <!-- Invoice Statistics -->
  <div class="row mb-4" style="margin-left: 280px; max-width: calc(100% - 280px);">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-file-earmark-text text-success" style="font-size: 2rem;"></i>
          <h5 class="card-title mt-2">Total Invoices</h5>
          <p class="display-6 text-success fw-bold">
            <?php
            try {
                echo $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
            } catch(PDOException $e) {
                echo "0";
            }
            ?>
          </p>
        </div>
      </div>
    </div>
    <div class="col-md-3" >
      <div class="card border-0 shadow-sm" >
        <div class="card-body text-center">
          <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
          <h5 class="card-title mt-2">Pending</h5>
          <p class="display-6 text-warning fw-bold">
            <?php
            try {
                echo $pdo->query("SELECT COUNT(*) FROM invoices WHERE status='pending'")->fetchColumn();
            } catch(PDOException $e) {
                echo "0";
            }
            ?>
          </p>
        </div>
      </div>
    </div>
    <div class="col-md-3" >
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-check-circle text-info" style="font-size: 2rem;"></i>
          <h5 class="card-title mt-2">Paid</h5>
          <p class="display-6 text-info fw-bold">
            <?php
            try {
                echo $pdo->query("SELECT COUNT(*) FROM invoices WHERE status='paid'")->fetchColumn();
            } catch(PDOException $e) {
                echo "0";
            }
            ?>
          </p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body text-center">
          <i class="bi bi-currency-dollar text-success" style="font-size: 2rem;"></i>
          <h5 class="card-title mt-2">Total Revenue</h5>
          <p class="display-6 text-success fw-bold">
            ₱<?php
            try {
                $total = $pdo->query("SELECT SUM(amount) FROM invoices WHERE status='paid'")->fetchColumn();
                echo number_format($total ?: 0, 2);
            } catch(PDOException $e) {
                echo "0.00";
            }
            ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Invoices Table -->
  <div class="card border-0 shadow-sm" style="margin-left: 280px; max-width: calc(100% - 280px);">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">
        <i class="bi bi-file-earmark-text me-2"></i>Recent Invoices
      </h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>Invoice #</th>
              <th>Student</th>
              <th>Date</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php
            try {
                $invoices = $pdo->query("SELECT i.*, s.first_name, s.last_name FROM invoices i LEFT JOIN students s ON i.student_id = s.id ORDER BY i.id DESC LIMIT 50")->fetchAll();
                
                if (count($invoices) > 0) {
                    foreach($invoices as $inv):
            ?>
                <tr>
                  <td><strong><?=htmlspecialchars($inv['invoice_no'])?></strong></td>
                  <td><?=htmlspecialchars(($inv['first_name'] ?? 'N/A').' '.($inv['last_name'] ?? ''))?></td>
                  <td><?=date('M d, Y', strtotime($inv['invoice_date']))?></td>
                  <td class="fw-bold">₱<?=number_format($inv['amount'], 2)?></td>
                  <td>
                    <?php
                    $statusClass = $inv['status'] == 'paid' ? 'success' : ($inv['status'] == 'pending' ? 'warning' : 'secondary');
                    ?>
                    <span class="badge bg-<?=$statusClass?>"><?=ucfirst(htmlspecialchars($inv['status']))?></span>
                  </td>
                <td>
  <div class="btn-group btn-group-sm">
    <a href="invoice_view.php?id=<?= $inv['id'] ?>" class="btn btn-outline-success" title="View">
      <i class="bi bi-eye"></i>
    </a>
    <a href="invoice_edit.php?id=<?= $inv['id'] ?>" class="btn btn-outline-primary" title="Edit">
      <i class="bi bi-pencil"></i>
    </a>
    <a href="invoice_delete.php?id=<?= $inv['id'] ?>" 
       class="btn btn-outline-danger"
       onclick="return confirm('Delete this invoice?');"
       title="Delete">
      <i class="bi bi-trash"></i>
    </a>
  </div>
</td>

                </tr>
            <?php 
                    endforeach;
                } else {
            ?>
                <tr>
                  <td colspan="6" class="text-center text-muted py-4">
                    <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                    <p class="mb-0">No invoices found. Create your first invoice to get started.</p>
                  </td>
                </tr>
            <?php
                }
            } catch(PDOException $e) {
            ?>
                <tr>
                  <td colspan="6" class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem;"></i>
                    <p class="mb-0">Error loading invoice data. Please check database connection.</p>
                  </td>
                </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require 'footer.php'; ?>