<?php
session_start();
define('BASE_URL', 'http://localhost/Yogesh/0/E-Commerce Platform/');
define('SITE_NAME', 'E-Commerce Platform');

// Currency settings
$currencies = [
    'INR' => ['symbol' => '₹', 'rate' => 1.00],
    'USD' => ['symbol' => '$', 'rate' => 0.012],
    'EUR' => ['symbol' => '€', 'rate' => 0.011],
    'GBP' => ['symbol' => '£', 'rate' => 0.0095],
    'JPY' => ['symbol' => '¥', 'rate' => 1.80]
];

if (!isset($_SESSION['currency'])) {
    $_SESSION['currency'] = 'INR';
}

function formatPrice($price, $currency = null) {
    global $currencies;
    $currency = $currency ?: $_SESSION['currency'];
    $convertedPrice = $price * $currencies[$currency]['rate'];
    return $currencies[$currency]['symbol'] . number_format($convertedPrice, 2);
}

require_once 'database.php';
?>