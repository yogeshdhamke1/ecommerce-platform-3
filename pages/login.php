<?php
require_once '../config/config.php';
require_once '../classes/User.php';

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    if (isset($_POST['send_otp'])) {
        $contact = $_POST['contact'];
        $type = $_POST['otp_type'];
        
        // Check if user exists
        $field = $type == 'email' ? 'email' : 'phone';
        $query = "SELECT id, full_name FROM users WHERE $field = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$contact]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_data) {
            $otp = rand(100000, 999999);
            $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));
            
            $otp_query = "INSERT INTO user_otps (user_id, otp, expires_at) VALUES (?, ?, ?) 
                         ON DUPLICATE KEY UPDATE otp = ?, expires_at = ?";
            $otp_stmt = $db->prepare($otp_query);
            $otp_stmt->execute([$user_data['id'], $otp, $expires, $otp, $expires]);
            
            $_SESSION['otp_sent'] = true;
            $_SESSION['otp_contact'] = $contact;
            $_SESSION['otp_type'] = $type;
            $_SESSION['debug_otp'] = $otp;
            
            $success = "OTP sent to your $type. Check your $type.";
        } else {
            $error = ucfirst($type) . " not found. Please register first.";
        }
    }
    
    if (isset($_POST['verify_otp'])) {
        $contact = $_POST['contact'];
        $otp = $_POST['otp'];
        $type = $_POST['otp_type'];
        
        $field = $type == 'email' ? 'email' : 'phone';
        $query = "SELECT u.id, u.full_name, u.email, u.username FROM users u 
                  JOIN user_otps o ON u.id = o.user_id 
                  WHERE u.$field = ? AND o.otp = ? AND o.expires_at > NOW()";
        $stmt = $db->prepare($query);
        $stmt->execute([$contact, $otp]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_data) {
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['full_name'] = $user_data['full_name'];
            $_SESSION['email'] = $user_data['email'];
            $_SESSION['username'] = $user_data['username'];
            
            $delete_query = "DELETE FROM user_otps WHERE user_id = ?";
            $delete_stmt = $db->prepare($delete_query);
            $delete_stmt->execute([$user_data['id']]);
            
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid or expired OTP.";
        }
    }
    
    if (isset($_POST['password'])) {
        $user = new User($db);
        if ($user->login($_POST['email'], $_POST['password'])) {
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <meta name="description" content="Login to your account to access exclusive features and manage your orders.">
    <meta name="robots" content="noindex, nofollow">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <a href="../index.php" class="inline-flex items-center text-2xl font-bold text-blue-600 hover:text-blue-700 transition">
                    <i class="fas fa-store mr-2"></i><?php echo SITE_NAME; ?>
                </a>
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Welcome Back</h2>
                <p class="mt-2 text-sm text-gray-600">Sign in to your account</p>
            </div>
            
            <!-- Login Form -->
            <div class="bg-white rounded-lg shadow-xl p-8">
                <?php if (isset($error)): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                        <i class="fas fa-check-circle mr-2"></i><?php echo $success; ?>
                        <?php if (isset($_SESSION['debug_otp'])): ?>
                            <br><small>Debug OTP: <?php echo $_SESSION['debug_otp']; ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- OTP Login Form -->
                <div id="otpLoginForm" style="display: none;">
                    <?php if (!isset($_SESSION['otp_sent'])): ?>
                        <form method="POST" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Login Method</label>
                                <div class="flex space-x-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="otp_type" value="email" checked class="mr-2">
                                        <i class="fas fa-envelope mr-1"></i>Email
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="otp_type" value="phone" class="mr-2">
                                        <i class="fas fa-phone mr-1"></i>Mobile
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email/Mobile</label>
                                <input type="text" name="contact" required 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enter email or mobile number">
                            </div>
                            <button type="submit" name="send_otp" 
                                    class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition">
                                <i class="fas fa-paper-plane mr-2"></i>Send OTP
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="contact" value="<?php echo $_SESSION['otp_contact']; ?>">
                            <input type="hidden" name="otp_type" value="<?php echo $_SESSION['otp_type']; ?>">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Enter OTP</label>
                                <input type="text" name="otp" required maxlength="6" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-2xl"
                                       placeholder="000000">
                            </div>
                            <button type="submit" name="verify_otp" 
                                    class="w-full bg-green-600 text-white py-3 px-4 rounded-md hover:bg-green-700 transition">
                                <i class="fas fa-check mr-2"></i>Verify OTP
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <div class="mt-4 text-center">
                        <button type="button" onclick="showPasswordLogin()" class="text-blue-600 hover:text-blue-700">
                            <i class="fas fa-arrow-left mr-1"></i>Back to Password Login
                        </button>
                    </div>
                </div>
                
                <!-- Password Login Form -->
                <div id="passwordLoginForm">
                    <form method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1"></i>Email Address
                        </label>
                        <input type="email" id="email" name="email" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                               placeholder="Enter your email">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1"></i>Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   placeholder="Enter your password">
                            <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <i id="passwordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>
                        <a href="forgot_password.php" class="text-sm text-blue-600 hover:text-blue-500 transition">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <i class="fas fa-sign-in-alt mr-2"></i>Sign In
                    </button>
                    
                    <div class="mt-4 text-center">
                        <button type="button" onclick="showOTPLogin()" class="text-green-600 hover:text-green-700 font-medium">
                            <i class="fas fa-mobile-alt mr-1"></i>Login with OTP instead
                        </button>
                    </div>
                    </form>
                </div>
                </form>
                
                <div class="mt-6">
                    <?php 
                    $is_in_pages = true;
                    include '../includes/social_buttons.php';
                    renderSocialLoginButtons('dashboard.php');
                    ?>
                </div>
                
                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-gray-500">New to <?php echo SITE_NAME; ?>?</span>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <a href="register.php" class="w-full inline-flex justify-center py-3 px-4 border border-blue-600 rounded-md shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 transition">
                            <i class="fas fa-user-plus mr-2"></i>Create New Account
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Back to Home -->
            <div class="text-center">
                <a href="../index.php" class="text-sm text-gray-600 hover:text-blue-600 transition">
                    <i class="fas fa-arrow-left mr-1"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        
        function showOTPLogin() {
            document.getElementById('passwordLoginForm').style.display = 'none';
            document.getElementById('otpLoginForm').style.display = 'block';
        }
        
        function showPasswordLogin() {
            document.getElementById('otpLoginForm').style.display = 'none';
            document.getElementById('passwordLoginForm').style.display = 'block';
        }
    </script>
</body>
</html>