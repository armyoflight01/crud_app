<?php
// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Russell Evan User Management System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="index.php" class="logo">Russell Evan User Management System</a>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <?php if ($logged_in): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <?php if ($user_role === 'admin'): ?>
                        <a href="users/index.php">Manage Users</a>
                    <?php endif; ?>
                    <a href="profile.php">Profile</a>
                    <a href="logout.php">Logout (<?php echo htmlspecialchars($username); ?>)</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="main-content">
        <div class="container">