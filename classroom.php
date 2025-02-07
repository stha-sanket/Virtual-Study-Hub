<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Check if classroom ID is provided in the URL
if (!isset($_GET['classroom_id'])) {
    die("Classroom ID is missing.");
}
$classroom_id = intval($_GET['classroom_id']); // Convert to integer for security

// Secure database query to get classroom details
$stmt = $conn->prepare("SELECT * FROM classrooms WHERE id = ?");
$stmt->bind_param("i", $classroom_id);
$stmt->execute();
$classroom_result = $stmt->get_result();

if ($classroom_result->num_rows == 0) {
    die("Classroom not found.");
}

$classroom = $classroom_result->fetch_assoc();

// Fetch classroom owner name
$owner_query = "SELECT username FROM users WHERE id = " . $classroom['owner_id'];
$owner_result = $conn->query($owner_query);
$owner_name = $owner_result->fetch_assoc()['username'];

// Fetch classroom threads
$threads_query = "SELECT threads.*, users.username AS thread_username FROM classroom_threads AS threads 
                  JOIN users ON threads.created_by = users.id 
                  WHERE classroom_id = '$classroom_id' ORDER BY threads.created_at DESC";
$threads_result = $conn->query($threads_query);

// Handle adding a comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['thread_id']) && isset($_POST['comment'])) {
    $thread_id = intval($_POST['thread_id']);
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id']; // Get logged-in user ID

    // Secure the input
    $comment = $conn->real_escape_string($comment);

    // Insert the comment into the database
    $insert_comment = "INSERT INTO classroom_comments (classroom_id, thread_id, comment, user_id) 
                        VALUES ('$classroom_id', '$thread_id', '$comment', '$user_id')";
    if ($conn->query($insert_comment)) {
        header("Location: classroom.php?classroom_id=$classroom_id"); // Redirect to avoid form resubmission
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

// Handle creating a new thread
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['title']) && isset($_POST['description'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    // Secure the input
    $title = $conn->real_escape_string($title);
    $description = $conn->real_escape_string($description);

    // Insert the new thread into the database
    $insert_thread = "INSERT INTO classroom_threads (classroom_id, title, description, created_by) 
                      VALUES ('$classroom_id', '$title', '$description', '$created_by')";
    if ($conn->query($insert_thread)) {
        header("Location: classroom.php?classroom_id=$classroom_id"); // Redirect to avoid form resubmission
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?php echo htmlspecialchars($classroom['name']); ?> - Classroom</title>
</head>
<body>

<h2><?php echo htmlspecialchars($classroom['name']); ?> (<?php echo htmlspecialchars($classroom['subject']); ?>)</h2>
<p>Welcome to the classroom!</p>

<!-- Display classroom ID -->
<p>Classroom ID: <?php echo htmlspecialchars($classroom['id']); ?></p>

<!-- Display classroom owner -->
<p>Classroom Owner: <?php echo htmlspecialchars($owner_name); ?></p>

<!-- Display classroom passcode (For Admin View Only) -->
<?php if ($_SESSION['user_id'] == $classroom['owner_id']) { ?>
    <p>Classroom Passcode: <?php echo htmlspecialchars($classroom['passcode']); ?></p>
<?php } ?>
<a href="assignments.php?id=<?php echo $classroom['id']; ?>">Assignments</a> |
<a href="resources.php?classroom_id=<?php echo $classroom_id; ?>">Resources</a>

<h3>Create New Thread</h3>
<form method="POST" action="classroom.php?classroom_id=<?php echo $classroom_id; ?>">
    <label for="title">Title:</label><br>
    <input type="text" name="title" required><br><br>
    <label for="description">Description:</label><br>
    <textarea name="description" rows="4" required></textarea><br><br>
    <button type="submit">Create Thread</button>
</form>

<h3>Threads</h3>
<?php while ($thread = $threads_result->fetch_assoc()) { ?>
    <div style="border:1px solid black; margin-bottom: 10px; padding: 10px;">
        <h4><?php echo htmlspecialchars($thread['title']); ?></h4>
        <p><?php echo nl2br(htmlspecialchars($thread['description'])); ?></p>

        <!-- Display thread "Posted by" and timestamp -->
        <p><small>Posted by <?php echo htmlspecialchars($thread['thread_username']); ?> on <?php echo htmlspecialchars($thread['created_at']); ?></small></p>

        <!-- Display existing comments for this thread -->
        <?php
        $comments_query = "SELECT comments.*, users.username FROM classroom_comments AS comments 
                           JOIN users ON comments.user_id = users.id
                           WHERE thread_id = " . $thread['id'] . " ORDER BY comments.created_at DESC";
        $comments_result = $conn->query($comments_query);
        ?>
        <h5>Comments:</h5>
        <div>
            <?php while ($comment = $comments_result->fetch_assoc()) { ?>
                <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                <p><small>Posted by <?php echo htmlspecialchars($comment['username']); ?> on <?php echo htmlspecialchars($comment['created_at']); ?></small></p>
                <hr>
            <?php } ?>
        </div>

        <!-- Comment form -->
        <form method="POST" action="classroom.php?classroom_id=<?php echo $classroom_id; ?>">
            <input type="hidden" name="thread_id" value="<?php echo $thread['id']; ?>">
            <label for="comment">Your Comment:</label><br>
            <textarea name="comment" rows="4" required></textarea><br><br>
            <button type="submit">Post Comment</button>
        </form>
    </div>
<?php } ?>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
