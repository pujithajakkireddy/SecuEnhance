<?php
// This file handles the database connection.
require_once 'db_connect.php';

// ADD THIS LINE to start the session.
session_start();

// Check if the user is logged in. If not, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = ''; // Initialize the message variable

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $title = htmlspecialchars(trim($_POST['title']));
    $context = htmlspecialchars(trim($_POST['context']));

    // Simple validation
    if (empty($title) || empty($context)) {
        $message = "Please fill in both the title and content fields.";
    } else {
        try {
            // The column name 'content' is changed to 'context' in the SQL query
            $stmt = $conn->prepare("INSERT INTO posts (title, context) VALUES (?, ?)");
            
            // Execute the statement with parameters passed as an array
            $stmt->execute([$title, $context]);

            // Success: redirect back to the main page
            header("Location: index.php");
            exit(); // Always exit after a header redirect
            
        } catch (PDOException $e) {
            // Error handling for database insertion
            $message = "Error: Could not save the post. " . $e->getMessage();
        }
    }
}

// NOTE: We do not close the connection here. PDO connections close automatically at the end of the script.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <!-- Use the main stylesheet for consistent styling -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Create a New Blog Post</h1>
        <p>You are logged in as: <?php echo htmlspecialchars($_SESSION['username']); ?></p>

        <?php if (isset($message)) echo '<p class="message">' . htmlspecialchars($message) . '</p>'; ?>

        <div class="form-container">
            <form action="add_post.php" method="POST">
                <div class="form-group">
                    <label for="title">Post Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="context">Post Content:</label>
                    <textarea id="context" name="context" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Submit Post</button>
            </form>
        </div>

        <a href="index.php" class="back-link">&laquo; Back to all posts</a>

    </div>

</body>
</html>