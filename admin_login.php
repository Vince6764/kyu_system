<?php
session_start();
require 'db.php';

// Redirect if already logged in
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize and capture input
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        try {
            // 2. Securely fetch the admin user
            $stmt = $pdo->prepare("SELECT username, password_hash FROM admins WHERE username = ? LIMIT 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // 3. Verify existence and password hash
            if ($admin && password_verify($password, $admin['password_hash'])) {
                // Regenerate session ID to prevent session fixation attacks
                session_regenerate_id(true);
                
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_user'] = $admin['username'];
                
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "A system error occurred. Please try again later.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login | Kirinyaga University</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { max-width: 400px; margin: 80px auto; }
        .login-container { border: 1px solid #444; padding: 30px; border-radius: 12px; background: #1a1b1c; }
        .error-text { color: #ff4d4d; font-size: 0.9em; text-align: center; margin-bottom: 15px; }
        
        /* Eye Toggle Styling */
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-wrapper input {
            width: 100%;
            padding-right: 45px; /* Space for the icon */
        }
        .toggle-btn {
            position: absolute;
            right: 15px;
            cursor: pointer;
            user-select: none;
            font-size: 1.2em;
            /* Center the icon vertically */
            top: 50%;
            transform: translateY(-90%); 
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2 style="text-align: center; color: #007bff; margin-bottom: 5px;">KYU Staff</h2>
        <p style="text-align: center; color: #888; margin-bottom: 25px;">Administrative Portal</p>

        <?php if($error): ?>
            <div class="error-text"><strong>Error:</strong> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="admin_login.php">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required autofocus>

            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" required>
                <span class="toggle-btn" onclick="togglePassword()">👁️</span>
            </div>

            <button type="submit" style="width: 100%; margin-top: 15px; background-color: #007bff;">Sign In</button>
        </form>
        
        <div style="text-align: center; margin-top: 25px;">
            <a href="login.php" style="font-size: 0.85em; color: #bbb;">Student Login Portal</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const toggleIcon = document.querySelector(".toggle-btn");
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.textContent = "🙈"; 
            } else {
                passwordField.type = "password";
                toggleIcon.textContent = "👁️";
            }
        }
    </script>

    <?php if(isset($_GET['logout'])): ?>
    <script>
        Swal.fire({ title: 'Logged Out', text: 'You have been safely disconnected.', icon: 'info', timer: 2000, showConfirmButton: false });
    </script>
    <?php endif; ?>

</body>
</html>