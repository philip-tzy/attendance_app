<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'attendance_db');

// Attempt to connect to MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql)) {
    mysqli_select_db($conn, DB_NAME);
} else {
    die("Error creating database: " . mysqli_error($conn));
}

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS students (
        student_id INT PRIMARY KEY AUTO_INCREMENT,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS courses (
        course_id INT PRIMARY KEY AUTO_INCREMENT,
        course_name VARCHAR(100) NOT NULL,
        course_code VARCHAR(20) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS attendance (
        attendance_id INT PRIMARY KEY AUTO_INCREMENT,
        student_id INT NOT NULL,
        course_id INT NOT NULL,
        status ENUM('present', 'absent', 'late') NOT NULL,
        date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE
    )"
];

foreach ($tables as $sql) {
    if (!mysqli_query($conn, $sql)) {
        die("Error creating table: " . mysqli_error($conn));
    }
}
?> 