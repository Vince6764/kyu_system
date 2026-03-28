<?php
session_start();
require 'db.php';

// Security: Verify Admin State without hard-coding names
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$message = "";
$msg_type = "";

// 1. Handle Adding New Registration Number
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_reg'])) {
    $new_reg = strtoupper(trim($_POST['reg_no']));
    if (!empty($new_reg)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO admitted_students (reg_no) VALUES (?)");
            $stmt->execute([$new_reg]);
            $message = "Successfully authorized: $new_reg";
            $msg_type = "success";
        } catch (PDOException $e) {
            $message = "Error: This number is already authorized.";
            $msg_type = "error";
        }
    }
}

// 2. Handle Deletion (Revoke Access)
if (isset($_POST['delete_id'])) {
    $stmt = $pdo->prepare("DELETE FROM admitted_students WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $message = "Access revoked successfully.";
    $msg_type = "success";
}

// 3. Dynamic Data Retrieval
$admissions = $pdo->query("SELECT * FROM admitted_students ORDER BY reg_no ASC")->fetchAll(PDO::FETCH_ASSOC);
$auth_count = count($admissions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admissions Management | Staff</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f4f7f6; display: block; padding: 20px; }
        .manage-container { max-width: 850px; margin: 40px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .badge { background: #0056b3; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; }
        
        .alert { padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; text-align: center; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        .input-bar { display: flex; gap: 10px; margin-bottom: 30px; background: #f9f9f9; padding: 20px; border-radius: 8px; }
        .input-bar input { flex: 1; margin: 0; border: 1px solid #ddd; }
        .input-bar button { width: auto; white-space: nowrap; padding: 0 25px; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; color: #888; font-size: 0.75rem; text-transform: uppercase; padding: 15px; border-bottom: 2px solid #eee; }
        td { padding: 15px; border-bottom: 1px solid #f1f1f1; font-size: 0.95rem; }
        
        .revoke-btn { background: transparent; color: #d9534f; border: 1px solid #d9534f; padding: 5px 12px; border-radius: 4px; cursor: pointer; transition: 0.3s; }
        .revoke-btn:hover { background: #d9534f; color: white; }

        .back-link { display: block; text-align: center; margin-top: 30px; color: #0056b3; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>

    <div class="manage-container">
        <div class="header-flex">
            <div>
                <h2 style="margin:0;">Admissions Control</h2>
                <p style="color:#666; font-size: 0.9rem; margin-top: 5px;">Authorize Student Registration Numbers</p>
            </div>
            <span class="badge"><?= $auth_count ?> Authorized</span>
        </div>

        <?php if($message): ?>
            <div class="alert alert-<?= $msg_type ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="POST" class="input-bar">
            <input type="text" name="reg_no" placeholder="Enter Registration Number (e.g. BIT/001/24)" required>
            <button type="submit" name="add_reg">Authorize Access</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Authorized Registration Number</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($auth_count > 0): ?>
                    <?php foreach ($admissions as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['reg_no']) ?></strong></td>
                        <td style="text-align: right;">
                            <form method="POST" onsubmit="return confirm('Revoke access for this student?');">
                                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="revoke-btn">Revoke</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="2" style="text-align: center; color: #999; padding: 40px;">No students authorized yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="admin_dashboard.php" class="back-link">← Return to Dashboard</a>
    </div>

</body>
</html>