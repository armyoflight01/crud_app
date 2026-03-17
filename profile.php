<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get current user data
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <h2 class="auth-title">My Profile</h2>
        
        <div class="user-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name'] ?: 'Not provided'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></p>
            <p><strong>Role:</strong> <span style="background: <?php echo $user['role'] == 'admin' ? '#667eea' : '#48bb78'; ?>; color: white; padding: 3px 10px; border-radius: 3px;"><?php echo $user['role']; ?></span></p>
            <p><strong>Member since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'])); ?></p>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
            <a href="dashboard.php" class="btn" style="background: #ccc;">Back to Dashboard</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>