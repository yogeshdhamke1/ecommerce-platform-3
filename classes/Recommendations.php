<?php
class Recommendations {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getRecommendedProducts($user_id, $limit = 6) {
        // Get user's order history
        $history_query = "SELECT DISTINCT p.category_id FROM order_items oi 
                         JOIN products p ON oi.product_id = p.id 
                         JOIN orders o ON oi.order_id = o.id 
                         WHERE o.user_id = ?";
        $history_stmt = $this->conn->prepare($history_query);
        $history_stmt->execute([$user_id]);
        $categories = $history_stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($categories)) {
            // Return popular products if no history
            return $this->getPopularProducts($limit);
        }

        // Get products from user's preferred categories
        $placeholders = str_repeat('?,', count($categories) - 1) . '?';
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.category_id IN ($placeholders) 
                  ORDER BY RAND() LIMIT ?";
        
        $params = array_merge($categories, [$limit]);
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopularProducts($limit = 6) {
        $query = "SELECT p.*, c.name as category_name, COUNT(oi.id) as order_count 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  LEFT JOIN order_items oi ON p.id = oi.product_id 
                  GROUP BY p.id 
                  ORDER BY order_count DESC, p.created_at DESC 
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRelatedProducts($product_id, $limit = 4) {
        // Get current product's category
        $cat_query = "SELECT category_id FROM products WHERE id = ?";
        $cat_stmt = $this->conn->prepare($cat_query);
        $cat_stmt->execute([$product_id]);
        $category_id = $cat_stmt->fetchColumn();

        if (!$category_id) return [];

        // Get related products from same category
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.category_id = ? AND p.id != ? 
                  ORDER BY RAND() LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$category_id, $product_id, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}