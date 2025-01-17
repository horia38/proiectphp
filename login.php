<?php
// login.php
require_once 'includes/session.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        // Check if IP is blocked due to too many failed attempts
        $stmt = $pdo->prepare("SELECT attempts, last_attempt FROM failed_logins WHERE ip_address = :ip");
        $stmt->execute(['ip' => $ip_address]);
        $failed_login = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($failed_login && $failed_login['attempts'] >= 3) {
            $last_attempt_time = strtotime($failed_login['last_attempt']);
            $current_time = time();

            if ($current_time - $last_attempt_time < 30) { // 5-minute block
                $error = "Too many failed attempts. Please try again later.";
            } else {
                // Reset failed attempts after 5 minutes
                $stmt = $pdo->prepare("DELETE FROM failed_logins WHERE ip_address = :ip");
                $stmt->execute(['ip' => $ip_address]);
            }
        }

        // Proceed if no blocking error exists
        if (!isset($error)) {
            // Validate user credentials
            $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Successful login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Reset failed login attempts for this IP
                $stmt = $pdo->prepare("DELETE FROM failed_logins WHERE ip_address = :ip");
                $stmt->execute(['ip' => $ip_address]);

                redirect('index.php');
            } else {
                // Invalid login: record failed attempt
                if ($failed_login) {
                    $stmt = $pdo->prepare("UPDATE failed_logins SET attempts = attempts + 1, last_attempt = NOW() WHERE ip_address = :ip");
                } else {
                    $stmt = $pdo->prepare("INSERT INTO failed_logins (ip_address, attempts, last_attempt) VALUES (:ip, 1, NOW())");
                }
                $stmt->execute(['ip' => $ip_address]);

                $error = "Invalid username or password.";
            }
        }
    } else {
        $error = "Please fill in both username and password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form action="login.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" name="password" id="password" required><br><br>

        <input type="submit" value="Log In">
    </form>
    <p><a href="signup.php">Don't have an account? Sign up here</a></p>
</body>
</html>
