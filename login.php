<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple authentication (you can modify this to use database)
    if ($username === 'admin' && $password === 'onlypass29') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MOR System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3a2a 0%, #2d5a3d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            background: #1e3a2a;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        /* Logo style */
        .login-header img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 12px;
            border: 3px solid #ffffff55;
        }

        .login-header h2 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .login-header p {
            margin: 8px 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: #1e3a2a;
            box-shadow: 0 0 0 3px rgba(30, 58, 42, 0.1);
            outline: none;
        }

        .btn-login {
            background: #1e3a2a;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-login:hover {
            background: #2d5a3d;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            border: none;
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1px solid #e5e7eb;
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: none;
            border-color: #1e3a2a;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <!-- LOGO INSERTED HERE -->
            <img src="logo.jpg">

            <h2>The Redeemed Citadel Of Education Incorporated</h2>
            <p>User Log In</p>
        </div>
        
        <div class="login-body">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="username" class="form-label">
                        </i>Username
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Enter username" required autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">
                        </i>Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Log In
                </button>
            </form>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('bi-eye');
            toggleIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('bi-eye-slash');
            toggleIcon.classList.add('bi-eye');
        }
    });
</script>

</body>
</html>
