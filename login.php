<?php
require_once 'db_connect.php';
session_start();

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $message = "Please enter both username and password.";
    } else {
        try {
            // Updated query to also select the username
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful.
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                
                // ADDED THIS LINE: Store the username in the session
                $_SESSION['username'] = $user['username'];
                
                header("Location: index.php");
                exit();
            } else {
                $message = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $message = "Error: Could not log in. " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if ($message) echo "<p class='message'>" . htmlspecialchars($message) . "</p>"; ?>
        <div class="form-container">
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="submit-btn">Login</button>
            </form>
        </div>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>