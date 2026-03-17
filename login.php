<?php
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM users WHERE username = :username OR email = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Invalid password';
            }
        } else {
            $error = 'User not found';
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <h2 class="auth-title">Welcome Back</h2>
        <p style="text-align: center; color: #666; margin-bottom: 2rem;">Please login to your account</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Registration successful! Please login.</div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label for="username">Username or Email</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-control" 
                       placeholder="Enter your username or email"
                       required 
                       autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Enter your password"
                       required>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
            <p style="margin-top: 0.5rem; font-size: 0.9rem;">
                <a href="#" style="color: #666;">Forgot password?</a>
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>