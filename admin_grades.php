<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin'])) {
    header("Location: admin_login.php");
    exit();
}

$status = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reg = trim($_POST['student_reg']);
    $course = $_POST['course_id'];
    $sem = $_POST['semester']; // Now comes from a dropdown
    $grade = strtoupper(trim($_POST['grade']));

    $stmt = $pdo->prepare("INSERT INTO grades (student_reg, course_id, semester, grade) VALUES (?, ?, ?, ?)");
    
    try {
        $stmt->execute([$reg, $course, $sem, $grade]);
        $status = "success";
    } catch (PDOException $e) {
        $status = "error";
        $error_msg = $e->getMessage();
    }
}

$students = $pdo->query("SELECT reg_no, name FROM students")->fetchAll();
$courses = $pdo->query("SELECT course_id, course_name FROM courses")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grade Entry - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>📝 Input Student Grades</h2>
        <a href="admin_dashboard.php">Dashboard</a>
    </div>

    <form method="POST">
        <label>Select Student</label>
        <select name="student_reg" required>
            <?php foreach ($students as $s): ?>
                <option value="<?= htmlspecialchars($s['reg_no']) ?>"><?= htmlspecialchars($s['reg_no']) ?> - <?= htmlspecialchars($s['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Select Course</label>
        <select name="course_id" required>
            <?php foreach ($courses as $c): ?>
                <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['course_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Semester (Must match student view)</label>
        <select name="semester" required>
            <option value="Jan-Apr 2026">Jan-Apr 2026</option>
            <option value="Sept-Dec 2025">Sept-Dec 2025</option>
        </select>

        <label>Grade (A, B, C, D, E, F)</label>
        <input type="text" name="grade" placeholder="e.g. A" maxlength="2" required>

        <button type="submit" style="width: 100%; background-color: #0056b3;">Save Grade</button>
    </form>

    <?php if ($status == "success"): ?>
    <script>Swal.fire('Success', 'Grade recorded successfully!', 'success');</script>
    <?php elseif ($status == "error"): ?>
    <script>Swal.fire('Error', '<?= $error_msg ?>', 'error');</script>
    <?php endif; ?>
</body>
</html>