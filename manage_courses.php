<?php
session_start();
require 'db.php';
if (!isset($_SESSION['is_admin'])) { header("Location: admin_login.php"); exit(); }

if (isset($_POST['add'])) {
    $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_name, credit_hours) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['code'], $_POST['name'], $_POST['credits']]);
    $msg = "Course Added!";
}
$courses = $pdo->query("SELECT * FROM courses")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Courses | Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <h2>Manage University Units</h2>
    <form method="POST">
        <input type="text" name="code" placeholder="Unit Code (e.g. BIT2101)" required>
        <input type="text" name="name" placeholder="Unit Name" required>
        <input type="number" name="credits" placeholder="Credits" required>
        <button type="submit" name="add">Add Course</button>
    </form>
    <table>
        <tr><th>Code</th><th>Name</th></tr>
        <?php foreach($courses as $c): ?>
            <tr><td><?= $c['course_code'] ?></td><td><?= $c['course_name'] ?></td></tr>
        <?php endforeach; ?>
    </table>
    <a href="admin_dashboard.php">Back</a>
    <?php if(isset($msg)): ?> <script>Swal.fire('Done!', '<?= $msg ?>', 'success');</script> <?php endif; ?>
</body>
</html>