<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get user ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : die('User ID not specified');

// Fetch user data
$query = "SELECT * FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: index.php?msg=User not found');
    exit();
}

include '../includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <h2 class="auth-title">User Details</h2>
        
        <div class="user-info">
            <p><strong>ID:</strong> <?php echo $user['id']; ?></p>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars($user['full_name'] ?: 'Not provided'); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($user['address'] ?: 'Not provided'); ?></p>
            <p><strong>Role:</strong> <span style="background: <?php echo $user['role'] == 'admin' ? '#667eea' : '#48bb78'; ?>; color: white; padding: 3px 10px; border-radius: 3px;"><?php echo $user['role']; ?></span></p>
            <p><strong>Created:</strong> <?php echo date('F d, Y H:i:s', strtotime($user['created_at'])); ?></p>
            <p><strong>Last Updated:</strong> <?php echo date('F d, Y H:i:s', strtotime($user['updated_at'])); ?></p>
        </div>
        
        <div style="text-align: center; margin-top: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
            <a href="edit.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit User</a>
            <a href="index.php" class="btn" style="background: #ccc;">Back to List</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>