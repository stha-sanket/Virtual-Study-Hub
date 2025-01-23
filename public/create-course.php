<?php
session_start();
include('../includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Insert the course into the database
    $stmt = $pdo->prepare("INSERT INTO courses (title, description, teacher_id) VALUES (:title, :description, :teacher_id)");
    $stmt->execute(['title' => $title, 'description' => $description, 'teacher_id' => $_SESSION['user_id']]);

    // Update the user's role to teacher
    $stmt = $pdo->prepare("UPDATE users SET role = 'teacher' WHERE id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $_SESSION['role'] = 'teacher';

    header('Location: dashboard.php');
}
?>

<form method="POST">
    <input type="text" name="title" placeholder="Course Title" required>
    <textarea name="description" placeholder="Course Description" required></textarea>
    <button type="submit">Create Course</button>
</form>