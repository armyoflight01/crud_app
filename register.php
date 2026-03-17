<?php
require_once 'config/database.php';

$error = '';
$success = '';
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form_data = [
        'username' => trim($_POST['username']),
        'email' => trim($_POST['email']),
        'full_name' => trim($_POST['full_name']),
        'phone' => trim($_POST['phone']),
        'address' => trim($_POST['address'])
    ];
    
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($form_data['username']) || empty($form_data['email']) || empty($password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if username or email exists
        $check_query = "SELECT id FROM users WHERE username = :username OR email = :email";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':username', $form_data['username']);
        $check_stmt->bindParam(':email', $form_data['email']);
        $check_stmt->execute();
        
        if ($check_stmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $query = "INSERT INTO users (username, email, password, full_name, phone, address) 
                     VALUES (:username, :email, :password, :full_name, :phone, :address)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $form_data['username']);
            $stmt->bindParam(':email', $form_data['email']);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':full_name', $form_data['full_name']);
            $stmt->bindParam(':phone', $form_data['phone']);
            $stmt->bindParam(':address', $form_data['address']);
            
            if ($stmt->execute()) {
                header('Location: login.php?registered=1');
                exit();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="auth-container">
    <div class="card">
        <h2 class="auth-title">Create Account</h2>
        <p style="text-align: center; color: #666; margin-bottom: 2rem;">Join us today! It's free and easy.</p>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" class="auth-form">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       class="form-control" 
                       placeholder="Choose a username"
                       value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="Enter your email"
                       value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>"
                       required>
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Create a password (min. 6 characters)"
                       required>
                <small style="color: #666; font-size: 0.8rem; display: block; margin-top: 0.25rem;">
                    Password must be at least 6 characters long
                </small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="form-control" 
                       placeholder="Confirm your password"
                       required>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" 
                       id="full_name" 
                       name="full_name" 
                       class="form-control" 
                       placeholder="Enter your full name"
                       value="<?php echo htmlspecialchars($form_data['full_name'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       class="form-control" 
                       placeholder="Enter your phone number"
                       value="<?php echo htmlspecialchars($form_data['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" 
                          name="address" 
                          class="form-control" 
                          placeholder="Enter your address"><?php echo htmlspecialchars($form_data['address'] ?? ''); ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        
        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>