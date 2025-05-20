<?php
require_once "config.php";
session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
                $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                $date = mysqli_real_escape_string($conn, $_POST['date']);
                
                $sql = "INSERT INTO attendance (student_id, course_id, status, date) 
                        VALUES ('$student_id', '$course_id', '$status', '$date')";
                mysqli_query($conn, $sql);
                break;
                
            case 'update':
                $attendance_id = mysqli_real_escape_string($conn, $_POST['attendance_id']);
                $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
                $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                $date = mysqli_real_escape_string($conn, $_POST['date']);
                
                $sql = "UPDATE attendance 
                        SET student_id='$student_id', course_id='$course_id', 
                            status='$status', date='$date' 
                        WHERE attendance_id=$attendance_id";
                mysqli_query($conn, $sql);
                break;
                
            case 'delete':
                $attendance_id = mysqli_real_escape_string($conn, $_POST['attendance_id']);
                $sql = "DELETE FROM attendance WHERE attendance_id=$attendance_id";
                mysqli_query($conn, $sql);
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management - Attendance System</title>
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
                            <a class="nav-link active" href="attendance.php">
                                <i class="fas fa-clipboard-check me-2"></i> Attendance
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Attendance Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAttendanceModal">
                        <i class="fas fa-plus"></i> Add New Attendance
                    </button>
                </div>

                <!-- Attendance Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Student</th>
                                        <th>Course</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT a.*, s.first_name, s.last_name, c.course_name 
                                           FROM attendance a 
                                           JOIN students s ON a.student_id = s.student_id 
                                           JOIN courses c ON a.course_id = c.course_id 
                                           ORDER BY a.date DESC, a.attendance_id DESC";
                                    $result = mysqli_query($conn, $sql);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['attendance_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                                        echo "<td>
                                                <button class='btn btn-sm btn-primary edit-btn' 
                                                        data-id='" . $row['attendance_id'] . "'
                                                        data-student='" . $row['student_id'] . "'
                                                        data-course='" . $row['course_id'] . "'
                                                        data-status='" . $row['status'] . "'
                                                        data-date='" . $row['date'] . "'
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#editAttendanceModal'>
                                                    <i class='fas fa-edit'></i>
                                                </button>
                                                <button class='btn btn-sm btn-danger delete-btn'
                                                        data-id='" . $row['attendance_id'] . "'
                                                        data-bs-toggle='modal'
                                                        data-bs-target='#deleteAttendanceModal'>
                                                    <i class='fas fa-trash'></i>
                                                </button>
                                                <button class='btn btn-sm btn-info copy-link-btn'
                                                        data-course='" . $row['course_id'] . "'
                                                        title='Copy Attendance Link'>
                                                    <i class='fas fa-link'></i>
                                                </button>
                                              </td>";
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

    <!-- Add Attendance Modal -->
    <div class="modal fade" id="addAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <select class="form-select" name="student_id" required>
                                <option value="">Select Student</option>
                                <?php
                                $sql = "SELECT * FROM students ORDER BY first_name, last_name";
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['student_id'] . "'>" . 
                                         htmlspecialchars($row['first_name'] . " " . $row['last_name']) . 
                                         "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <select class="form-select" name="course_id" required>
                                <option value="">Select Course</option>
                                <?php
                                $sql = "SELECT * FROM courses ORDER BY course_name";
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['course_id'] . "'>" . 
                                         htmlspecialchars($row['course_name']) . 
                                         "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Attendance</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Attendance Modal -->
    <div class="modal fade" id="editAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="attendance_id" id="edit_attendance_id">
                        <div class="mb-3">
                            <label class="form-label">Student</label>
                            <select class="form-select" name="student_id" id="edit_student_id" required>
                                <?php
                                $sql = "SELECT * FROM students ORDER BY first_name, last_name";
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['student_id'] . "'>" . 
                                         htmlspecialchars($row['first_name'] . " " . $row['last_name']) . 
                                         "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course</label>
                            <select class="form-select" name="course_id" id="edit_course_id" required>
                                <?php
                                $sql = "SELECT * FROM courses ORDER BY course_name";
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value='" . $row['course_id'] . "'>" . 
                                         htmlspecialchars($row['course_name']) . 
                                         "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="edit_status" required>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date" id="edit_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Attendance Modal -->
    <div class="modal fade" id="deleteAttendanceModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Attendance</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="attendance_id" id="delete_attendance_id">
                        <p>Are you sure you want to delete this attendance record? This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle edit button click
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_attendance_id').value = this.dataset.id;
                document.getElementById('edit_student_id').value = this.dataset.student;
                document.getElementById('edit_course_id').value = this.dataset.course;
                document.getElementById('edit_status').value = this.dataset.status;
                document.getElementById('edit_date').value = this.dataset.date;
            });
        });

        // Handle delete button click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('delete_attendance_id').value = this.dataset.id;
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Copy link functionality
            const copyButtons = document.querySelectorAll('.copy-link-btn');
            copyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const courseId = this.getAttribute('data-course');
                    const attendanceLink = window.location.origin + 
                        window.location.pathname.replace('attendance.php', '') + 
                        'student_attendance.php?course_id=' + courseId;
                    
                    navigator.clipboard.writeText(attendanceLink).then(() => {
                        alert('Attendance link copied to clipboard!');
                    }).catch(err => {
                        console.error('Failed to copy link: ', err);
                        alert('Failed to copy link. Please try again.');
                    });
                });
            });
        });
    </script>
</body>
</html> 