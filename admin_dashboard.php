<?php
session_start();
require 'db.php';

// Security check
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Dynamic Stats (No Hard-coding)
$total_students = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_courses = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$total_grades = $pdo->query("SELECT COUNT(*) FROM grades")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KYU Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { background: #f0f2f5; margin: 0; padding: 0; display: block; }
        .dashboard-container { max-width: 1000px; margin: 50px auto; padding: 20px; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 2px solid #ddd; padding-bottom: 20px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: white; padding: 30px; border-radius: 10px; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .stat-card h1 { font-size: 2.5rem; color: #0056b3; margin: 0; }
        
        .action-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .action-card { 
            background: white; padding: 25px; border-radius: 10px; text-align: center; 
            text-decoration: none; color: inherit; border: 1px solid #eee; transition: 0.3s; 
        }
        .action-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); border-color: #0056b3; }
        .action-card h3 { color: #0056b3; margin-top: 10px; }
        
        .logout-link { color: #d9534f; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="dashboard-container">
    <div class="header">
        <div>
            <h2>KYU Administrative Dashboard</h2>
            <span>Logged in as: <strong><?= htmlspecialchars($_SESSION['admin_user']) ?></strong></span>
        </div>
        <a href="logout.php" class="logout-link">Secure Logout</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h1><?= $total_students ?></h1>
            <p>Total Students</p>
        </div>
        <div class="stat-card">
            <h1><?= $total_courses ?></h1>
            <p>Total Courses</p>
        </div>
        <div class="stat-card">
            <h1><?= $total_grades ?></h1>
            <p>Grades Recorded</p>
        </div>
    </div>

    <h3>Management Actions</h3>
    <div class="action-grid">
        <a href="manage_admisions.php" class="action-card">
            <span style="font-size: 2rem;">👤</span>
            <h3>Manage Admissions</h3>
            <p style="font-size: 0.8rem; color: #666;">Authorize student registration</p>
        </a>

        <a href="manage_courses.php" class="action-card">
            <span style="font-size: 2rem;">📚</span>
            <h3>Manage Courses</h3>
            <p style="font-size: 0.8rem; color: #666;">Add or view university units</p>
        </a>

        <a href="admin_grades.php" class="action-card">
            <span style="font-size: 2rem;">📝</span>
            <h3>Enter Grades</h3>
            <p style="font-size: 0.8rem; color: #666;">Assign scores to students</p>
        </a>
    </div>
</div>

</body>
</html>