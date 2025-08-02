<?php
require_once '../config/config.php';
require_once '../classes/Order.php';
require_once '../classes/TCPDF_Invoice.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);
$invoice = new TCPDF_Invoice($db);

$order_details = $order->getOrderById($_GET['order_id']);
$order_items = $order->getOrderItems($_GET['order_id']);

if (!$order_details || $order_details['user_id'] != $_SESSION['user_id']) {
    header("Location: orders.php");
    exit();
}

// Handle PDF download
if (isset($_GET['download']) && $_GET['download'] == 'pdf') {
    $pdf_content = $invoice->generateInvoicePDF($_GET['order_id']);
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="invoice-' . $_GET['order_id'] . '.pdf"');
    header('Content-Length: ' . strlen($pdf_content));
    
    echo $pdf_content;
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?php echo str_pad($order_details['id'], 6, '0', STR_PAD_LEFT); ?> - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="no-print">
        <?php include '../includes/header.php'; ?>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <!-- Print/Download Actions -->
        <div class="no-print mb-6 flex justify-between items-center">
            <a href="orders.php" class="text-blue-600 hover:text-blue-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
            <div class="space-x-3">
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    <i class="fas fa-print mr-2"></i>Print Invoice
                </button>
                <a href="invoice.php?order_id=<?php echo $order_details['id']; ?>&download=pdf" 
                   class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition inline-block">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
            </div>
        </div>

        <!-- Invoice Content -->
        <div class="bg-white rounded-lg shadow-lg p-8 max-w-4xl mx-auto">
            <!-- Invoice Header -->
            <div class="border-b-2 border-gray-200 pb-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            <i class="fas fa-store text-blue-600 mr-2"></i><?php echo SITE_NAME; ?>
                        </h1>
                        <p class="text-gray-600">123 Business Street</p>
                        <p class="text-gray-600">City, State 12345</p>
                        <p class="text-gray-600">Phone: +1 (555) 123-4567</p>
                        <p class="text-gray-600">Email: info@ecommerce.com</p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">INVOICE</h2>
                        <p class="text-lg font-semibold text-blue-600">
                            #INV-<?php echo str_pad($order_details['id'], 6, '0', STR_PAD_LEFT); ?>
                        </p>
                        <p class="text-gray-600">Date: <?php echo date('M d, Y', strtotime($order_details['created_at'])); ?></p>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Bill To:</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($order_details['full_name']); ?></p>
                        <p class="text-gray-600"><?php echo htmlspecialchars($order_details['email']); ?></p>
                        <p class="text-gray-600 mt-2">
                            <strong>Shipping Address:</strong><br>
                            <?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?>
                        </p>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Details:</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p><strong>Order ID:</strong> #<?php echo $order_details['id']; ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('M d, Y', strtotime($order_details['created_at'])); ?></p>
                        <p><strong>Currency:</strong> <?php echo $order_details['currency']; ?></p>
                        <p><strong>Status:</strong> 
                            <span class="px-2 py-1 text-xs rounded-full 
                                <?php echo $order_details['status'] == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                <?php echo ucfirst($order_details['status']); ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Invoice Items -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Items Ordered:</h3>
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-medium text-gray-900 border-b">Item</th>
                                <th class="px-4 py-3 text-center text-sm font-medium text-gray-900 border-b">Qty</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-900 border-b">Unit Price</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-900 border-b">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php 
                            $subtotal = 0;
                            foreach ($order_items as $item): 
                                $item_total = $item['quantity'] * $item['price'];
                                $subtotal += $item_total;
                            ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 border-b">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-center border-b">
                                        <?php echo $item['quantity']; ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right border-b">
                                        <?php echo formatPrice($item['price']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right border-b">
                                        <?php echo formatPrice($item_total); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Invoice Totals -->
            <div class="flex justify-end">
                <div class="w-full md:w-1/2">
                    <div class="bg-gray-50 p-6 rounded-md">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium"><?php echo formatPrice($subtotal); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium text-green-600">Free</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium"><?php echo formatPrice(0); ?></span>
                            </div>
                            <div class="border-t pt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-bold text-gray-900">Total Amount:</span>
                                    <span class="text-lg font-bold text-blue-600"><?php echo formatPrice($order_details['total']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Footer -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <div class="text-center text-sm text-gray-600">
                    <p class="mb-2"><strong>Thank you for your business!</strong></p>
                    <p>For any questions regarding this invoice, please contact us at info@ecommerce.com</p>
                    <p class="mt-4">This is a computer-generated invoice and does not require a signature.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="no-print">
        <?php include '../includes/footer.php'; ?>
    </div>
</body>
</html>