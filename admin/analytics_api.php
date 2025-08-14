<?php
require_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Check admin status
$admin_query = "SELECT is_admin FROM users WHERE id = ?";
$admin_stmt = $db->prepare($admin_query);
$admin_stmt->execute([$_SESSION['user_id']]);
$user = $admin_stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !$user['is_admin']) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit();
}

// Today's statistics
$today = date('Y-m-d');
$today_stats_query = "SELECT 
    (SELECT COUNT(*) FROM orders WHERE DATE(created_at) = ?) as todayOrders,
    (SELECT COALESCE(SUM(total), 0) FROM orders WHERE DATE(created_at) = ? AND status = 'completed') as todayRevenue,
    (SELECT COUNT(*) FROM users WHERE DATE(created_at) = ?) as todayUsers";
$today_stmt = $db->prepare($today_stats_query);
$today_stmt->execute([$today, $today, $today]);
$today_stats = $today_stmt->fetch(PDO::FETCH_ASSOC);

// Top products (last 30 days)
$top_products_query = "SELECT p.name, COUNT(oi.id) as sales 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       JOIN orders o ON oi.order_id = o.id 
                       WHERE o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                       GROUP BY p.id, p.name 
                       ORDER BY sales DESC 
                       LIMIT 5";
$top_products_stmt = $db->prepare($top_products_query);
$top_products_stmt->execute();
$top_products = $top_products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent activity
$recent_activity_query = "SELECT 'order' as type, CONCAT('New order #', id, ' from ', (SELECT full_name FROM users WHERE id = user_id)) as text, created_at
                         FROM orders 
                         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                         UNION ALL
                         SELECT 'user' as type, CONCAT('New user registered: ', full_name) as text, created_at
                         FROM users 
                         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                         ORDER BY created_at DESC 
                         LIMIT 5";
$recent_activity_stmt = $db->prepare($recent_activity_query);
$recent_activity_stmt->execute();
$recent_activity_raw = $recent_activity_stmt->fetchAll(PDO::FETCH_ASSOC);

$recent_activity = array_map(function($activity) {
    return [
        'text' => $activity['text'],
        'icon' => $activity['type'] == 'order' ? 'shopping-cart' : 'user-plus',
        'time' => date('H:i', strtotime($activity['created_at']))
    ];
}, $recent_activity_raw);

// Prepare response
$response = [
    'todayOrders' => $today_stats['todayOrders'],
    'todayRevenue' => number_format($today_stats['todayRevenue'], 0),
    'todayUsers' => $today_stats['todayUsers'],
    'topProducts' => $top_products ?: [
        ['name' => 'No sales yet', 'sales' => '0']
    ],
    'recentActivity' => $recent_activity ?: [
        ['text' => 'No recent activity', 'icon' => 'info-circle', 'time' => '']
    ]
];

echo json_encode($response);
?>