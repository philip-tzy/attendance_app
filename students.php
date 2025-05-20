<?php
require_once "config.php";
session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
                $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                
                $sql = "INSERT INTO students (first_name, last_name, email) VALUES ('$first_name', '$last_name', '$email')";
                mysqli_query($conn, $sql);
                break;
                
            case 'update':
                $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
                $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
                $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                
                $sql = "UPDATE students SET first_name='$first_name', last_name='$last_name', email='$email' WHERE student_id=$student_id";
                mysqli_query($conn, $sql);
                break;
                
            case 'delete':
                $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
                $sql = "DELETE FROM students WHERE student_id=$student_id";
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
    <title>Students Management - Attendance System</title>
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
                            <a class="nav-link active" href="students.php">
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Students Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fas fa-plus"></i> Add New Student
                    </button>
                </div>

                <!-- Students Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM students ORDER BY student_id DESC";
                                    $result = mysqli_query($conn, $sql);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['student_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                        echo "<td>
                                                <button class='btn btn-sm btn-primary edit-btn' 
                                                        data-id='" . $row['student_id'] . "'
                                                        data-first='" . htmlspecialchars($row['first_name']) . "'
                                                        data-last='" . htmlspecialchars($row['last_name']) . "'
                                                        data-email='" . htmlspecialchars($row['email']) . "'
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#editStudentModal'>
                                                    <i class='fas fa-edit'></i>
                                                </button>
                                                <button class='btn btn-sm btn-danger delete-btn'
                                                        data-id='" . $row['student_id'] . "'
                                                        data-bs-toggle='modal'
                                                        data-bs-target='#deleteStudentModal'>
                                                    <i class='fas fa-trash'></i>
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

    <!-- Add Student Modal -->
    <div class="modal fade" id="addStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Student Modal -->
    <div class="modal fade" id="editStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="student_id" id="edit_student_id">
                        <div class="mb-3">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="edit_first_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="edit_last_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
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

    <!-- Delete Student Modal -->
    <div class="modal fade" id="deleteStudentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="student_id" id="delete_student_id">
                        <p>Are you sure you want to delete this student? This action cannot be undone.</p>
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
                document.getElementById('edit_student_id').value = this.dataset.id;
                document.getElementById('edit_first_name').value = this.dataset.first;
                document.getElementById('edit_last_name').value = this.dataset.last;
                document.getElementById('edit_email').value = this.dataset.email;
            });
        });

        // Handle delete button click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('delete_student_id').value = this.dataset.id;
            });
        });
    </script>
</body>
</html> 