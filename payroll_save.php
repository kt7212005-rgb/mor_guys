<?php
require 'config.php'; // database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get all input values
    $employee_id       = $_POST['employee_id'];
    $name              = $_POST['name'];
    $position          = $_POST['position'];
    $email             = $_POST['email'];
    $monthly_salary    = floatval($_POST['monthly_salary']);
    $sss               = floatval($_POST['sss']);
    $pagibig           = floatval($_POST['pagibig']);
    $philhealth        = floatval($_POST['philhealth']);
    $other_deductions  = floatval($_POST['other_deductions']);
    $allowances        = floatval($_POST['allowances']);
    $pay_date          = $_POST['pay_date'];

    // Auto compute bi-weekly salary (monthly / 2 OR divide by 2.1666)
    $bi_weekly_rate = $monthly_salary / 2;

    // Total deductions
    $total_deductions = $sss + $pagibig + $philhealth + $other_deductions;

    // NET PAY calculation
    $net_pay = ($bi_weekly_rate + $allowances) - $total_deductions;

    // Insert query
    $sql = "INSERT INTO payroll 
        (employee_id, name, position, email, monthly_salary, bi_weekly_rate, 
         sss, pagibig, philhealth, other_deductions, allowances, pay_date, net_pay)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssdddddddssd",
        $employee_id, 
        $name, 
        $position, 
        $email,
        $monthly_salary,
        $bi_weekly_rate,
        $sss, 
        $pagibig, 
        $philhealth,
        $other_deductions,
        $allowances,
        $pay_date,
        $net_pay
    );

    if ($stmt->execute()) {
        header("Location: index.php?success=1");
        exit();
    } else {
        echo "Error saving payroll: " . $conn->error;
    }
}
?>
