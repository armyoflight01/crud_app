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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];
    
    // Validation
    if (empty($username) || empty($email)) {
        $error = 'Username and email are required';
    } else {
        // Check if username or email exists for other users
        $check_query = "SELECT id FROM users WHERE (username = :username OR email = :email) AND id != :id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->bindParam(':id', $id);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {
            // Update user
            if (!empty($new_password)) {
                // Update with new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $query = "UPDATE users SET username = :username, email = :email, password = :password, 
                         full_name = :full_name, phone = :phone, address = :address, role = :role 
                         WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':password', $hashed_password);
            } else {
                // Update without password
                $query = "UPDATE users SET username = :username, email = :email, 
                         full_name = :full_name, phone = :phone, address = :address, role = :role 
                         WHERE id = :id";
                $stmt = $db->prepare($query);
            }
            
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                $success = 'User updated successfully';
                // Refresh user data
                $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = 'Failed to update user';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <h2 class="auth-title">Edit User</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" class="form-control" 
                       value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            
            <div class="form-group">
                <label>New Password (leave blank to keep current)</label>
                <input type="password" name="new_password" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" 
                       value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" 
                       value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update User</button>
            <a href="index.php" class="btn" style="background: #ccc; width: 100%; margin-top: 10px; text-align: center;">Cancel</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>