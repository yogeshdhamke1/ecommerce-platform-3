<?php
// Social Media Configuration
define('GOOGLE_CLIENT_ID', 'your-google-client-id');
define('GOOGLE_CLIENT_SECRET', 'your-google-client-secret');
define('FACEBOOK_APP_ID', 'your-facebook-app-id');
define('FACEBOOK_APP_SECRET', 'your-facebook-app-secret');

// Social Login URLs
define('GOOGLE_REDIRECT_URI', BASE_URL . 'pages/social_login.php?provider=google');
define('FACEBOOK_REDIRECT_URI', BASE_URL . 'pages/social_login.php?provider=facebook');

// Social Media URLs for sharing
function getSocialShareUrls($url, $title, $description = '', $image = '') {
    $encodedUrl = urlencode($url);
    $encodedTitle = urlencode($title);
    $encodedDesc = urlencode($description);
    $encodedImage = urlencode($image);
    
    return [
        'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
        'twitter' => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}",
        'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
        'whatsapp' => "https://wa.me/?text={$encodedTitle}%20{$encodedUrl}",
        'pinterest' => "https://pinterest.com/pin/create/button/?url={$encodedUrl}&media={$encodedImage}&description={$encodedDesc}",
        'telegram' => "https://t.me/share/url?url={$encodedUrl}&text={$encodedTitle}"
    ];
}
?>