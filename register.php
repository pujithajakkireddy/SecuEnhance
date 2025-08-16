<?php
// We'll use the $pdo variable from db_connect.php as per the previous recommendation.
require_once 'db_connect.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Enhanced Server-Side Validation and Sanitization
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Create an array to collect errors
    $errors = [];

    // Check for empty fields
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $errors[] = "Please fill in all fields.";
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Validate username format (only letters, numbers, and underscores)
    if (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores.";
    }

    // Validate password length
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // If there are no validation errors, proceed with database operations
    if (empty($errors)) {
        try {
            // 2. Use the correct PDO variable ($pdo) from your db_connect.php file.
            // Check if the username already exists using a prepared statement.
            $check_stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $check_stmt->execute([$username]);

            if ($check_stmt->rowCount() > 0) {
                $message = "Username is already taken. Please choose another.";
            } else {
                // Hash the password for secure storage
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 3. Update the INSERT statement to include the 'role' column.
                // Every new user gets the default 'member' role.
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'member')");
                $stmt->execute([$username, $hashed_password]);

                header("Location: login.php?registered=true");
                exit();
            }
        } catch (PDOException $e) {
            $message = "Error: Could not register user. " . $e->getMessage();
            // In a production environment, you would log the error instead of displaying it.
        }
    } else {
        // If there are errors, join them into a single message
        $message = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        <?php if ($message) echo "<p class='message'>" . htmlspecialchars($message) . "</p>"; ?>
        <div class="form-container">
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required minlength="3" pattern="^[a-zA-Z0-9_]+$" title="Username can only contain letters, numbers, and underscores.">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="submit-btn">Register</button>
            </form>
        </div>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>