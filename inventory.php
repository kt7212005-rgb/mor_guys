<?php
require 'config.php';
require 'header.php';

// Fetch all inventory items
$items = $pdo->query("SELECT * FROM inventory ORDER BY id DESC")->fetchAll();
?>

<div style="margin-left:280px; max-width:calc(100% - 280px);">
  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> Inventory item has been saved successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Success!</strong> Inventory item has been deleted successfully.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <strong>Error!</strong> <?= htmlspecialchars($_GET['error']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Inventory</h2>
    <a href="#" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addItemModal">
      <i class="bi bi-plus-circle me-2"></i>Add Item
    </a>
  </div>

  <div class="row">
    <?php foreach($items as $it): ?>
      <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
          <div class="card-header text-center">
            <strong><?= htmlspecialchars($it['item_name']) ?></strong>
          </div>
          <div class="card-body">
            <p><strong>Quantity:</strong> <?= htmlspecialchars($it['quantity']) ?></p>
            <p><strong>Condition:</strong> 
              <?php 
                $condClass = $it['condition'] == 'Good' ? 'text-success' : ($it['condition'] == 'Poor' ? 'text-warning' : 'text-danger');
                echo "<span class='$condClass'>".$it['condition']."</span>";
              ?>
            </p>
            <p><strong>Room:</strong> <?= htmlspecialchars($it['room'] ?? 'N/A') ?></p>
            <p><strong>Responsible:</strong> <?= htmlspecialchars($it['technician'] ?? 'N/A') ?></p>
          </div>
          <div class="card-footer text-center">
            <div class="btn-group btn-group-sm">
              <a href="invetory_view.php?id=<?= $it['id'] ?>" class="btn btn-outline-success" title="View">
                <i class="bi bi-eye"></i>
              </a>
              <a href="inventory_edit.php?id=<?= $it['id'] ?>" class="btn btn-outline-primary" title="Edit">
                <i class="bi bi-pencil"></i>
              </a>
              <a href="invetory_delete.php?id=<?= $it['id'] ?>" class="btn btn-outline-danger"
                 onclick="return confirm('Delete this item?');" title="Delete">
                <i class="bi bi-trash"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- Modal for Add Item -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" action="inventory_add.php" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Inventory Item</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>Item Name</label><input name="item_name" class="form-control" required></div>
        <div class="mb-3"><label>Quantity</label><input type="number" name="quantity" class="form-control" value="1"></div>
        <div class="mb-3"><label>Condition</label>
          <select name="condition" class="form-control">
            <option>Good</option>
            <option>Poor</option>
            <option>Needs Replacement</option>
          </select>
        </div>
        <div class="mb-3"><label>Room</label><input name="room" class="form-control"></div>
        <div class="mb-3"><label>Technician / Responsible</label><input name="technician" class="form-control"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">Add</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </form>
  </div>
</div>

<?php require 'footer.php'; ?>
