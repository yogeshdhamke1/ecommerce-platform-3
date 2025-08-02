<?php
class Shipping {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function calculateShippingRates($destination, $weight, $dimensions = null) {
        $rates = [];
        
        // Standard shipping rates based on weight and distance
        $baseRate = $this->getBaseShippingRate($destination);
        $weightRate = $weight * 2; // ₹2 per kg
        
        $rates[] = [
            'method' => 'Standard Delivery',
            'description' => '5-7 business days',
            'rate' => $baseRate + $weightRate,
            'delivery_time' => '5-7 days',
            'carrier' => 'India Post'
        ];
        
        $rates[] = [
            'method' => 'Express Delivery',
            'description' => '2-3 business days',
            'rate' => ($baseRate + $weightRate) * 1.5,
            'delivery_time' => '2-3 days',
            'carrier' => 'BlueDart'
        ];
        
        $rates[] = [
            'method' => 'Same Day Delivery',
            'description' => 'Same day delivery (metro cities only)',
            'rate' => ($baseRate + $weightRate) * 2.5,
            'delivery_time' => 'Same day',
            'carrier' => 'Dunzo'
        ];
        
        return $rates;
    }
    
    private function getBaseShippingRate($destination) {
        $pincode = $destination['pincode'] ?? '';
        
        // Metro cities - lower base rate
        $metroPincodes = ['110001', '400001', '700001', '600001', '560001', '500001'];
        if (in_array(substr($pincode, 0, 6), $metroPincodes)) {
            return 50;
        }
        
        // Tier 2 cities
        if (strlen($pincode) >= 6) {
            $firstDigit = substr($pincode, 0, 1);
            if (in_array($firstDigit, ['1', '2', '3', '4'])) {
                return 75;
            }
        }
        
        // Remote areas
        return 100;
    }
    
    public function getShippingMethods() {
        $query = "SELECT * FROM shipping_methods WHERE active = 1 ORDER BY sort_order";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function validatePincode($pincode) {
        // Basic Indian pincode validation
        return preg_match('/^[1-9][0-9]{5}$/', $pincode);
    }
    
    public function getDeliveryEstimate($pincode, $method) {
        if (!$this->validatePincode($pincode)) {
            return null;
        }
        
        $baseDate = new DateTime();
        
        switch ($method) {
            case 'Standard Delivery':
                $baseDate->add(new DateInterval('P5D'));
                $endDate = clone $baseDate;
                $endDate->add(new DateInterval('P2D'));
                break;
            case 'Express Delivery':
                $baseDate->add(new DateInterval('P2D'));
                $endDate = clone $baseDate;
                $endDate->add(new DateInterval('P1D'));
                break;
            case 'Same Day Delivery':
                return $baseDate->format('Y-m-d');
            default:
                $baseDate->add(new DateInterval('P7D'));
                $endDate = clone $baseDate;
                $endDate->add(new DateInterval('P3D'));
        }
        
        return $baseDate->format('M d') . ' - ' . $endDate->format('M d, Y');
    }
}
?>