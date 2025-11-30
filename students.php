<?php
require 'config.php';
require 'header.php';
?>

<!-- Student Management Section -->
<div class="students-management">
  <div class="d-flex justify-content-between align-items-center mb-4" style="margin-left: 280px; max-width: calc(100% - 280px);">
    <div>
      <h2 class="mb-1">Student Management</h2>
      <p class="text-muted">Manage student records and medical information</p>
    </div>
    <a href="students_add.php" class="btn btn-success">
      <i class="bi bi-plus-circle me-2"></i>Add Student
    </a>
  </div>

  <!-- Students Table -->
  <div class="card border-0 shadow-sm" style="margin-left: 280px; max-width: calc(100% - 280px);">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">
        <i class="bi bi-people me-2"></i>Student Records
      </h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Student No</th>
              <th>Name</th>
              <th>Parent Contact</th>
              <th>Grade Level</th>
              <th>Medical Info</th>
              <th>Status</th>
               <th>Action</th>
             
            </tr>
          </thead>
          <tbody>
            <?php
            try {
                $stmt = $pdo->query("SELECT s.*, sm.allergies, sm.immunizations, sm.blood_type 
                                     FROM students s 
                                     LEFT JOIN student_medical sm ON s.id = sm.student_id 
                                     ORDER BY s.id DESC");
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($students) {
                    foreach($students as $s):
            ?>
                <tr>
                  <td><?= htmlspecialchars($s['id']) ?></td>
                  <td><strong><?= htmlspecialchars($s['student_no'] ?? 'N/A') ?></strong></td>
                  <td><?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?></td>
                  <td><?= htmlspecialchars($s['contact'] ?? 'N/A') ?></td>
                  <td><?= htmlspecialchars($s['grade_level'] ?? 'N/A') ?></td>
                  <td>
                    <?php if(!empty($s['allergies'])): ?>
                      <span class="badge bg-danger mb-1">Allergies</span>
                    <?php endif; ?>
                    <?php if(!empty($s['blood_type'])): ?>
                      <small class="text-muted d-block">Blood: <?= htmlspecialchars($s['blood_type']) ?></small>
                    <?php endif; ?>
                    <?php if(empty($s['allergies']) && empty($s['blood_type'])): ?>
                      <small class="text-muted">No medical data</small>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge bg-success">Active</span>
                  </td>
                  <td>
                    <div class="btn-group btn-group-sm">
                      <a href="students_view.php?id=<?= $s['id'] ?>" class="btn btn-outline-success" title="View"><i class="bi bi-eye"></i></a>
                      <a href="students_edit.php?id=<?= $s['id'] ?>" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                      <a href="students_delete.php?id=<?= $s['id'] ?>" class="btn btn-outline-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this student?');"><i class="bi bi-trash"></i></a>
                    </div>
                  </td>
                </tr>
            <?php
                    endforeach;
                } else {
                    echo '<tr><td colspan="8" class="text-center text-muted py-4">No students found. Add your first student.</td></tr>';
                }
            } catch(PDOException $e) {
                echo '<tr><td colspan="8" class="text-center text-danger py-4">Error loading data: ' . $e->getMessage() . '</td></tr>';
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row mt-4" style="margin-left: 280px; max-width: calc(100% - 280px);">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
          <h5 class="card-title mt-2">Total Students</h5>
          <p class="display-6 text-success fw-bold"><?= $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn() ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-heart-pulse text-success" style="font-size: 2rem;"></i>
          <h5 class="card-title mt-2">Medical Records</h5>
          <p class="display-6 text-success fw-bold"><?= $pdo->query("SELECT COUNT(*) FROM student_medical")->fetchColumn() ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm text-center">
        <div class="card-body">
          <i class="bi bi-calendar-check text-success" style="font-size: 2rem;"></i>
          <h5 class="card-title mt-2">New This Month</h5>
          <p class="display-6 text-success fw-bold">
            <?php
            $month = date('m');
            $year = date('Y');
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE MONTH(created_at)=? AND YEAR(created_at)=?");
            $stmt->execute([$month, $year]);
            echo $stmt->fetchColumn();
            ?>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require 'footer.php'; ?>
