<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Get all users
$query = "SELECT * FROM users ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <h1>Manage Users</h1>
        <a href="create.php" class="btn btn-success">Add New User</a>
    </div>
    
    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['full_name'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone'] ?: '-'); ?></td>
                    <td>
                        <span style="background: <?php echo $user['role'] == 'admin' ? '#667eea' : '#48bb78'; ?>; 
                                     color: white; padding: 3px 10px; border-radius: 3px; display: inline-block;">
                            <?php echo $user['role']; ?>
                        </span>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td class="action-links">
                        <a href="view.php?id=<?php echo $user['id']; ?>" class="view-link">View</a>
                        <a href="edit.php?id=<?php echo $user['id']; ?>" class="edit-link">Edit</a>
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="delete.php?id=<?php echo $user['id']; ?>" 
                               class="delete-link" 
                               onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>