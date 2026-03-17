<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $role = $_POST['role'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if username or email exists
        $check_query = "SELECT id FROM users WHERE username = :username OR email = :email";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->bindParam(':email', $email);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $query = "INSERT INTO users (username, email, password, full_name, phone, address, role) 
                     VALUES (:username, :email, :password, :full_name, :phone, :address, :role)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':role', $role);
            
            if ($stmt->execute()) {
                header('Location: index.php?msg=User created successfully');
                exit();
            } else {
                $error = 'Failed to create user';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <h2 class="auth-title">Create New User</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username *</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Password *</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="full_name" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3"></textarea>
            </div>
            
            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">Create User</button>
            <a href="index.php" class="btn" style="background: #ccc; width: 100%; margin-top: 10px; text-align: center;">Cancel</a>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>