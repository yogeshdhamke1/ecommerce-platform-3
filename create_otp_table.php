<?php
require_once 'config/config.php';

$database = new Database();
$db = $database->getConnection();

$sql = "CREATE TABLE IF NOT EXISTS user_otps (
    user_id INT PRIMARY KEY,
    otp VARCHAR(6) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if ($db->exec($sql)) {
    echo "OTP table created successfully!";
} else {
    echo "Error creating table.";
}
?>