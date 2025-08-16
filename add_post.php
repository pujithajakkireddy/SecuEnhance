<?php
// This file handles the database connection.
require_once 'db_connect.php';

session_start();

// 1. IMPLEMENT ROLE-BASED ACCESS CONTROL
// Check if the user is NOT logged in OR if their role is NOT allowed to post.
// We'll assume only 'admin' and 'editor' roles can add posts.
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'editor')) {
    // Redirect to a login page or an access denied page.
    header("Location: login.php"); // or header("Location: access_denied.php");
    exit();
}

$message = ''; // Initialize the message variable

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 2. Correctly Collect and Sanitize Form Data
    $title = htmlspecialchars(trim($_POST['title']));
    // The input name in HTML and the database column name should be 'content'.
    $content = htmlspecialchars(trim($_POST['content']));

    // Simple validation
    if (empty($title) || empty($content)) {
        $message = "Please fill in both the title and content fields.";
    } else {
        try {
            // 3. Update the SQL query to use the correct 'content' column name.
            // 4. Add the 'user_id' to the INSERT statement to link the post to the author.
            $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id) VALUES (?, ?, ?)");
            
            // Execute the statement with parameters passed as an array
            $stmt->execute([
                $title, 
                $content, 
                $_SESSION['user_id'] // Get the user_id from the session
            ]);

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
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Create a New Blog Post</h1>
        <p>You are logged in as: **<?php echo htmlspecialchars($_SESSION['username']); ?>**</p>

        <?php if ($message) echo '<p class="message">' . htmlspecialchars($message) . '</p>'; ?>

        <div class="form-container">
            <form action="add_post.php" method="POST">
                <div class="form-group">
                    <label for="title">Post Title:</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="content">Post Content:</label>
                    <textarea id="content" name="content" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Submit Post</button>
            </form>
        </div>

        <a href="index.php" class="back-link">&laquo; Back to all posts</a>

    </div>

</body>
</html>