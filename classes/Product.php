<?php
class Product {
    private $conn;
    private $table_name = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts($filters = []) {
        $query = "SELECT p.*, c.name as category_name, 
                  COALESCE(AVG(r.rating), 0) as avg_rating,
                  COUNT(r.id) as review_count
                  FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id
                  LEFT JOIN reviews r ON p.id = r.product_id
                  WHERE 1=1";
        $params = [];
        
        if (!empty($filters['category'])) {
            $query .= " AND p.category_id = ?";
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['search'])) {
            $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['min_price'])) {
            $query .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $query .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['in_stock'])) {
            $query .= " AND p.stock > 0";
        }
        
        $query .= " GROUP BY p.id, c.name";
        
        if (!empty($filters['min_rating'])) {
            $query .= " HAVING AVG(r.rating) >= ?";
            $params[] = $filters['min_rating'];
        }
        
        // Sorting
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_low':
                $query .= " ORDER BY p.price ASC";
                break;
            case 'price_high':
                $query .= " ORDER BY p.price DESC";
                break;
            case 'rating':
                $query .= " ORDER BY avg_rating DESC";
                break;
            case 'popular':
                $query .= " ORDER BY review_count DESC";
                break;
            case 'name':
                $query .= " ORDER BY p.name ASC";
                break;
            default:
                $query .= " ORDER BY p.created_at DESC";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $query = "SELECT p.*, c.name as category_name FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addProduct($data) {
        $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image, stock) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['name'], $data['description'], $data['price'], $data['category_id'], $data['image'], $data['stock']]);
    }

    public function updateProduct($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET name = ?, description = ?, price = ?, category_id = ?, stock = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['name'], $data['description'], $data['price'], $data['category_id'], $data['stock'], $id]);
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
    
    public function getPriceRange() {
        $query = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function searchProducts($query, $limit = 10) {
        $sql = "SELECT p.*, c.name as category_name FROM " . $this->table_name . " p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE ? OR p.description LIKE ? 
                ORDER BY p.name ASC LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(["%$query%", "%$query%", $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>