<?php
class Coupons {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function validateCoupon($code, $total) {
        $query = "SELECT * FROM coupons WHERE code = ? AND is_active = 1 
                  AND (expires_at IS NULL OR expires_at > NOW()) 
                  AND (usage_limit IS NULL OR usage_count < usage_limit)
                  AND minimum_amount <= ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$code, $total]);
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$coupon) return false;

        return $coupon;
    }

    public function applyCoupon($coupon, $total) {
        if ($coupon['type'] == 'percentage') {
            $discount = ($total * $coupon['value']) / 100;
            if ($coupon['max_discount'] && $discount > $coupon['max_discount']) {
                $discount = $coupon['max_discount'];
            }
        } else {
            $discount = $coupon['value'];
        }

        return min($discount, $total);
    }

    public function useCoupon($coupon_id) {
        $query = "UPDATE coupons SET usage_count = usage_count + 1 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$coupon_id]);
    }

    public function getActiveCoupons() {
        $query = "SELECT * FROM coupons WHERE is_active = 1 
                  AND (expires_at IS NULL OR expires_at > NOW()) 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}