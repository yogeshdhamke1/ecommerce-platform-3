<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($user_id, $total, $shipping_address, $currency) {
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO orders (user_id, total, shipping_address, currency, status) 
                      VALUES (?, ?, ?, ?, 'pending')";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id, $total, $shipping_address, $currency]);
            $order_id = $this->conn->lastInsertId();

            $cart_query = "SELECT * FROM cart WHERE user_id = ?";
            $cart_stmt = $this->conn->prepare($cart_query);
            $cart_stmt->execute([$user_id]);
            $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($cart_items as $item) {
                $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                               SELECT ?, ?, ?, price FROM products WHERE id = ?";
                $item_stmt = $this->conn->prepare($item_query);
                $item_stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['product_id']]);
            }

            $clear_cart = "DELETE FROM cart WHERE user_id = ?";
            $clear_stmt = $this->conn->prepare($clear_cart);
            $clear_stmt->execute([$user_id]);

            $this->conn->commit();
            return $order_id;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    public function getUserOrders($user_id) {
        $query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderById($id) {
        $query = "SELECT o.*, u.full_name, u.email FROM orders o 
                  JOIN users u ON o.user_id = u.id WHERE o.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, p.name, p.image FROM order_items oi 
                  JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrderStatus($id, $status) {
        $query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $id]);
    }
}
?>