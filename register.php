<?php
require 'db.php';
$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg = htmlspecialchars(trim($_POST['reg_no']));
    $name = htmlspecialchars(trim($_POST['name']));
    $password = $_POST['password'];

    $passwordPattern = '/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/';

    if (!preg_match($passwordPattern, $password)) {
        $error = "Password must be at least 8 characters, include a Capital, Number, and Special character.";
    } else {
        $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO students (reg_no, name, password_hash) VALUES (?, ?, ?)");
            $stmt->execute([$reg, $name, $hashed_pass]);
            $success = true;
        } catch (PDOException $e) {
            $error = "Registration Number already exists.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register | KYU</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Hidden by default */
        #strength-wrapper {
            display: none; 
            margin-top: -10px;
        }
        
        .strength-container {
            width: 100%;
            height: 5px;
            background-color: #e0e0e0;
            margin-bottom: 5px;
            border-radius: 10px;
            overflow: hidden;
        }
        #strength-bar {
            height: 100%;
            width: 0%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
        .strength-text {
            font-size: 0.7rem;
            margin-bottom: 10px;
            display: block;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <h2>Create Account</h2>
        <p class="subtitle">Join the Kirinyaga University Portal</p>

        <?php if($error): ?>
            <p style="color: #d9534f; font-size: 0.8rem; text-align: center;"><?= $error ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="reg_no" placeholder="Reg No (e.g. CT101/001/24)" required>
            <input type="text" name="name" placeholder="Full Name" required>
            
            <input type="password" name="password" id="password-input" placeholder="Secure Password" required>
            
            <div id="strength-wrapper">
                <div class="strength-container">
                    <div id="strength-bar"></div>
                </div>
                <span id="strength-label" class="strength-text"></span>
            </div>

            <button type="submit">Sign Up</button>
        </form>

        <div class="link-text">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>

    <script>
        const passwordInput = document.getElementById('password-input');
        const strengthWrapper = document.getElementById('strength-wrapper');
        const strengthBar = document.getElementById('strength-bar');
        const strengthLabel = document.getElementById('strength-label');

        passwordInput.addEventListener('input', () => {
            const val = passwordInput.value;
            
            // Trigger visibility
            if (val.length > 0) {
                strengthWrapper.style.display = 'block';
            } else {
                strengthWrapper.style.display = 'none';
            }

            let strength = 0;
            if (val.length >= 8) strength += 25;
            if (/[A-Z]/.test(val)) strength += 25;
            if (/[0-9]/.test(val)) strength += 25;
            if (/[\W_]/.test(val)) strength += 25;

            strengthBar.style.width = strength + '%';

            if (strength <= 25) {
                strengthBar.style.backgroundColor = '#d9534f';
                strengthLabel.textContent = 'Weak';
                strengthLabel.style.color = '#d9534f';
            } else if (strength <= 75) {
                strengthBar.style.backgroundColor = '#f0ad4e';
                strengthLabel.textContent = 'Medium';
                strengthLabel.style.color = '#f0ad4e';
            } else {
                strengthBar.style.backgroundColor = '#5cb85c';
                strengthLabel.textContent = 'Strong';
                strengthLabel.style.color = '#5cb85c';
            }
        });
    </script>

    <?php if($success): ?>
    <script>
        Swal.fire('Success!', 'Account created successfully!', 'success')
        .then(() => { window.location.href='login.php'; });
    </script>
    <?php endif; ?>
</body>
</html>