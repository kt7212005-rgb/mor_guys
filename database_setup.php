<?php
require 'config.php';

try {
    // Drop existing tables if they exist (in reverse order of dependencies)
    $pdo->exec("DROP TABLE IF EXISTS work_minutes");
    $pdo->exec("DROP TABLE IF EXISTS notes");
    $pdo->exec("DROP TABLE IF EXISTS feedbacks");
    $pdo->exec("DROP TABLE IF EXISTS meetings");
    $pdo->exec("DROP TABLE IF EXISTS invoices");
    $pdo->exec("DROP TABLE IF EXISTS student_medical");
    $pdo->exec("DROP TABLE IF EXISTS inventory");
    $pdo->exec("DROP TABLE IF EXISTS employees");
    $pdo->exec("DROP TABLE IF EXISTS students");
    
    
    // Create students table
    $pdo->exec("CREATE TABLE students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_no VARCHAR(50) UNIQUE NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        grade_level VARCHAR(20),
        enrollment_date DATE,
        profile_image VARCHAR(255),
        status ENUM('active', 'inactive', 'graduated') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create student_medical table (with PWD tracking)
    $pdo->exec("CREATE TABLE student_medical (
        id INT AUTO_INCREMENT PRIMARY KEY,
        student_id INT UNIQUE,
        allergies TEXT,
        immunizations TEXT,
        blood_type VARCHAR(10),
        emergency_contact VARCHAR(100),
        emergency_phone VARCHAR(20),
        pwd_status ENUM('yes', 'no') DEFAULT 'no',
        pwd_id VARCHAR(100),
        disability_type TEXT,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
    )");

    // Create employees table
    $pdo->exec("CREATE TABLE employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_no VARCHAR(50) UNIQUE NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        position VARCHAR(100),
        department VARCHAR(100),
        hire_date DATE,
        salary DECIMAL(10,2),
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create invoices table
    $pdo->exec("CREATE TABLE invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        invoice_no VARCHAR(50) UNIQUE NOT NULL,
        student_id INT,
        description TEXT,
        amount DECIMAL(10,2) NOT NULL,
        due_date DATE,
        invoice_date DATE DEFAULT CURRENT_DATE,
        status ENUM('pending', 'paid', 'overdue', 'cancelled') DEFAULT 'pending',
        payment_method VARCHAR(50),
        payment_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
    )");

    // Create inventory table
    $pdo->exec("CREATE TABLE inventory (
        id INT AUTO_INCREMENT PRIMARY KEY,
        item_code VARCHAR(50) UNIQUE NOT NULL,
        item_name VARCHAR(200) NOT NULL,
        description TEXT,
        category VARCHAR(100),
        quantity INT DEFAULT 0,
        unit_price DECIMAL(10,2),
        supplier VARCHAR(100),
        reorder_level INT DEFAULT 10,
        status ENUM('available', 'out_of_stock', 'discontinued') DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Create reports tables (for Reports page)
    // Work Minutes table
    $pdo->exec("CREATE TABLE work_minutes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        employee_id INT,
        date DATE NOT NULL,
        minutes_worked INT DEFAULT 0,
        task_description TEXT,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE SET NULL
    )");

    // Meetings table
    $pdo->exec("CREATE TABLE meetings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        date DATE NOT NULL,
        time TIME,
        location VARCHAR(255),
        attendees TEXT,
        agenda TEXT,
        minutes TEXT,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Notes table
    $pdo->exec("CREATE TABLE notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT,
        category VARCHAR(100),
        created_by INT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES employees(id) ON DELETE SET NULL
    )");

    // Feedbacks and Complaints table
    $pdo->exec("CREATE TABLE feedbacks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        type ENUM('feedback', 'complaint') DEFAULT 'feedback',
        submitted_by VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(20),
        subject VARCHAR(255),
        message TEXT,
        status ENUM('new', 'in_progress', 'resolved', 'closed') DEFAULT 'new',
        response TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Insert sample data if tables are empty
    $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    if ($studentCount == 0) {
        // Sample students
        $students = [
            ['STU001', 'John', 'Smith', '555-0101', '123 Main St, City', 'Grade 10', '2024-01-15'],
            ['STU002', 'Sarah', 'Johnson', '555-0102', '456 Oak Ave, Town', 'Grade 11', '2024-01-16'],
            ['STU003', 'Mike', 'Wilson', '555-0103', '789 Pine Rd, Village', 'Grade 9', '2024-01-17'],
            ['STU004', 'Emily', 'Brown', '555-0104', '321 Elm St, City', 'Grade 12', '2024-01-18'],
            ['STU005', 'David', 'Lee', '555-0105', '654 Maple Dr, Town', 'Grade 10', '2024-01-19']
        ];

        foreach ($students as $student) {
            $stmt = $pdo->prepare("INSERT INTO students (student_no, first_name, last_name, phone, address, grade_level, enrollment_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($student);
        }

        // Sample medical records
        $medical = [
            [1, 'Peanuts, Shellfish', 'MMR, Tetanus, Hepatitis B', 'O+', 'Mary Smith', '555-0111'],
            [2, 'None', 'MMR, Tetanus, Hepatitis B', 'A+', 'Robert Johnson', '555-0112'],
            [3, 'Penicillin', 'MMR, Tetanus, Hepatitis B', 'B+', 'Susan Wilson', '555-0113'],
            [4, 'Latex', 'MMR, Tetanus, Hepatitis B', 'AB+', 'James Brown', '555-0114'],
            [5, 'None', 'MMR, Tetanus, Hepatitis B', 'O-', 'Thomas Lee', '555-0115']
        ];

        foreach ($medical as $med) {
            $stmt = $pdo->prepare("INSERT INTO student_medical (student_id, allergies, immunizations, blood_type, emergency_contact, emergency_phone) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute($med);
        }

        // Update sample student medical records with PWD data
        $pwdStudents = [
            [2, 'yes', 'PWD-2024-001', 'Visual Impairment'],
            [4, 'yes', 'PWD-2024-002', 'Hearing Impairment']
        ];

        foreach ($pwdStudents as $pwd) {
            $stmt = $pdo->prepare("UPDATE student_medical SET pwd_status = ?, pwd_id = ?, disability_type = ? WHERE student_id = ?");
            $stmt->execute([$pwd[1], $pwd[2], $pwd[3], $pwd[0]]);
        }
    }

    $employeeCount = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn();
    if ($employeeCount == 0) {
        // Sample employees
        $employees = [
            ['EMP001', 'Alice', 'Anderson', 'alice.a@school.edu', '555-0201', 'Principal', 'Administration', '2020-08-01', 75000.00],
            ['EMP002', 'Bob', 'Baker', 'bob.b@school.edu', '555-0202', 'Teacher', 'Academics', '2021-06-15', 55000.00],
            ['EMP003', 'Carol', 'Clark', 'carol.c@school.edu', '555-0203', 'Accountant', 'Finance', '2020-09-01', 60000.00],
            ['EMP004', 'Daniel', 'Davis', 'daniel.d@school.edu', '555-0204', 'Librarian', 'Library', '2022-01-10', 45000.00],
            ['EMP005', 'Eva', 'Evans', 'eva.e@school.edu', '555-0205', 'Counselor', 'Student Services', '2021-03-20', 52000.00]
        ];

        foreach ($employees as $employee) {
            $stmt = $pdo->prepare("INSERT INTO employees (employee_no, first_name, last_name, email, phone, position, department, hire_date, salary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($employee);
        }
    }

    $invoiceCount = $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn();
    if ($invoiceCount == 0) {
        // Sample invoices
        $invoices = [
            ['INV001', 1, 'Tuition Fee - Grade 10', 15000.00, '2024-02-01', '2024-01-15', 'paid', 'Bank Transfer', '2024-01-20'],
            ['INV002', 2, 'Tuition Fee - Grade 11', 16000.00, '2024-02-01', '2024-01-16', 'pending', NULL, NULL],
            ['INV003', 3, 'Tuition Fee - Grade 9', 14000.00, '2024-02-01', '2024-01-17', 'paid', 'Cash', '2024-01-22'],
            ['INV004', 4, 'Tuition Fee - Grade 12', 17000.00, '2024-02-01', '2024-01-18', 'pending', NULL, NULL],
            ['INV005', 5, 'Laboratory Fees', 2000.00, '2024-01-30', '2024-01-19', 'paid', 'Bank Transfer', '2024-01-25'],
            ['INV006', 1, 'Books and Supplies', 3500.00, '2024-02-05', '2024-01-20', 'pending', NULL, NULL],
            ['INV007', 3, 'Sports Activity Fee', 1500.00, '2024-02-10', '2024-01-21', 'overdue', NULL, NULL],
            ['INV008', 2, 'Computer Lab Fee', 1000.00, '2024-02-15', '2024-01-22', 'paid', 'Credit Card', '2024-01-28']
        ];

        foreach ($invoices as $invoice) {
            $stmt = $pdo->prepare("INSERT INTO invoices (invoice_no, student_id, description, amount, due_date, invoice_date, status, payment_method, payment_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($invoice);
        }
    }

    $inventoryCount = $pdo->query("SELECT COUNT(*) FROM inventory")->fetchColumn();
    if ($inventoryCount == 0) {
        // Sample inventory items
        $items = [
            ['ITM001', 'Laptop Computer', 'Dell Inspiron 15 for student use', 'Electronics', 25, 35000.00, 'Tech Supplies Inc.', 5],
            ['ITM002', 'Desk Chair', 'Ergonomic office chair', 'Furniture', 50, 2500.00, 'Office Depot', 10],
            ['ITM003', 'Whiteboard', '4x6 feet magnetic whiteboard', 'Supplies', 15, 8000.00, 'School Supplies Co.', 3],
            ['ITM004', 'Textbook - Mathematics', 'Grade 10 Math Textbook', 'Books', 100, 1200.00, 'Educational Publishers', 20],
            ['ITM005', 'Science Lab Kit', 'Complete chemistry set', 'Laboratory', 8, 15000.00, 'Lab Equipment Ltd.', 5],
            ['ITM006', 'Basketball', 'Official size basketball', 'Sports', 20, 1500.00, 'Sports Gear Inc.', 10],
            ['ITM007', 'Printer Paper', 'A4 size ream (500 sheets)', 'Supplies', 200, 350.00, 'Paper Company', 50],
            ['ITM008', 'Projector', 'HD classroom projector', 'Electronics', 5, 45000.00, 'AV Solutions', 2]
        ];

        foreach ($items as $item) {
            $stmt = $pdo->prepare("INSERT INTO inventory (item_code, item_name, description, category, quantity, unit_price, supplier, reorder_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($item);
        }
    }

    // Insert sample data for reports tables
    $workMinutesCount = $pdo->query("SELECT COUNT(*) FROM work_minutes")->fetchColumn();
    if ($workMinutesCount == 0) {
        // Sample work minutes
        $workMinutes = [
            [1, '2024-01-15', 480, 'Attended faculty meeting and prepared lesson plans', 'Productive day'],
            [2, '2024-01-15', 420, 'Graded assignments and updated student records', 'Completed all tasks'],
            [3, '2024-01-16', 360, 'Library organization and cataloging', 'Made good progress'],
            [4, '2024-01-16', 450, 'Student counseling sessions', 'Helped 5 students'],
            [1, '2024-01-17', 480, 'Administrative tasks and budget review', 'Important meeting scheduled']
        ];

        foreach ($workMinutes as $wm) {
            $stmt = $pdo->prepare("INSERT INTO work_minutes (employee_id, date, minutes_worked, task_description, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute($wm);
        }
    }

    $meetingsCount = $pdo->query("SELECT COUNT(*) FROM meetings")->fetchColumn();
    if ($meetingsCount == 0) {
        // Sample meetings
        $meetings = [
            ['Faculty Meeting - January', '2024-01-15', '09:00:00', 'Main Conference Room', 'All Faculty Members', 'Discuss semester plans and curriculum updates', 'Meeting went well, all items discussed', 'Follow up on action items'],
            ['Budget Review Meeting', '2024-01-17', '14:00:00', 'Principal Office', 'Principal, Accountant, Department Heads', 'Review Q1 budget and expenses', 'Budget approved with minor adjustments', 'Submit revised budget by Friday'],
            ['Student Council Meeting', '2024-01-20', '15:30:00', 'Student Lounge', 'Student Council Members, Advisor', 'Plan upcoming school events', 'Events scheduled for next month', 'Distribute event flyers']
        ];

        foreach ($meetings as $m) {
            $stmt = $pdo->prepare("INSERT INTO meetings (title, date, time, location, attendees, agenda, minutes, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($m);
        }
    }

    $notesCount = $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn();
    if ($notesCount == 0) {
        // Sample notes
        $notes = [
            ['Important Reminder', 'Don\'t forget to submit quarterly reports by end of month', 'General', 1],
            ['Curriculum Update', 'New curriculum guidelines will be implemented next semester', 'Academic', 2],
            ['Library Hours', 'Library will be closed on Friday for maintenance', 'Administrative', 3],
            ['Student Event', 'Science fair scheduled for next week, need volunteers', 'Events', 4]
        ];

        foreach ($notes as $n) {
            $stmt = $pdo->prepare("INSERT INTO notes (title, content, category, created_by) VALUES (?, ?, ?, ?)");
            $stmt->execute($n);
        }
    }

    $feedbacksCount = $pdo->query("SELECT COUNT(*) FROM feedbacks")->fetchColumn();
    if ($feedbacksCount == 0) {
        // Sample feedbacks and complaints
        $feedbacks = [
            ['feedback', 'John Parent', 'john.parent@email.com', '555-1001', 'Great teaching quality', 'I am very satisfied with the teaching quality and my child is learning well.', 'resolved', 'Thank you for your feedback!'],
            ['complaint', 'Mary Student', 'mary.student@email.com', '555-1002', 'Library hours too short', 'The library closes too early. Can we extend the hours?', 'in_progress', 'We are reviewing this request.'],
            ['feedback', 'Robert Guardian', 'robert.g@email.com', '555-1003', 'Excellent facilities', 'The school facilities are well-maintained and modern.', 'resolved', 'We appreciate your positive feedback!'],
            ['complaint', 'Sarah Parent', 'sarah.p@email.com', '555-1004', 'Cafeteria food quality', 'The cafeteria food needs improvement in quality and variety.', 'new', NULL]
        ];

        foreach ($feedbacks as $fb) {
            $stmt = $pdo->prepare("INSERT INTO feedbacks (type, submitted_by, email, phone, subject, message, status, response) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute($fb);
        }
    }

    echo "Database setup completed successfully!<br>";
    echo "Tables created and sample data inserted.<br>";
    
    // Display counts
    echo "<br>Current database contents:<br>";
    echo "Students: " . $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn() . "<br>";
    echo "Employees: " . $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn() . "<br>";
    echo "Invoices: " . $pdo->query("SELECT COUNT(*) FROM invoices")->fetchColumn() . "<br>";
    echo "Inventory Items: " . $pdo->query("SELECT COUNT(*) FROM inventory")->fetchColumn() . "<br>";
    echo "Work Minutes: " . $pdo->query("SELECT COUNT(*) FROM work_minutes")->fetchColumn() . "<br>";
    echo "Meetings: " . $pdo->query("SELECT COUNT(*) FROM meetings")->fetchColumn() . "<br>";
    echo "Notes: " . $pdo->query("SELECT COUNT(*) FROM notes")->fetchColumn() . "<br>";
    echo "Feedbacks: " . $pdo->query("SELECT COUNT(*) FROM feedbacks")->fetchColumn() . "<br>";
    echo "Students with PWD Status: " . $pdo->query("SELECT COUNT(*) FROM student_medical WHERE pwd_status = 'yes'")->fetchColumn() . "<br>";

} catch(PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
