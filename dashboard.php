<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get user statistics
$stats = [];
$queries = [
    'total_users' => "SELECT COUNT(*) as count FROM users",
    'new_users_today' => "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = CURDATE()",
    'admins' => "SELECT COUNT(*) as count FROM users WHERE role = 'admin'"
];

foreach ($queries as $key => $query) {
    $stmt = $db->query($query);
    $stats[$key] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
}

// Get current user data
$user_query = "SELECT * FROM users WHERE id = :id";
$user_stmt = $db->prepare($user_query);
$user_stmt->bindParam(':id', $_SESSION['user_id']);
$user_stmt->execute();
$current_user = $user_stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="card">
    <h1 class="auth-title">Dashboard</h1>
    
    <div class="user-info">
        <h3>Welcome, <?php echo htmlspecialchars($current_user['full_name'] ?: $current_user['username']); ?>!</h3>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($current_user['username']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($current_user['email']); ?></p>
        <p><strong>Role:</strong> <span style="background: <?php echo $current_user['role'] == 'admin' ? '#667eea' : '#48bb78'; ?>; color: white; padding: 3px 10px; border-radius: 3px;"><?php echo $current_user['role']; ?></span></p>
        <p><strong>Member since:</strong> <?php echo date('F d, Y', strtotime($current_user['created_at'])); ?></p>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <h3><?php echo $stats['total_users']; ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['new_users_today']; ?></h3>
            <p>New Users Today</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $stats['admins']; ?></h3>
            <p>Administrators</p>
        </div>
    </div>
    
    <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <div style="margin-top: 30px;">
        <h2>Quick Actions</h2>
        <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="users/index.php" class="btn btn-primary">Manage Users</a>
            <a href="users/create.php" class="btn btn-success">Add New User</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>