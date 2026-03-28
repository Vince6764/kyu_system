<?php
session_start();
require 'db.php';

if (!isset($_SESSION['student_reg'])) {
    header("Location: login.php");
    exit();
}

$results = [];
$searched = false;

if (isset($_POST['view_grades'])) {
    $searched = true;
    $current_student = $_SESSION['student_reg'];
    $selected_sem = $_POST['semester'];

    // Updated query with TRIM to handle any whitespace issues in the databases
    $query = "SELECT c.course_code, c.course_name, g.grade 
              FROM grades g 
              JOIN courses c ON g.course_id = c.course_id 
              WHERE TRIM(g.student_reg) = TRIM(?) 
              AND g.semester = ?";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([$current_student, $selected_sem]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Grades | KYU</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.css">
</head>
<body>
    <header style="display: flex; justify-content: space-between;">
        <h2>KYU Student Portal</h2>
        <a href="logout.php">Logout</a>
    </header>

    <blockquote>
        <strong>Student:</strong> <?= htmlspecialchars($_SESSION['student_name']) ?><br>
        <strong>Reg No:</strong> <?= htmlspecialchars($_SESSION['student_reg']) ?>
    </blockquote>

    <form method="POST">
        <label>Select Semester</label>
        <select name="semester" required>
            <option value="Jan-Apr 2026">Jan-Apr 2026</option>
            <option value="Sept-Dec 2025">Sept-Dec 2025</option>
        </select>
        <button type="submit" name="view_grades">Show My Grades</button>
    </form>

    <?php if ($searched): ?>
        <?php if (!empty($results)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Unit Code</th>
                        <th>Unit Name</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['course_code']) ?></td>
                            <td><?= htmlspecialchars($row['course_name']) ?></td>
                            <td style="color: #007bff; font-weight: bold;"><?= htmlspecialchars($row['grade']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: orange; border: 1px solid orange; padding: 10px;">
                No grades found for <?= htmlspecialchars($_SESSION['student_reg']) ?> in the selected semester. 
                Contact the Registrar (Alex) if this is an error.
            </p>
        <?php endif; ?>

<?php if (!empty($results)): ?>
    <div style="margin-top: 20px;">
        <a href="print_results.php?semester=<?= urlencode($_POST['semester']) ?>" 
           target="_blank" 
           class="button">
           📄 Download Official Result Slip (PDF)
        </a>
    </div>
<?php endif; ?>

    <?php endif; ?>
</body>
</html>