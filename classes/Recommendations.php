<?php
class Recommendations {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getRecommendedProducts($user_id, $limit = 6) {
        // Always return products - simplified algorithm
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.stock > 0
                  ORDER BY RAND() LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPopularProducts($limit = 6) {
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.stock > 0
                  ORDER BY p.created_at DESC 
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRelatedProducts($product_id, $limit = 4) {
        // Get any products except current one
        $query = "SELECT p.*, c.name as category_name FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.id != ? AND p.stock > 0
                  ORDER BY RAND() LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$product_id, $limit]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}