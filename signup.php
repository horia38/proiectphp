<?php
// signup.php
require_once 'includes/session.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        // Check if username already exists
        $checkSql = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $pdo->prepare($checkSql);
        $stmt->execute(['username' => $username]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "Username already taken.";
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert user (role defaults to 'user')
            $insertSql = "INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)";
            $stmt = $pdo->prepare($insertSql);
            $stmt->execute([
                'username' => $username,
                'password_hash' => $passwordHash
            ]);

            // Redirect to login or auto-login
            redirect('login.php');
        }
    } else {
        $error = "Please fill in both username and password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
</head>
<body>
    <h1>Create an Account</h1>
    <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form action="signup.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <input type="submit" value="Sign Up">
    </form>
    <p><a href="login.php">Already have an account? Login here</a></p>
</body>
</html>
