<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $email, $password, $full_name) {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password, full_name) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        return $stmt->execute([$username, $email, $password_hash, $full_name]);
    }

    public function login($email, $password) {
        $query = "SELECT id, username, email, password, full_name FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['full_name'] = $row['full_name'];
                return true;
            }
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, $data) {
        $query = "UPDATE " . $this->table_name . " SET full_name = ?, phone = ?, address = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['full_name'], $data['phone'], $data['address'], $id]);
    }

    public function resetPassword($email, $new_password) {
        $query = "UPDATE " . $this->table_name . " SET password = ? WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        return $stmt->execute([$password_hash, $email]);
    }
    
    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function registerSocial($data) {
        $query = "INSERT INTO " . $this->table_name . " (username, email, password, full_name, social_provider, social_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['username'], $data['email'], $data['password'], $data['full_name'], $data['social_provider'], $data['social_id']]);
    }
}
?>