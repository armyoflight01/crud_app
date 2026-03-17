<?php
require_once 'config/database.php';
include 'includes/header.php';
?>

<div class="card" style="max-width: 800px; margin: 0 auto;">
    <h1 class="auth-title">Welcome to User Management System</h1>
    <p style="text-align: center; margin-bottom: 30px;">This is a professional CRUD application with user authentication.</p>
    
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div style="text-align: center;">
            <a href="register.php" class="btn btn-primary">Register Now</a>
            <a href="login.php" class="btn btn-success" style="margin-left: 10px;">Login</a>
        </div>
    <?php else: ?>
        <div style="text-align: center;">
            <h3>Welcome back, <?php echo htmlspecialchars($_SESSION['full_name'] ?: $_SESSION['username']); ?>!</h3>
            <div style="margin-top: 20px;">
                <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                <a href="users/index.php" class="btn btn-success" style="margin-left: 10px;">Manage Users</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>