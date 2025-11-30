<?php
require 'config.php';
require 'header.php';


$logs = $pdo->query("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10");
$activities = $logs->fetchAll();

?>
 <link rel="stylesheet" href="style.css">
<div class="container-fluid p-4" style="margin-left: 280px; max-width: calc(100% - 280px);">
 
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="mb-1">Welcome back! Admin👋</h2>
      <p class="text-muted mb-0">
        <i class="bi bi-calendar3 me-2"></i>
        Today is <?php echo date('l, F j, Y'); ?> | 
        <i class="bi bi-clock me-2"></i>
        <?php echo date('g:i A'); ?>
      </p>
    </div>
    <div class="d-flex gap-2">
      <button class="btn btn-success">
        <i class="bi bi-bell me-2"></i>Reminders
        <span class="badge bg-danger ms-1"></span>
      </button>
      <button class="btn btn-outline-success">
        <i class="bi bi-gear me-2"></i>Settings
      </button>
    </div>
  </div>

 
  <div class="card mb-4">
    <div class="card-body">
      <h5 class="card-title mb-3">
        <i class="bi bi-search me-2 text-success"></i>Quick Search
      </h5>
      <div class="row">
        <div class="col-md-8">
          <input type="text" class="form-control" placeholder="Search students, invoices, employees..." id="quickSearch">
        </div>
        <div class="col-md-4">
          <button class="btn btn-success w-100">
            <i class="bi bi-search me-2"></i>Search
          </button>
        </div>
      </div>
    </div>
  </div>

 
  <div class="row mb-4">
   
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="bi bi-people-fill text-success" style="font-size: 2.5rem;"></i>
          <h5 class="card-title mt-2">Students</h5>
          <?php
          try {
              $count = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
              echo "<h3 class='text-success fw-bold'>$count</h3>";
              echo "<small class='text-success'><i class='bi bi-arrow-up'></i> +2 this week</small>";
          } catch(PDOException $e) {
              echo "<h3 class='text-muted'>0</h3>";
              echo "<small class='text-muted'>No data</small>";
          }
          ?>
          <div class="mt-3">
            <a href="students.php" class="btn btn-success btn-sm">View All</a>
          </div>
        </div>
      </div>
    </div>

 
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="bi bi-file-earmark-text-fill text-info" style="font-size: 2.5rem;"></i>
          <h5 class="card-title mt-2">Invoices</h5>
          <?php
          try {
              $countInv = $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
              $pending = $pdo->query("SELECT COUNT(*) FROM invoices WHERE status='pending'")->fetchColumn();
              echo "<h3 class='text-info fw-bold'>$countInv</h3>";
              echo "<small class='text-warning'><i class='bi bi-clock'></i> $pending pending</small>";
          } catch(PDOException $e) {
              echo "<h3 class='text-muted'>0</h3>";
              echo "<small class='text-muted'>No data</small>";
          }
          ?>
          <div class="mt-3">
            <a href="invoices.php" class="btn btn-info btn-sm">View All</a>
          </div>
        </div>
      </div>
    </div>

   
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="bi bi-person-badge-fill text-warning" style="font-size: 2.5rem;"></i>
          <h5 class="card-title mt-2">Faculty Members</h5>
          <?php
          try {
              $countEmp = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
              echo "<h3 class='text-warning fw-bold'>$countEmp</h3>";
              echo "<small class='text-muted'><i class='bi bi-check-circle'></i> All active</small>";
          } catch(PDOException $e) {
              echo "<h3 class='text-muted'>0</h3>";
              echo "<small class='text-muted'>No data</small>";
          }
          ?>
          <div class="mt-3">
            <a href="employees.php" class="btn btn-warning btn-sm">View All</a>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card h-100">
        <div class="card-body text-center">
          <i class="bi bi-cash-stack text-primary" style="font-size: 2.5rem;"></i>
          <h5 class="card-title mt-2">Revenue</h5>
          <?php
          try {
              $revenue = $pdo->query("SELECT SUM(amount) FROM invoices WHERE status='paid'")->fetchColumn();
              echo "<h3 class='text-primary fw-bold'>₱" . number_format($revenue ?: 0, 2) . "</h3>";
              echo "<small class='text-success'><i class='bi bi-graph-up'></i> +12% this month</small>";
          } catch(PDOException $e) {
              echo "<h3 class='text-muted'>₱0.00</h3>";
              echo "<small class='text-muted'>No data</small>";
          }
          ?>
          <div class="mt-3">
            <a href="reports.php" class="btn btn-primary btn-sm">View Reports</a>
          </div>
        </div>
      </div>
    </div>
  </div>
<div class="card">
  <div class="card-body">
    <h5 class="card-title mb-3">Recent Activities</h5>

    <?php
    try {
        $activities = $pdo->query("SELECT * FROM activity_log ORDER BY activity_date DESC LIMIT 10")->fetchAll();
    } catch (PDOException $e) {
        $activities = [];
    }
    ?>

    <div class="table-responsive">
      <table class="table table-hover">
        <thead>
          <tr>
            <th>Activity</th>
            <th>User</th>
            <th>Date</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
<?php if(count($activities) > 0): ?>
    <?php foreach ($activities as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['activity']) ?></td>
            <td><?= htmlspecialchars($row['user']) ?></td>
            <td><?= htmlspecialchars($row['created_at']) ?></td>

            <td>
                <span class="badge 
                    <?= $row['status'] == 'Completed' ? 'bg-success' : 
                       ($row['status'] == 'Pending' ? 'bg-warning' : 'bg-danger') ?>">
                    <?= htmlspecialchars($row['status']) ?>
                </span>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="4" class="text-center">No recent activities</td>
    </tr>
<?php endif; ?>
</tbody>

      </table>
    </div>
  </div>
</div>
