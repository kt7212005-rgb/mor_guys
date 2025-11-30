<?php
require 'config.php';

if (!isset($_GET['id'])) die("Item ID not provided.");
$id = $_GET['id'];

$item = $pdo->prepare("SELECT * FROM inventory WHERE id=?");
$item->execute([$id]);
$item = $item->fetch();
if (!$item) die("Item not found.");

require 'header.php';
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <h2>View Inventory Item</h2>
  <div class="card shadow-sm" style="max-width:600px;">
    <div class="card-body">
      <p><strong>Item Name:</strong> <?= htmlspecialchars($item['item_name']) ?></p>
      <p><strong>Quantity:</strong> <?= htmlspecialchars($item['quantity'] ?? 0) ?></p>
      <p><strong>Condition:</strong> 
        <?php 
          $condClass = ($item['condition'] ?? 'Good') == 'Good' ? 'text-success' : (($item['condition'] ?? '') == 'Poor' ? 'text-warning' : 'text-danger');
          echo "<span class='$condClass'>" . htmlspecialchars($item['condition'] ?? 'N/A') . "</span>";
        ?>
      </p>
      <p><strong>Room:</strong> <?= htmlspecialchars($item['room'] ?? 'N/A') ?></p>
      <p><strong>Technician / Responsible:</strong> <?= htmlspecialchars($item['technician'] ?? 'N/A') ?></p>
      <?php if(!empty($item['item_code'])): ?>
        <p><strong>Item Code:</strong> <?= htmlspecialchars($item['item_code']) ?></p>
      <?php endif; ?>
      <?php if(!empty($item['description'])): ?>
        <p><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
      <?php endif; ?>
      <?php if(!empty($item['category'])): ?>
        <p><strong>Category:</strong> <?= htmlspecialchars($item['category']) ?></p>
      <?php endif; ?>
      <?php if(!empty($item['unit_price'])): ?>
        <p><strong>Unit Price:</strong> ₱<?= number_format($item['unit_price'], 2) ?></p>
      <?php endif; ?>
    </div>
    <div class="card-footer">
      <div class="d-flex gap-2">
        <a href="inventory_edit.php?id=<?= $item['id'] ?>" class="btn btn-primary">
          <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="invetory_delete.php?id=<?= $item['id'] ?>" 
           class="btn btn-danger"
           onclick="return confirm('Are you sure you want to delete this item?');">
          <i class="bi bi-trash me-2"></i>Delete
        </a>
        <a href="inventory.php" class="btn btn-secondary">Back to List</a>
      </div>
    </div>
  </div>
</div>

<?php require 'footer.php'; ?>
