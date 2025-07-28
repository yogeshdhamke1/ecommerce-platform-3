<?php
require_once '../config/config.php';
require_once '../classes/User.php';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $email = $_POST['email'];
    $reset_token = bin2hex(random_bytes(32));
    
    // Store reset token in database (simplified - in production use proper token storage)
    $query = "UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?";
    $stmt = $db->prepare($query);
    
    if ($stmt->execute([$reset_token, $email])) {
        $success = "Password reset link sent to your email.";
        // In production, send actual email with reset link
    } else {
        $error = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                <a href="../index.php" class="text-2xl font-bold text-blue-600">
                    <i class="fas fa-store mr-2"></i><?php echo SITE_NAME; ?>
                </a>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">Forgot Password</h2>
                <p class="mt-2 text-gray-600">Enter your email to reset password</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-xl p-8">
                <?php if (isset($error)): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                        <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                        <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1"></i>Email Address
                        </label>
                        <input type="email" name="email" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter your email">
                    </div>
                    
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                    </button>
                </form>
                
                <div class="mt-6 text-center">
                    <a href="login.php" class="text-blue-600 hover:text-blue-500">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>