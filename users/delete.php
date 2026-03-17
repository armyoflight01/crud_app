<?php
require_once '../config/database.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get user ID from URL
$id = isset($_GET['id']) ? $_GET['id'] : die('User ID not specified');

// Prevent deleting yourself
if ($id == $_SESSION['user_id']) {
    header('Location: index.php?msg=You cannot delete your own account');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Delete user
$query = "DELETE FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);

if ($stmt->execute()) {
    header('Location: index.php?msg=User deleted successfully');
} else {
    header('Location: index.php?msg=Failed to delete user');
}
exit();
?>