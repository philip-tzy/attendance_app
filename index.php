<?php
require_once "config.php";
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management System</title>
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
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
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
                            <a class="nav-link" href="courses.php">
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
                <h2 class="mb-4">Dashboard</h2>
                
                <div class="row">
                    <!-- Students Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Students</h5>
                                <?php
                                $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM students");
                                $row = mysqli_fetch_assoc($result);
                                ?>
                                <h2 class="card-text"><?php echo $row['count']; ?></h2>
                                <a href="students.php" class="btn btn-light">View Students</a>
                            </div>
                        </div>
                    </div>

                    <!-- Courses Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Courses</h5>
                                <?php
                                $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM courses");
                                $row = mysqli_fetch_assoc($result);
                                ?>
                                <h2 class="card-text"><?php echo $row['count']; ?></h2>
                                <a href="courses.php" class="btn btn-light">View Courses</a>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Today's Attendance</h5>
                                <?php
                                $today = date('Y-m-d');
                                $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM attendance WHERE date = '$today'");
                                $row = mysqli_fetch_assoc($result);
                                ?>
                                <h2 class="card-text"><?php echo $row['count']; ?></h2>
                                <a href="attendance.php" class="btn btn-light">View Attendance</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Attendance -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Attendance Records</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT a.*, s.first_name, s.last_name, c.course_name 
                                           FROM attendance a 
                                           JOIN students s ON a.student_id = s.student_id 
                                           JOIN courses c ON a.course_id = c.course_id 
                                           ORDER BY a.date DESC LIMIT 5";
                                    $result = mysqli_query($conn, $sql);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
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
</body>
</html> 