<?php
session_start();
include('../includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];

    // Insert the enrollment into the database
    $stmt = $pdo->prepare("INSERT INTO enrollments (course_id, user_id) VALUES (:course_id, :user_id)");
    $stmt->execute(['course_id' => $course_id, 'user_id' => $_SESSION['user_id']]);

    header('Location: dashboard.php');
}
?>

<form method="POST">
    <label for="course_id">Course ID:</label>
    <input type="text" name="course_id" required>
    <button type="submit">Join Course</button>
</form>