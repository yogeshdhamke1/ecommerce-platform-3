<?php
require_once (isset($is_in_pages) && $is_in_pages ? '../' : '') . 'config/social_config.php';

function renderSocialLoginButtons($redirect_after = 'dashboard.php') {
    $google_auth_url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query([
        'client_id' => GOOGLE_CLIENT_ID,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'scope' => 'email profile',
        'response_type' => 'code',
        'state' => $redirect_after
    ]);
    
    $facebook_auth_url = 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query([
        'client_id' => FACEBOOK_APP_ID,
        'redirect_uri' => FACEBOOK_REDIRECT_URI,
        'scope' => 'email',
        'response_type' => 'code',
        'state' => $redirect_after
    ]);
    
    echo '
    <div class="space-y-3">
        <div class="text-center text-gray-500 text-sm">Or continue with</div>
        <div class="grid grid-cols-2 gap-3">
            <a href="' . $google_auth_url . '" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Google
            </a>
            <a href="' . $facebook_auth_url . '" class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Facebook
            </a>
        </div>
    </div>';
}

function renderSocialShareButtons($url, $title, $description = '', $image = '') {
    $shareUrls = getSocialShareUrls($url, $title, $description, $image);
    
    echo '
    <div class="social-share">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Share this product</h3>
        <div class="flex flex-wrap gap-3">
            <a href="' . $shareUrls['facebook'] . '" target="_blank" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition text-sm">
                <i class="fab fa-facebook-f mr-2"></i>Facebook
            </a>
            <a href="' . $shareUrls['twitter'] . '" target="_blank" class="flex items-center px-4 py-2 bg-blue-400 text-white rounded-md hover:bg-blue-500 transition text-sm">
                <i class="fab fa-twitter mr-2"></i>Twitter
            </a>
            <a href="' . $shareUrls['whatsapp'] . '" target="_blank" class="flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition text-sm">
                <i class="fab fa-whatsapp mr-2"></i>WhatsApp
            </a>
            <a href="' . $shareUrls['linkedin'] . '" target="_blank" class="flex items-center px-4 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-800 transition text-sm">
                <i class="fab fa-linkedin-in mr-2"></i>LinkedIn
            </a>
            <a href="' . $shareUrls['pinterest'] . '" target="_blank" class="flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition text-sm">
                <i class="fab fa-pinterest mr-2"></i>Pinterest
            </a>
            <a href="' . $shareUrls['telegram'] . '" target="_blank" class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition text-sm">
                <i class="fab fa-telegram mr-2"></i>Telegram
            </a>
        </div>
        <div class="mt-4">
            <button onclick="copyToClipboard(\'' . $url . '\')" class="flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition text-sm">
                <i class="fas fa-copy mr-2"></i>Copy Link
            </button>
        </div>
    </div>
    
    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert("Link copied to clipboard!");
        });
    }
    </script>';
}
?>