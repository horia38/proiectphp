<?php
// admin_update.php
require_once 'includes/session.php';
require_once 'config/db.php';
require_once 'includes/functions.php';

if (!isAdmin()) {
    // Not an admin, redirect away
    redirect('index.php');
}

$user_id = $_GET['user_id'] ?? null;

// Fetch user info to pre-fill
if (!$user_id) {
    redirect('index.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username'] ?? '');
    $newPassword = trim($_POST['password'] ?? '');

    // Build an update query dynamically
    $updateFields = [];
    $params = ['user_id' => $user_id];

    if ($newUsername !== '') {
        $updateFields[] = "username = :username";
        $params['username'] = $newUsername;
    }
    if ($newPassword !== '') {
        $updateFields[] = "password_hash = :password_hash";
        $params['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
    }

    if (!empty($updateFields)) {
        $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    }

    redirect('index.php');
}

// Get current user data
$sql = "SELECT id, username FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    redirect('index.php');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Update</title>
</head>
<body>
    <h1>Edit User (ID: <?php echo $user['id']; ?>)</h1>
    <form action="admin_update.php?user_id=<?php echo $user['id']; ?>" method="post">
        <label for="username">New Username (leave blank to keep current):</label><br>
        <input type="text" name="username" id="username" value=""><br><br>

        <label for="password">New Password (leave blank to keep current):</label><br>
        <input type="password" name="password" id="password" value=""><br><br>

        <button type="submit">Update</button>
    </form>

    <p><a href="index.php">Back to User List</a></p>
</body>
</html>
