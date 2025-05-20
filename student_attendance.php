<?php
require_once "config.php";
session_start();

$message = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $status = 'present'; // Default status for self-attendance
    $date = date('Y-m-d');
    
    // Check if attendance already exists for this student, course, and date
    $check_sql = "SELECT * FROM attendance WHERE student_id = '$student_id' AND course_id = '$course_id' AND date = '$date'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "You have already marked your attendance for today.";
    } else {
        $sql = "INSERT INTO attendance (student_id, course_id, status, date) 
                VALUES ('$student_id', '$course_id', '$status', '$date')";
        if (mysqli_query($conn, $sql)) {
            $message = "Attendance marked successfully!";
        } else {
            $error = "Error marking attendance. Please try again.";
        }
    }
}

// Get course information from URL parameter
$course_id = isset($_GET['course_id']) ? mysqli_real_escape_string($conn, $_GET['course_id']) : '';
$course_name = '';

if ($course_id) {
    $sql = "SELECT course_name FROM courses WHERE course_id = '$course_id'";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        $course_name = $row['course_name'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .attendance-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="attendance-form">
            <h2 class="text-center mb-4">Student Attendance Form</h2>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($course_name): ?>
                <div class="alert alert-info">
                    Course: <?php echo htmlspecialchars($course_name); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course_id); ?>">
                
                <div class="mb-3">
                    <label class="form-label">Student ID</label>
                    <input type="text" class="form-control" name="student_id" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Mark Attendance</button>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 