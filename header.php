<?php


?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>MOR system </title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/custom.css" rel="stylesheet">
 

  <style>
 
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #ffffff;
      color: #333;
      line-height: 1.5;
    }

    .sidebar {
      position: fixed;
      top: 0;
      left: 0;
      width: 280px;
      height: 100vh;
      background: #1e3a2a;
      overflow-y: auto;
      z-index: 1000;
    }

    /* ===== CARDS ===== */
    .card {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      background: #fff;
      margin-bottom: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .card-header {
      background: #1e3a2a;
      color: #fff;
      padding: 16px;
      border-bottom: 1px solid #e5e7eb;
    }

    .card-body {
      padding: 20px;
    }

    /* ===== BUTTONS ===== */
    .btn {
      border-radius: 6px;
      padding: 10px 20px;
      font-size: 14px;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn:hover {
      opacity: 0.9;
      transform: translateY(-1px);
    }

    .btn-success {
      background: #1e3a2a;
      color: #fff;
    }

    .btn-info {
      background: #0ea5e9;
      color: #fff;
    }

    .btn-warning {
      background: #f59e0b;
      color: #fff;
    }

    .btn-primary {
      background: #3b82f6;
      color: #fff;
    }

    /* ===== TABLES ===== */
    .table {
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
    }

    .table thead th {
      background: #1e3a2a;
      color: #fff;
      padding: 12px;
      font-weight: 600;
    }

    .table-hover tbody tr:hover {
      background: #f8f9fa;
    }

    /* ===== NAVIGATION ===== */
    .nav-link {
      color: rgba(255,255,255,0.8);
      padding: 12px 20px;
      margin: 2px 12px;
      border-radius: 6px;
      text-decoration: none;
      display: flex;
      align-items: center;
      font-size: 14px;
      transition: all 0.2s ease;
    }

    .nav-link:hover {
      background: rgba(255,255,255,0.15);
      color: #fff;
    }

    .nav-link.active {
      background: rgba(255,255,255,0.2);
      color: #fff;
      font-weight: 600;
    }

    .nav-link i {
      margin-right: 12px;
      font-size: 18px;
    }

    /* ===== BRAND LOGO ===== */
    .brand-logo {
      color: #fff;
      font-size: 18px;
      font-weight: 700;
      padding: 16px;
      margin: 12px;
      text-decoration: none;
      display: flex;
      align-items: center;
      background: rgba(255,255,255,0.1);
      border-radius: 8px;
    }

    .brand-logo:hover {
      color: #fff;
      background: rgba(255,255,255,0.15);
    }

    .brand-logo i {
      margin-right: 12px;
      font-size: 24px;
    }

    /* ===== SEPARATOR ===== */
    .nav-separator {
      height: 1px;
      background: rgba(255,255,255,0.2);
      margin: 12px 12px;
      border: none;
    }

    /* ===== FOOTER ===== */
    .sidebar-footer {
      color: rgba(255,255,255,0.7);
      font-size: 12px;
      text-align: center;
      padding: 12px;
      background: rgba(255,255,255,0.05);
      border-radius: 8px;
      margin: 12px;
    }

    .sidebar-footer i {
      display: block;
      font-size: 24px;
      margin-bottom: 8px;
    }

    /* ===== PAGE HEADER ===== */
    .page-header {
      background: #1e3a2a;
      color: #fff;
      padding: 20px;
      margin: 0;
    }

    .page-header h1 {
      font-size: 24px;
      font-weight: 700;
      margin: 0;
    }

    /* ===== DASHBOARD CARDS ===== */
    .dashboard-card {
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      background: #fff;
      position: relative;
    }

    .dashboard-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: #1e3a2a;
    }

    .dashboard-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .icon-wrapper {
      width: 60px;
      height: 60px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 12px;
      background: rgba(30, 58, 42, 0.1);
    }

    /* ===== RESPONSIVE DESIGN ===== */
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        position: relative;
        height: auto;
      }

      .main-layout {
        flex-direction: column;
      }

      .main-content {
        padding: 16px;
      }

      .page-header {
        padding: 16px 20px;
      }

      .page-header h1 {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="main-layout">
    <nav class="sidebar d-flex flex-column flex-shrink-0 p-3">
            <img src="logo.jpg" 
                style=
                "width: 120px;
                height: 120px;
                object-fit: cover;
                border-radius: 50%;
                border: 3px solid #ffffff55;
                display:flex;
                justify-content: center;
                margin-bottom: 1">  <h5 class="text-white fw-bold mb-0">School Management System</h5>
      </a>
      <hr class="nav-separator">
      <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
          <a href="index.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="bi bi-house-door-fill me-2"></i>Dashboard
          </a>
        </li>
        <li>
          <a href="students.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>">
            <i class="bi bi-people-fill me-2"></i>Students
          </a>
        </li>
        <li>
          <a href="invoices.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'invoices.php' ? 'active' : ''; ?>">
            <i class="bi bi-file-earmark-text-fill me-2"></i>Invoices
          </a>
        </li>
        <li>
          <a href="employees.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'employees.php' ? 'active' : ''; ?>">
            <i class="bi bi-person-badge-fill me-2"></i>Employees
          </a>
        </li>
        <li>
          <a href="inventory.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'active' : ''; ?>">
            <i class="bi bi-box-seam-fill me-2"></i>Inventory
          </a>
        </li>
        <li>
          <a href="payroll.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'payroll.php' ? 'active' : ''; ?>">
            <i class="bi bi-cash-stack me-2"></i>Payroll
          </a>
        </li>
        <li>
          <a href="reports.php" class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>">
            <i class="bi bi-graph-up me-2"></i>Reports
          </a>
        </li>
      </ul>
      <hr class="nav-separator">
      <ul class="nav nav-pills flex-column mb-auto">
        <li>
          <a href="logout.php" class="nav-link text-white">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
          </a>
        </li>
      </ul>
      <hr class="nav-separator">
      <div class="sidebar-footer">
        <i class="bi bi-building"></i>
        <div>The Redeemed Citadel</div>
        <div>of Education Incorporated</div>
      </div>
    </nav>

    <div class="main-content flex-fill p-4">
      <div class="container-fluid"></div>
    </div>
  </div>
</body>
</html>