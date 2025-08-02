<?php
require_once '../config/config.php';
require_once '../config/social_config.php';
require_once '../classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$provider = $_GET['provider'] ?? '';

if ($provider === 'google' && isset($_GET['code'])) {
    // Google OAuth
    $token_url = 'https://oauth2.googleapis.com/token';
    $user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
    
    $post_data = [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code',
        'code' => $_GET['code']
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $token_data = json_decode($response, true);
    
    if (isset($token_data['access_token'])) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $user_info_url . '?access_token=' . $token_data['access_token']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user_response = curl_exec($ch);
        curl_close($ch);
        
        $user_data = json_decode($user_response, true);
        
        if ($user_data) {
            handleSocialLogin($user, $user_data, 'google');
        }
    }
}

if ($provider === 'facebook' && isset($_GET['code'])) {
    // Facebook OAuth
    $token_url = 'https://graph.facebook.com/v18.0/oauth/access_token';
    $user_info_url = 'https://graph.facebook.com/me';
    
    $token_params = [
        'client_id' => FACEBOOK_APP_ID,
        'client_secret' => FACEBOOK_APP_SECRET,
        'redirect_uri' => FACEBOOK_REDIRECT_URI,
        'code' => $_GET['code']
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url . '?' . http_build_query($token_params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $token_data = json_decode($response, true);
    
    if (isset($token_data['access_token'])) {
        $user_params = [
            'fields' => 'id,name,email',
            'access_token' => $token_data['access_token']
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $user_info_url . '?' . http_build_query($user_params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user_response = curl_exec($ch);
        curl_close($ch);
        
        $user_data = json_decode($user_response, true);
        
        if ($user_data) {
            handleSocialLogin($user, $user_data, 'facebook');
        }
    }
}

function handleSocialLogin($user, $user_data, $provider) {
    $email = $user_data['email'] ?? '';
    $name = $user_data['name'] ?? '';
    $social_id = $user_data['id'] ?? '';
    
    if (!$email) {
        header("Location: login.php?error=email_required");
        exit();
    }
    
    // Check if user exists
    $existing_user = $user->getUserByEmail($email);
    
    if ($existing_user) {
        // Update social login info
        $query = "UPDATE users SET social_provider = ?, social_id = ? WHERE email = ?";
        $stmt = $GLOBALS['db']->prepare($query);
        $stmt->execute([$provider, $social_id, $email]);
        
        $_SESSION['user_id'] = $existing_user['id'];
        $_SESSION['username'] = $existing_user['username'];
        $_SESSION['email'] = $existing_user['email'];
    } else {
        // Create new user
        $username = strtolower(str_replace(' ', '', $name)) . rand(100, 999);
        $password = password_hash(uniqid(), PASSWORD_DEFAULT); // Random password
        
        $user_data = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'full_name' => $name,
            'social_provider' => $provider,
            'social_id' => $social_id
        ];
        
        if ($user->registerSocial($user_data)) {
            $new_user = $user->getUserByEmail($email);
            $_SESSION['user_id'] = $new_user['id'];
            $_SESSION['username'] = $new_user['username'];
            $_SESSION['email'] = $new_user['email'];
        }
    }
    
    header("Location: dashboard.php");
    exit();
}
?>