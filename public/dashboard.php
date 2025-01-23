<?php
include('../includes/db.php');
session_start(); // Ensure session is started

// // Debugging: Check if session is started and user_id is set
// if (!isset($_SESSION['user_id'])) {
//     echo "Session not set. Redirecting to login.";
//     header('Location: login.php');
//     exit();
// } else {
//     echo "Session is set. User ID: " . $_SESSION['user_id'];
// }

// Fetch user classrooms from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM classrooms WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 200px;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #333;
            display: block;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light" style="margin-left: 220px;">
        <a class="navbar-brand" href="#">StudyNest</a>
        <div class="collapse navbar-collapse justify-content-end">
            <button class="btn btn-outline-success mr-2">Join Classroom</button>
            <button class="btn btn-outline-primary mr-2">Create Classroom</button>
            <img src="path/to/user/logo.png" alt="User Logo" class="rounded-circle" width="40" height="40">
        </div>
    </nav>

    <div class="sidebar">
        <a href="#">Dashboard</a>
        <a href="#">Notes</a>
        <a href="#">Flashcard</a>
        <a href="#">Profile</a>
        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="btn btn-link">Logout</button>
        </form>
    </div>

    <div class="main-content">
        <h2>Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
        <h3>Your Classrooms</h3>
        <div class="row">
            <?php if (!empty($classrooms)): ?>
                <?php foreach ($classrooms as $classroom): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="path/to/classroom/image.png" class="card-img-top" alt="Classroom Image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($classroom['name']); ?></h5>
                                <p class="card-text">Created by: <?php echo htmlspecialchars($classroom['creator']); ?></p>
                                <a href="classroom.php?id=<?php echo $classroom['id']; ?>" class="btn btn-primary">Go to Classroom</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No classrooms found.</p>
            <?php endif; ?>
        </div>
        <h3>Assignments</h3>
        <!-- Add assignments content here -->
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>