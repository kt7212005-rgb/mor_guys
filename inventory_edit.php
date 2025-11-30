<?php
require 'config.php';

$errors = [];
$id = intval($_GET['id'] ?? 0);

if (!$id) {
    header("Location: inventory.php?error=invalid_id");
    exit;
}

// Fetch item
$itemStmt = $pdo->prepare("SELECT * FROM inventory WHERE id=?");
$itemStmt->execute([$id]);
$item = $itemStmt->fetch();

if (!$item) {
    header("Location: inventory.php?error=item_not_found");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_name = trim($_POST['item_name'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 0);
    $condition = $_POST['condition'] ?? 'Good';
    $room = trim($_POST['room'] ?? '');
    $technician = trim($_POST['technician'] ?? '');

    // Validation
    if (empty($item_name)) $errors[] = "Item name is required.";
    if ($quantity < 0) $errors[] = "Quantity cannot be negative.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("UPDATE inventory SET item_name=?, quantity=?, `condition`=?, room=?, technician=? WHERE id=?");
            $stmt->execute([
                $item_name,
                $quantity,
                $condition,
                $room,
                $technician,
                $id
            ]);
            
            $pdo->commit();
            header("Location: inventory.php?success=1");
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // Repopulate form values on error
    $item = array_merge($item, [
        'item_name' => $item_name,
        'quantity' => $quantity,
        'condition' => $condition,
        'room' => $room,
        'technician' => $technician
    ]);
}

require 'header.php';
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <h2>Edit Inventory Item</h2>
  
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($errors as $err): ?>
          <li><?= htmlspecialchars($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="inventory_edit.php?id=<?= $id ?>" id="inventoryEditForm">
    <input type="hidden" name="id" value="<?= $id ?>">
    
    <div class="mb-3">
      <label>Item Name <span class="text-danger">*</span></label>
      <input name="item_name" class="form-control" value="<?= htmlspecialchars($item['item_name']) ?>" required>
    </div>
    
    <div class="mb-3">
      <label>Quantity</label>
      <input type="number" min="0" name="quantity" class="form-control" value="<?= htmlspecialchars($item['quantity'] ?? 0) ?>">
    </div>
    
    <div class="mb-3">
      <label>Condition</label>
      <select name="condition" class="form-control">
        <option value="Good" <?= ($item['condition'] ?? '') == 'Good' ? 'selected' : '' ?>>Good</option>
        <option value="Poor" <?= ($item['condition'] ?? '') == 'Poor' ? 'selected' : '' ?>>Poor</option>
        <option value="Needs Replacement" <?= ($item['condition'] ?? '') == 'Needs Replacement' ? 'selected' : '' ?>>Needs Replacement</option>
      </select>
    </div>
    
    <div class="mb-3">
      <label>Room</label>
      <input name="room" class="form-control" value="<?= htmlspecialchars($item['room'] ?? '') ?>">
    </div>
    
    <div class="mb-3">
      <label>Technician / Responsible</label>
      <input name="technician" class="form-control" value="<?= htmlspecialchars($item['technician'] ?? '') ?>">
    </div>
    
    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary" id="updateBtn">
        <i class="bi bi-save me-2"></i>Update Item
      </button>
      <a href="invetory_view.php?id=<?= $id ?>" class="btn btn-info">
        <i class="bi bi-eye me-2"></i>View
      </a>
      <a href="inventory.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('inventoryEditForm');
  const updateBtn = document.getElementById('updateBtn');
  
  if(form && updateBtn) {
    form.addEventListener('submit', function(e) {
      const itemName = document.querySelector('input[name="item_name"]').value.trim();
      const quantity = parseInt(document.querySelector('input[name="quantity"]').value) || 0;
      
      if(!itemName) {
        e.preventDefault();
        alert('Please enter item name.');
        return false;
      }
      
      if(quantity < 0) {
        e.preventDefault();
        alert('Quantity cannot be negative.');
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
