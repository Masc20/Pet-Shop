<?php
/**
 * Pawfect Pet Shop - Order Model
 * Handles order data and operations
 */

class Order extends Model {
    protected $table = 'orders';
    protected $fillable = [
        'order_number', 'user_id', 'status', 'subtotal', 'tax', 'shipping_cost', 'total',
        'shipping_address', 'shipping_city', 'shipping_state', 'shipping_zip',
        'payment_method', 'payment_status', 'notes'
    ];

    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    public function createOrder($userId, $totalAmount) {
        $stmt = $this->pdo->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmt->execute([$userId, $totalAmount]);
        return $this->pdo->lastInsertId();
    }
    
    public function addItem($orderId, $productId, $quantity, $price) {
        $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$orderId, $productId, $quantity, $price]);
    }
    
    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("
            SELECT o.*, u.first_name, u.last_name, u.email 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.id DESC
        ");
        return $stmt->fetchAll();
    }
    
    public function getItems($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, p.name, p.product_image 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
    
    public function updateStatusByPdo($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
    
    public function getStats() {
        $stmt = $this->pdo->query("SELECT 
            COUNT(*) as total_orders,
            SUM(total_amount) as total_revenue,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders
            FROM orders");
        return $stmt->fetch();
    }
    
    public function createFromCart($userId, $shippingData) {
        $cartModel = new Cart();
        $cartSummary = $cartModel->getCartSummary($userId);
        
        if (empty($cartSummary['items'])) {
            return false;
        }
        
        // Validate cart before creating order
        $cartIssues = $cartModel->validateCart($userId);
        if (!empty($cartIssues)) {
            return false; // Cart has issues
        }
        
        $orderData = [
            'order_number' => generate_order_number(),
            'user_id' => $userId,
            'status' => 'pending',
            'subtotal' => $cartSummary['subtotal'],
            'tax' => $cartSummary['tax'],
            'shipping_cost' => $cartSummary['shipping_cost'],
            'total' => $cartSummary['total'],
            'shipping_address' => sanitize_string($shippingData['shipping_address']),
            'shipping_city' => sanitize_string($shippingData['shipping_city']),
            'shipping_state' => sanitize_string($shippingData['shipping_state']),
            'shipping_zip' => sanitize_string($shippingData['shipping_zip']),
            'payment_method' => sanitize_string($shippingData['payment_method']),
            'payment_status' => 'pending',
            'notes' => sanitize_string($shippingData['notes'] ?? '')
        ];
        
        $orderId = db_insert($this->table, $orderData);
        
        if ($orderId) {
            // Transfer cart items to order
            $cartModel->transferToOrder($userId, $orderId);
            return $orderId;
        }
        
        return false;
    }
    
    public function getOrderWithItems($orderId, $userId = null) {
        $sql = "
            SELECT o.*, u.first_name, u.last_name, u.email
            FROM {$this->table} o
            INNER JOIN users u ON o.user_id = u.id
            WHERE o.id = :order_id
        ";
        
        $params = ['order_id' => $orderId];
        
        if ($userId) {
            $sql .= " AND o.user_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        $order = db_select_one($sql, $params);
        
        if (!$order) {
            return null;
        }
        
        // Get order items
        $itemsSql = "
            SELECT oi.*, p.name, p.image
            FROM order_items oi
            INNER JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
            ORDER BY oi.id ASC
        ";
        
        $order['items'] = db_select($itemsSql, ['order_id' => $orderId]);
        
        return $order;
    }
    
    public function getUserOrders($userId, $page = 1, $perPage = 10) {
        return $this->paginate($page, $perPage, ['user_id' => $userId], 'created_at DESC');
    }
    
    public function updateStatus($orderId, $status, $notes = null) {
        $updateData = ['status' => $status];
        
        if ($notes) {
            $updateData['notes'] = $notes;
        }
        
        // Set specific timestamps based on status
        switch ($status) {
            case 'processing':
                $updateData['processed_at'] = now();
                break;
            case 'shipped':
                $updateData['shipped_at'] = now();
                break;
            case 'delivered':
                $updateData['delivered_at'] = now();
                $updateData['payment_status'] = 'completed';
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = now();
                // Restore product stock
                $this->restoreStock($orderId);
                break;
        }
        
        return $this->update($orderId, $updateData);
    }
    
    public function restoreStock($orderId) {
        $items = db_select("SELECT * FROM order_items WHERE order_id = :order_id", ['order_id' => $orderId]);
        
        $productModel = new Product();
        foreach ($items as $item) {
            $productModel->restoreStock($item['product_id'], $item['quantity']);
        }
    }
    
    public function getOrderStats() {
        return [
            'total' => $this->count(),
            'pending' => $this->count(['status' => 'pending']),
            'processing' => $this->count(['status' => 'processing']),
            'shipped' => $this->count(['status' => 'shipped']),
            'delivered' => $this->count(['status' => 'delivered']),
            'cancelled' => $this->count(['status' => 'cancelled']),
            'total_revenue' => $this->getTotalRevenue()
        ];
    }
    
    public function getTotalRevenue($status = ['delivered']) {
        $statusList = "'" . implode("','", $status) . "'";
        $sql = "SELECT SUM(total) as revenue FROM {$this->table} WHERE status IN ({$statusList})";
        $result = db_select_one($sql);
        return (float)($result['revenue'] ?? 0);
    }
    
    public function getRecentOrders($limit = 10) {
        $sql = "
            SELECT o.*, u.first_name, u.last_name
            FROM {$this->table} o
            INNER JOIN users u ON o.user_id = u.id
            ORDER BY o.created_at DESC
            LIMIT {$limit}
        ";
        
        return db_select($sql);
    }
    
    public function getSalesData($period = '30_days') {
        switch ($period) {
            case '7_days':
                $interval = 'INTERVAL 7 DAY';
                $format = '%Y-%m-%d';
                break;
            case '30_days':
                $interval = 'INTERVAL 30 DAY';
                $format = '%Y-%m-%d';
                break;
            case '12_months':
                $interval = 'INTERVAL 12 MONTH';
                $format = '%Y-%m';
                break;
            default:
                $interval = 'INTERVAL 30 DAY';
                $format = '%Y-%m-%d';
        }
        
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '{$format}') as period,
                COUNT(*) as order_count,
                SUM(total) as revenue
            FROM {$this->table}
            WHERE created_at >= DATE_SUB(NOW(), {$interval})
            AND status IN ('delivered', 'shipped')
            GROUP BY DATE_FORMAT(created_at, '{$format}')
            ORDER BY period ASC
        ";
        
        return db_select($sql);
    }
    
    public function getTopCustomers($limit = 10) {
        $sql = "
            SELECT 
                u.id, u.first_name, u.last_name, u.email,
                COUNT(o.id) as order_count,
                SUM(o.total) as total_spent
            FROM users u
            INNER JOIN {$this->table} o ON u.id = o.user_id
            WHERE o.status IN ('delivered', 'shipped')
            GROUP BY u.id
            ORDER BY total_spent DESC
            LIMIT {$limit}
        ";
        
        return db_select($sql);
    }
    
    public function canBeCancelled($orderId, $userId = null) {
        $conditions = ['id' => $orderId, 'status' => ['pending', 'processing']];
        
        if ($userId) {
            $conditions['user_id'] = $userId;
        }
        
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE id = :id AND status IN ('pending', 'processing')";
        $params = ['id' => $orderId];
        
        if ($userId) {
            $sql .= " AND user_id = :user_id";
            $params['user_id'] = $userId;
        }
        
        $result = db_select_one($sql, $params);
        return $result['count'] > 0;
    }
    
    public function cancelOrder($orderId, $userId = null, $reason = null) {
        if (!$this->canBeCancelled($orderId, $userId)) {
            return false;
        }
        
        $updateData = [
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason
        ];
        
        $result = $this->update($orderId, $updateData);
        
        if ($result) {
            $this->restoreStock($orderId);
        }
        
        return $result;
    }
}
?>
