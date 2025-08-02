<?php
class TCPDF_Invoice {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function generateInvoicePDF($order_id) {
        // Get order details
        $order_query = "SELECT o.*, u.full_name, u.email FROM orders o 
                        JOIN users u ON o.user_id = u.id WHERE o.id = ?";
        $order_stmt = $this->conn->prepare($order_query);
        $order_stmt->execute([$order_id]);
        $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

        // Get order items
        $items_query = "SELECT oi.*, p.name FROM order_items oi 
                        JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
        $items_stmt = $this->conn->prepare($items_query);
        $items_stmt->execute([$order_id]);
        $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create PDF content
        $html = $this->generateInvoiceHTML($order, $items);
        
        // Simple PDF generation using HTML to PDF conversion
        return $this->htmlToPDF($html, "Invoice-{$order_id}.pdf");
    }

    private function htmlToPDF($html, $filename) {
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        // Simple HTML to PDF conversion using wkhtmltopdf if available
        // Otherwise return HTML content
        if ($this->commandExists('wkhtmltopdf')) {
            $tempHtml = tempnam(sys_get_temp_dir(), 'invoice') . '.html';
            $tempPdf = tempnam(sys_get_temp_dir(), 'invoice') . '.pdf';
            
            file_put_contents($tempHtml, $html);
            exec("wkhtmltopdf {$tempHtml} {$tempPdf}");
            
            if (file_exists($tempPdf)) {
                $pdfContent = file_get_contents($tempPdf);
                unlink($tempHtml);
                unlink($tempPdf);
                return $pdfContent;
            }
        }
        
        // Fallback: return HTML with PDF headers
        return $html;
    }

    private function commandExists($command) {
        $whereIsCommand = (PHP_OS == 'WINNT') ? 'where' : 'which';
        $process = proc_open(
            "$whereIsCommand $command",
            array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("pipe", "w")
            ),
            $pipes
        );
        if ($process !== false) {
            $stdout = stream_get_contents($pipes[1]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
            return $stdout != '';
        }
        return false;
    }

    private function generateInvoiceHTML($order, $items) {
        global $currencies;
        $currency = $order['currency'];
        $symbol = isset($currencies[$currency]) ? $currencies[$currency]['symbol'] : '₹';
        
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Invoice</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .company-info { margin-bottom: 20px; }
                .invoice-details { margin-bottom: 20px; }
                .customer-info { background: #f5f5f5; padding: 15px; margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .total-row { background-color: #e8f4f8; font-weight: bold; }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>INVOICE</h1>
                <h2>' . SITE_NAME . '</h2>
                <p>123 Business Street, City, State 12345<br>
                Phone: +1 (555) 123-4567 | Email: info@ecommerce.com</p>
            </div>
            
            <div class="invoice-details">
                <table style="border: none;">
                    <tr style="border: none;">
                        <td style="border: none; width: 50%;">
                            <strong>Invoice Number:</strong> INV-' . str_pad($order['id'], 6, '0', STR_PAD_LEFT) . '<br>
                            <strong>Invoice Date:</strong> ' . date('M d, Y', strtotime($order['created_at'])) . '<br>
                            <strong>Order ID:</strong> #' . $order['id'] . '
                        </td>
                        <td style="border: none; width: 50%;">
                            <strong>Currency:</strong> ' . $order['currency'] . '<br>
                            <strong>Status:</strong> ' . ucfirst($order['status']) . '
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="customer-info">
                <h3>Bill To:</h3>
                <strong>' . htmlspecialchars($order['full_name']) . '</strong><br>
                ' . htmlspecialchars($order['email']) . '<br><br>
                <strong>Shipping Address:</strong><br>
                ' . nl2br(htmlspecialchars($order['shipping_address'])) . '
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Item Description</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-right">Unit Price</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>';
        
        $subtotal = 0;
        foreach ($items as $item) {
            $item_total = $item['quantity'] * $item['price'];
            $subtotal += $item_total;
            $html .= '
                    <tr>
                        <td>' . htmlspecialchars($item['name']) . '</td>
                        <td class="text-center">' . $item['quantity'] . '</td>
                        <td class="text-right">' . $symbol . number_format($item['price'], 2) . '</td>
                        <td class="text-right">' . $symbol . number_format($item_total, 2) . '</td>
                    </tr>';
        }
        
        $html .= '
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Subtotal:</td>
                        <td class="text-right">' . $symbol . number_format($subtotal, 2) . '</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Shipping:</td>
                        <td class="text-right">Free</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3" class="text-right">Tax:</td>
                        <td class="text-right">' . $symbol . '0.00</td>
                    </tr>
                    <tr class="total-row" style="font-size: 16px;">
                        <td colspan="3" class="text-right"><strong>TOTAL AMOUNT:</strong></td>
                        <td class="text-right"><strong>' . $symbol . number_format($order['total'], 2) . '</strong></td>
                    </tr>
                </tbody>
            </table>
            
            <div class="footer">
                <p><strong>Thank you for your business!</strong></p>
                <p>For any questions regarding this invoice, please contact us at info@ecommerce.com</p>
                <p>This is a computer-generated invoice.</p>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}