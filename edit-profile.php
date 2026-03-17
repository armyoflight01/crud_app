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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Update basic info
    if (!empty($current_password) || !empty($new_password)) {
        // Verify current password
        if (empty($current_password)) {
            $error = 'Please enter your current password';
        } elseif (!password_verify($current_password, $user['password'])) {
            $error = 'Current password is incorrect';
        } elseif ($new_password !== $confirm_password) {
            $error = 'New passwords do not match';
        } elseif (strlen($new_password) < 6) {
            $error = 'New password must be at least 6 characters';
        } else {
            // Update with new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET full_name = :full_name, phone = :phone, address = :address, password = :password WHERE id = :id";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bindParam(':password', $hashed_password);
        }
    } else {
        // Update without password
        $update_query = "UPDATE users SET full_name = :full_name, phone = :phone, address = :address WHERE id = :id";
        $update_stmt = $db->prepare($update_query);
    }
    
    if (empty($error) && isset($update_stmt)) {
        $update_stmt->bindParam(':full_name', $full_name);
        $update_stmt->bindParam(':phone', $phone);
        $update_stmt->bindParam(':address', $address);
        $update_stmt->bindParam(':id', $_SESSION['user_id']);
        
        if ($update_stmt->execute()) {
            $_SESSION['full_name'] = $full_name;
            $success = 'Profile updated successfully';
            
            // Refresh user data
            $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $_SESSION['user_id']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Failed to update profile';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <h2 class="auth-title">Edit Profile</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>">
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>">
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($user['address']); ?></textarea>
            </div>
            
            <h3 style="margin: 20px 0 10px;">Change Password (Optional)</h3>
            
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control">
            </div>
            
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Profile</button>
            <a href="profile.php" class="btn" style="background: #ccc; width: 100%; margin-top: 10px; text-align: center;">Cancel</a>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>