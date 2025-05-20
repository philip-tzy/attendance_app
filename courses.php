<?php
require_once "config.php";
session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
                $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
                
                $sql = "INSERT INTO courses (course_name, course_code) VALUES ('$course_name', '$course_code')";
                mysqli_query($conn, $sql);
                break;
                
            case 'update':
                $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
                $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
                $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
                
                $sql = "UPDATE courses SET course_name='$course_name', course_code='$course_code' WHERE course_id=$course_id";
                mysqli_query($conn, $sql);
                break;
                
            case 'delete':
                $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
                $sql = "DELETE FROM courses WHERE course_id=$course_id";
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
    <title>Courses Management - Attendance System</title>
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
                    <h2>Courses Management</h2>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="fas fa-plus"></i> Add New Course
                    </button>
                </div>

                <!-- Courses Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Course Name</th>
                                        <th>Course Code</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM courses ORDER BY course_id DESC";
                                    $result = mysqli_query($conn, $sql);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['course_id']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['course_code']) . "</td>";
                                        echo "<td>
                                                <a href='course_detail.php?id=" . $row['course_id'] . "' class='btn btn-sm btn-info me-1'>
                                                    <i class='fas fa-eye'></i>
                                                </a>
                                                <button class='btn btn-sm btn-primary edit-btn' 
                                                        data-id='" . $row['course_id'] . "'
                                                        data-name='" . htmlspecialchars($row['course_name']) . "'
                                                        data-code='" . htmlspecialchars($row['course_code']) . "'
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#editCourseModal'>
                                                    <i class='fas fa-edit'></i>
                                                </button>
                                                <button class='btn btn-sm btn-danger delete-btn'
                                                        data-id='" . $row['course_id'] . "'
                                                        data-bs-toggle='modal'
                                                        data-bs-target='#deleteCourseModal'>
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

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" name="course_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" class="form-control" name="course_code" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="course_id" id="edit_course_id">
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" class="form-control" name="course_name" id="edit_course_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" class="form-control" name="course_code" id="edit_course_code" required>
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

    <!-- Delete Course Modal -->
    <div class="modal fade" id="deleteCourseModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="course_id" id="delete_course_id">
                        <p>Are you sure you want to delete this course? This action cannot be undone.</p>
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
                document.getElementById('edit_course_id').value = this.dataset.id;
                document.getElementById('edit_course_name').value = this.dataset.name;
                document.getElementById('edit_course_code').value = this.dataset.code;
            });
        });

        // Handle delete button click
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('delete_course_id').value = this.dataset.id;
            });
        });
    </script>
</body>
</html> 