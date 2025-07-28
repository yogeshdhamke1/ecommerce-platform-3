<?php
class Product {
    private $conn;
    private $table_name = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllProducts($category = null, $search = null) {
        $query = "SELECT p.*, c.name as category_name FROM " . $this->table_name . " p 
                  LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
        $params = [];
        
        if ($category) {
            $query .= " AND p.category_id = ?";
            $params[] = $category;
        }
        
        if ($search) {
            $query .= " AND (p.name ILIKE ? OR p.description ILIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $query .= " ORDER BY p.created_at DESC";
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
}
?>