<?php
require_once 'vendor/autoload.php';
use Dompdf\Dompdf;

class Invoice {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateInvoicePDF($order_id) {
        $order_query = "SELECT o.*, u.full_name, u.email FROM orders o 
                        JOIN users u ON o.user_id = u.id WHERE o.id = ?";
        $order_stmt = $this->conn->prepare($order_query);
        $order_stmt->execute([$order_id]);
        $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

        $items_query = "SELECT oi.*, p.name FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
        $items_stmt = $this->conn->prepare($items_query);
        $items_stmt->execute([$order_id]);
        $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

        $html = $this->generateInvoiceHTML($order, $items);
        
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $dompdf->output();
    }

    private function generateInvoiceHTML($order, $items) {
        global $currencies;
        $currency = $order['currency'];
        $symbol = $currencies[$currency]['symbol'];
        
        $html = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { text-align: center; margin-bottom: 30px; }
                .invoice-details { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .total { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>INVOICE</h1>
                <h2>' . SITE_NAME . '</h2>
            </div>
            
            <div class="invoice-details">
                <p><strong>Invoice #:</strong> INV-' . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . '</p>
                <p><strong>Date:</strong> ' . date('Y-m-d', strtotime($order['created_at'])) . '</p>
                <p><strong>Customer:</strong> ' . $order['full_name'] . '</p>
                <p><strong>Email:</strong> ' . $order['email'] . '</p>
                <p><strong>Shipping Address:</strong> ' . $order['shipping_address'] . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($items as $item) {
            $item_total = $item['quantity'] * $item['price'];
            $html .= '
                    <tr>
                        <td>' . $item['name'] . '</td>
                        <td>' . $item['quantity'] . '</td>
                        <td>' . $symbol . number_format($item['price'], 2) . '</td>
                        <td>' . $symbol . number_format($item_total, 2) . '</td>
                    </tr>';
        }
        
        $html .= '
                    <tr class="total">
                        <td colspan="3">Total Amount</td>
                        <td>' . $symbol . number_format($order['total'], 2) . '</td>
                    </tr>
                </tbody>
            </table>
        </body>
        </html>';
        
        return $html;
    }
}
?>