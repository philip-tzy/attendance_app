<?php
require_once "config.php";
require_once "vendor/autoload.php"; // For TCPDF
session_start();

$course_id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';
$course_name = '';
$course_code = '';

if ($course_id) {
    $sql = "SELECT * FROM courses WHERE course_id = '$course_id'";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $course_name = $row['course_name'];
        $course_code = $row['course_code'];
    }
}

// Handle PDF Export
if (isset($_GET['export']) && $_GET['export'] == 'pdf') {
    try {
        // Create new PDF document
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Attendance System');
        $pdf->SetAuthor('Attendance System');
        $pdf->SetTitle('Attendance Report - ' . $course_name);

        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Set margins
        $pdf->SetMargins(15, 15, 15);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', 'B', 16);

        // Title
        $pdf->Cell(0, 10, 'Attendance Report', 0, 1, 'C');
        $pdf->Ln(5);

        // Course Information
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Course Information:', 0, 1);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Course Name: ' . $course_name, 0, 1);
        $pdf->Cell(0, 10, 'Course Code: ' . $course_code, 0, 1);
        $pdf->Ln(5);

        // Table header
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(30, 10, 'Student ID', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Student Name', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Date', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Status', 1, 1, 'C');

        // Table data
        $pdf->SetFont('helvetica', '', 10);
        
        // Get attendance data
        $sql = "SELECT a.*, s.first_name, s.last_name 
                FROM attendance a 
                JOIN students s ON a.student_id = s.student_id 
                WHERE a.course_id = '$course_id' 
                ORDER BY a.date DESC, s.first_name, s.last_name";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $pdf->Cell(30, 10, $row['student_id'], 1, 0, 'C');
            $pdf->Cell(60, 10, $row['first_name'] . ' ' . $row['last_name'], 1, 0, 'L');
            $pdf->Cell(40, 10, $row['date'], 1, 0, 'C');
            $pdf->Cell(40, 10, ucfirst($row['status']), 1, 1, 'C');
        }

        // Output PDF
        $pdf->Output('attendance_report_' . $course_code . '.pdf', 'D');
        exit;
    } catch (Exception $e) {
        die('Error generating PDF: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Detail - <?php echo htmlspecialchars($course_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .nav-link {
            color: rgba(255,255,255,.8);
        }
        .nav-link:hover {
            color: white;
        }
        .main-content {
            padding: 20px;
        }
        @media (max-width: 991.98px) {
            .sidebar {
                display: none !important;
            }
            .main-content {
                padding: 10px;
            }
            .col-md-9.col-lg-10.main-content {
                flex: 0 0 100%;
                max-width: 100%;
            }
            .mobile-nav-toggle {
                display: block !important;
            }
        }
        .mobile-nav-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 2rem;
            color: #343a40;
            margin: 10px 0 10px 10px;
        }
        .offcanvas {
            background: #343a40;
            color: white;
        }
        .offcanvas .nav-link {
            color: rgba(255,255,255,.8);
        }
        .offcanvas .nav-link:hover {
            color: white;
        }
        .table-responsive {
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <!-- Hamburger Button for Mobile -->
    <button class="mobile-nav-toggle d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar" aria-controls="offcanvasSidebar">
        <i class="fas fa-bars"></i>
    </button>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar for Desktop -->
            <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-lg-block">
                <div class="p-3">
                    <h4 class="text-center mb-4">Attendance System</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="students.php">
                                <i class="fas fa-user-graduate me-2"></i> Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="courses.php">
                                <i class="fas fa-book me-2"></i> Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="attendance.php">
                                <i class="fas fa-clipboard-check me-2"></i> Attendance
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Offcanvas Sidebar for Mobile -->
            <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="offcanvasSidebar" aria-labelledby="offcanvasSidebarLabel">
                <div class="offcanvas-header">
                    <h4 class="offcanvas-title" id="offcanvasSidebarLabel">Attendance System</h4>
                    <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-home me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="students.php">
                                <i class="fas fa-user-graduate me-2"></i> Students
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="courses.php">
                                <i class="fas fa-book me-2"></i> Courses
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="attendance.php">
                                <i class="fas fa-clipboard-check me-2"></i> Attendance
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Course Detail: <?php echo htmlspecialchars($course_name); ?></h2>
                    <div>
                        <button class="btn btn-info me-2" id="copyLinkBtn">
                            <i class="fas fa-link"></i> Copy Attendance Link
                        </button>
                        <a href="export_pdf.php?id=<?php echo $course_id; ?>" class="btn btn-primary">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>
                </div>

                <!-- Course Info -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Course Information</h5>
                        <p><strong>Course Code:</strong> <?php echo htmlspecialchars($course_code); ?></p>
                    </div>
                </div>

                <!-- Attendance Table -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Attendance Records</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT a.*, s.first_name, s.last_name 
                                            FROM attendance a 
                                            JOIN students s ON a.student_id = s.student_id 
                                            WHERE a.course_id = '$course_id' 
                                            ORDER BY a.date DESC, s.first_name, s.last_name";
                                    $result = mysqli_query($conn, $sql);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('copyLinkBtn').addEventListener('click', function() {
            const attendanceLink = window.location.origin + 
                window.location.pathname.replace('course_detail.php', '') + 
                'student_attendance.php?course_id=<?php echo $course_id; ?>';
            
            navigator.clipboard.writeText(attendanceLink).then(() => {
                alert('Attendance link copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy link: ', err);
                alert('Failed to copy link. Please try again.');
            });
        });
    </script>
</body>
</html> 