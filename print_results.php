<?php
session_start();
require 'db.php';

if (!isset($_SESSION['student_reg'])) {
    header("Location: login.php");
    exit();
}

$semester = $_GET['semester'] ?? '';
$reg_no = $_SESSION['student_reg'];
$student_name = $_SESSION['student_name'];

$stmt = $pdo->prepare("SELECT c.course_code, c.course_name, g.grade 
                       FROM grades g 
                       JOIN courses c ON g.course_id = c.course_id 
                       WHERE g.student_reg = ? AND g.semester = ?");
$stmt->execute([$reg_no, $semester]);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate a verification link for the QR code (pointing to your system)
$verification_url = "http://localhost/kyu_system/verify.php?reg=$reg_no&sem=" . urlencode($semester);
$qr_code_url = "https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=" . urlencode($verification_url) . "&choe=UTF-8";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transcript_<?= htmlspecialchars($reg_no) ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 30px; color: #333; position: relative; }
        
        /* Watermark Styling */
        body::after {
            content: "KIRINYAGA UNIVERSITY";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 5rem;
            color: rgba(0, 0, 0, 0.05);
            white-space: nowrap;
            z-index: -1;
            pointer-events: none;
        }

        .header { text-align: center; border-bottom: 4px double #0056b3; margin-bottom: 30px; padding-bottom: 10px; }
        .header h1 { margin: 0; color: #0056b3; }
        
        .main-content { display: flex; justify-content: space-between; align-items: flex-start; }
        .student-info { line-height: 1.8; }
        .qr-section { text-align: center; border: 1px solid #ddd; padding: 5px; background: #fff; }

        table { width: 100%; border-collapse: collapse; margin-top: 30px; background: transparent; }
        th, td { border: 1px solid #888; padding: 12px; text-align: left; }
        th { background-color: #f8f9fa; color: #0056b3; text-transform: uppercase; font-size: 0.85rem; }
        
        .footer { margin-top: 60px; display: flex; justify-content: space-between; border-top: 1px solid #ccc; padding-top: 20px; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            body::after { color: rgba(0, 0, 0, 0.03); } /* Lighten watermark for ink saving */
        }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom: 30px; text-align: center; background: #f4f4f4; padding: 15px; border-radius: 8px;">
    <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer; background: #0056b3; color: white; border: none; border-radius: 5px; font-weight: bold;">
        Print / Save as PDF
    </button>
    
    <a href="dashboard.php">
        <button type="button" style="padding: 10px 20px; cursor: pointer; border: 1px solid #0056b3; background: white; color: #0056b3; border-radius: 5px; margin-left: 10px;">
            Return to Dashboard
        </button>
    </a>
</div>

    <div class="header">
        <h1>KIRINYAGA UNIVERSITY</h1>
        <p><em>Office of the Registrar (Academic Affairs)</em></p>
        <h2>PROVISIONAL ACADEMIC TRANSCRIPT</h2>
    </div>

    <div class="main-content">
        <div class="student-info">
            <p><strong>NAME:</strong> <?= htmlspecialchars(strtoupper($student_name)) ?></p>
            <p><strong>REGISTRATION NO:</strong> <?= htmlspecialchars($reg_no) ?></p>
            <p><strong>ACADEMIC YEAR:</strong> 2025/2026</p>
            <p><strong>SEMESTER:</strong> <?= htmlspecialchars($semester) ?></p>
        </div>

        <div class="qr-section">
            <img src="<?= $qr_code_url ?>" alt="Verification QR">
            <p style="font-size: 0.7rem; margin: 0;">Scan to Verify</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Course Code</th>
                <th style="width: 60%;">Course Description</th>
                <th style="width: 20%;">Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grades as $g): ?>
            <tr>
                <td><?= htmlspecialchars($g['course_code']) ?></td>
                <td><?= htmlspecialchars($g['course_name']) ?></td>
                <td style="text-align: center;"><strong><?= htmlspecialchars($g['grade']) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        <div>
            <p>Date Generated: <?= date('d/m/Y H:i:s') ?></p>
        </div>
        <div style="text-align: right;">
            <p>__________________________</p>
            <p>University Registrar</p>
        </div>
    </div>

</body>
</html>