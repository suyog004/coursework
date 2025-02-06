<?php
session_start();
include 'config.php';  // Ensure this is pointing to your database connection file
include 'header.php';  // Your site's header

// Check if the user is already logged in (optional)
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");  // Redirect if logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and trim inputs
    $username = trim(htmlspecialchars($_POST['username']));
    $password = trim($_POST['password']);

    // Check if fields are not empty
    if (empty($username) || empty($password)) {
        echo "<p style='color: red;'>Both fields are required!</p>";
    } else {
        try {
            // Prepare SQL query to fetch user details
            $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            // Check if user exists and verify the password
            if ($user && password_verify($password, $user['password'])) {
                // Store user details in session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                echo "<p style='color: green;'>Login successful! Redirecting...</p>";
                header("refresh:2; url=dashboard.php"); // Redirect to dashboard after successful login
                exit();
            } else {
                echo "<p style='color: red;'>Invalid credentials!</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>
<html>
    <head>
    <link rel="stylesheet" type="text/css" href="stt.css">
</head>
<!-- HTML Form for login -->
<div class="form-container">
    <h2>Login</h2>
    <form action="login.php" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
</div>

<?php include 'footer.php'; ?>  <!-- Include footer -->
</html>