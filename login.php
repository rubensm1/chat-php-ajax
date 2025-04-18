<?php
// Include session file
require_once 'includes/session.php';

// Redirect if already logged in
redirectIfLoggedIn();

// Initialize variables
$username = '';
$error = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    // Validate form data
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Attempt to login
        $user = loginUser($username, $password);
        
        if ($user) {
            // Set user session
            setUserSession($user);
            
            // Redirect to homepage
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    }
}

// Check for timeout message
$timeout_message = '';
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeout_message = 'Your session has timed out. Please login again.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Chat System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="auth-container">
            <h2>Login to Chat</h2>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if (!empty($timeout_message)): ?>
                <div class="alert alert-danger"><?php echo $timeout_message; ?></div>
            <?php endif; ?>
            
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo $username; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Login</button>
                </div>
            </form>
            
            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</body>
</html>
