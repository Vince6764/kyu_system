<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE reg_no = ?");
    $stmt->execute([$_POST['reg_no']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['student_reg'] = $user['reg_no'];
        $_SESSION['student_name'] = $user['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Registration Number or Password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | KYU</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Eye Toggle Styling */
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }
        .password-wrapper input {
            width: 100%;
            padding-right: 40px; /* Space for the icon */
        }
        .toggle-btn {
            position: absolute;
            right: 10px;
            cursor: pointer;
            user-select: none;
            font-size: 1.1rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2>Student Login</h2>
        <p class="subtitle">Enter your credentials to access grades</p>

        <?php if(isset($error)): ?>
            <p style="color: #d9534f; font-size: 0.8rem; text-align: center;"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="reg_no" placeholder="Registration Number" required>
            
            <div class="password-wrapper">
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span class="toggle-btn" onclick="togglePassword()">👁️</span>
            </div>
            
            <button type="submit">Login</button>
        </form>

        <div class="link-text">
            New student? <a href="register.php">Create an account</a>
            <br><br>
            <a href="admin_login.php" style="color: #666; font-weight: normal;">Staff Portal</a>
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
</body>
</html>