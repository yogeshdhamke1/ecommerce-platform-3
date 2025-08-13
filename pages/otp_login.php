<?php
require_once '../config/config.php';

$database = new Database();
$db = $database->getConnection();

if ($_POST) {
    if (isset($_POST['send_otp'])) {
        $email = $_POST['email'];
        
        // Check if user exists
        $query = "SELECT id, full_name FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $otp = rand(100000, 999999);
            $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            // Store OTP
            $otp_query = "INSERT INTO user_otps (user_id, otp, expires_at) VALUES (?, ?, ?) 
                         ON DUPLICATE KEY UPDATE otp = ?, expires_at = ?";
            $otp_stmt = $db->prepare($otp_query);
            $otp_stmt->execute([$user['id'], $otp, $expires, $otp, $expires]);
            
            // Send OTP (simulate email)
            $_SESSION['otp_sent'] = true;
            $_SESSION['otp_email'] = $email;
            $_SESSION['debug_otp'] = $otp; // For testing - remove in production
            
            $success = "OTP sent to your email. Check your inbox.";
        } else {
            $error = "Email not found. Please register first.";
        }
    }
    
    if (isset($_POST['verify_otp'])) {
        $email = $_POST['email'];
        $otp = $_POST['otp'];
        
        $query = "SELECT u.id, u.full_name, u.email FROM users u 
                  JOIN user_otps o ON u.id = o.user_id 
                  WHERE u.email = ? AND o.otp = ? AND o.expires_at > NOW()";
        $stmt = $db->prepare($query);
        $stmt->execute([$email, $otp]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            
            // Delete used OTP
            $delete_query = "DELETE FROM user_otps WHERE user_id = ?";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->execute([$user['id']]);
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid or expired OTP.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Login - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <?php include '../includes/header.php'; ?>
    
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                    <i class="fas fa-mobile-alt text-blue-600 mr-2"></i>OTP Login
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter your email to receive OTP
                </p>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                    <?php if (isset($_SESSION['debug_otp'])): ?>
                        <br><small>Debug OTP: <?php echo $_SESSION['debug_otp']; ?></small>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!isset($_SESSION['otp_sent'])): ?>
                <!-- Send OTP Form -->
                <form method="POST" class="mt-8 space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter your email">
                    </div>
                    <button type="submit" name="send_otp" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-paper-plane mr-2"></i>Send OTP
                    </button>
                </form>
            <?php else: ?>
                <!-- Verify OTP Form -->
                <form method="POST" class="mt-8 space-y-6">
                    <input type="hidden" name="email" value="<?php echo $_SESSION['otp_email']; ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Enter OTP</label>
                        <input type="text" name="otp" required maxlength="6" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-2xl"
                               placeholder="000000">
                    </div>
                    <button type="submit" name="verify_otp" 
                            class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i>Verify OTP
                    </button>
                </form>
                
                <div class="text-center">
                    <a href="otp_login.php" class="text-blue-600 hover:text-blue-700">
                        <i class="fas fa-arrow-left mr-1"></i>Back to Email
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Prefer password login? 
                    <a href="login.php" class="text-blue-600 hover:text-blue-700">Click here</a>
                </p>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>