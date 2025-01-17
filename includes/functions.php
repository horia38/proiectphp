<?php
// Redirects to a specified URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Checks if a user is logged in
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Checks if the logged-in user is an admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Validates user credentials (new function added for rate limiting)
function validate_user($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        return true;
    }
    return false;
}
?>
