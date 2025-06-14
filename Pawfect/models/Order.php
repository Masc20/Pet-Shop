<?php
/**
 * Pawfect Pet Shop - Order Model
 * Handles order data and operations
 */

require_once 'models/Product.php';
require_once 'models/Cart.php';

class Order extends Model {
    protected $table = 'orders';
    protected $fillable = [
        'order_number', 'user_id', 'status', 'subtotal', 'tax', 'shipping_cost', 'total',
        'shipping_address', 'shipping_city', 'shipping_barangay', 'shipping_zip',
        'payment_method', 'notes', 'shipped_date', 'delivery_date', 'cancelled_date'
    ];

    protected $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    public function createOrder($userId, $totalAmount, $deliveryAddressId, $paymentMethod) {
        $orderNumber = $this->generateOrderNumber();
        $stmt = $this->pdo->prepare("INSERT INTO orders (order_number, user_id, total_amount, delivery_address_id, payment_method, order_date, status) VALUES (?, ?, ?, ?, ?, NOW(), 'pending')");
        $stmt->execute([$orderNumber, $userId, $totalAmount, $deliveryAddressId, $paymentMethod]);
        return $this->pdo->lastInsertId();
    }
    
    public function addItem($orderId, $productId, $quantity, $price) {
        $stmt = $this->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$orderId, $productId, $quantity, $price]);
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT o.*, u.first_name, u.last_name, u.email, da.city, da.barangay, da.street, da.zipcode FROM orders o JOIN users u ON o.user_id = u.id LEFT JOIN delivery_addresses da ON o.delivery_address_id = da.id ORDER BY o.id DESC");
        $orders = $stmt->fetchAll();
        foreach ($orders as &$order) {
            $order['items'] = $this->getItems($order['id']);
            $order['delivery_address'] = [
                'city' => $order['city'],
                'barangay' => $order['barangay'],
                'street' => $order['street'],
                'zipcode' => $order['zipcode']
            ];
        }
        return $orders;
    }
    
    public function getItems($orderId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT oi.*, p.*
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = :order_id
            ");
            $stmt->bindValue(':order_id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error in getItems: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateStatusByPdo($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
    
    public function getStats() {
        $stmt = $this->pdo->query("SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'delivered' THEN total_amount ELSE 0 END) as total_revenue,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders
            FROM orders");
        return $stmt->fetch();
    }
    
    /**
     * Create a new order from cart items
     * @param int $userId User ID
     * @param array $shippingData Shipping information
     * @return int|false Order ID on success, false on failure
     */
    public function createFromCart($userId, $shippingData) {
        try {
            $this->pdo->beginTransaction();

            $cartModel = new Cart();
            $cartSummary = $cartModel->getCartSummary($userId);
            
            if (empty($cartSummary['items'])) {
                throw new Exception('Cart is empty');
            }
            
            // Validate cart before creating order
            $cartIssues = $cartModel->validateCart($userId);
            if (!empty($cartIssues)) {
                throw new Exception('Cart validation failed: ' . implode(', ', $cartIssues));
            }
            
            $orderData = [
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $userId,
                'status' => 'pending',
                'subtotal' => $cartSummary['subtotal'],
                'tax' => $cartSummary['tax'],
                'shipping_cost' => $cartSummary['shipping_cost'],
                'total' => $cartSummary['total'],
                'shipping_address' => $this->sanitizeString($shippingData['shipping_address']),
                'shipping_city' => $this->sanitizeString($shippingData['shipping_city']),
                'shipping_barangay' => $this->sanitizeString($shippingData['shipping_barangay']),
                'shipping_zip' => $this->sanitizeString($shippingData['shipping_zip']),
                'payment_method' => $this->sanitizeString($shippingData['payment_method']),
                'payment_status' => 'pending',
                'notes' => $this->sanitizeString($shippingData['notes'] ?? ''),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table} 
                (" . implode(', ', array_keys($orderData)) . ") 
                VALUES (" . implode(', ', array_fill(0, count($orderData), '?')) . ")
            ");
            
            $stmt->execute(array_values($orderData));
            $orderId = $this->pdo->lastInsertId();
            
            if (!$orderId) {
                throw new Exception('Failed to create order');
            }
            
            // Transfer cart items to order
            $cartModel->transferToOrder($userId, $orderId);
            
            $this->pdo->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get order details with items
     * @param int $orderId Order ID
     * @param int|null $userId Optional user ID for validation
     * @return array|null Order data or null if not found
     */
    public function getOrderWithItems($orderId, $userId = null) {
        try {
            $sql = "
                SELECT o.*, u.first_name, u.last_name, u.email,
                       da.city, da.barangay, da.street, da.zipcode
                FROM {$this->table} o
                INNER JOIN users u ON o.user_id = u.id
                LEFT JOIN delivery_addresses da ON o.delivery_address_id = da.id
                WHERE o.id = :order_id
            ";
            
            $params = ['order_id' => $orderId];
            
            if ($userId) {
                $sql .= " AND o.user_id = :user_id";
                $params['user_id'] = $userId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
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
            
            $stmt = $this->pdo->prepare($itemsSql);
            $stmt->execute(['order_id' => $orderId]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Structure the delivery address
            $order['delivery_address'] = [
                'city' => $order['city'] ?? null,
                'barangay' => $order['barangay'] ?? null,
                'street' => $order['street'] ?? null,
                'zipcode' => $order['zipcode'] ?? null
            ];
            
            // Clean up redundant fields
            unset(
                $order['city'],
                $order['barangay'],
                $order['street'],
                $order['zipcode']
            );
            
            return $order;
            
        } catch (Exception $e) {
            error_log("Error fetching order: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get user's orders with pagination
     * @param int $userId User ID
     * @param int $limit Number of records per page
     * @param int $offset Offset for pagination
     * @param array|null $statuses Filter by order statuses
     * @return array Orders with items
     */
    public function getUserOrders($userId, $limit = 5, $offset = 0, $statuses = null, $sortBy = 'order_date', $sortOrder = 'DESC') {
        try {
            $sql = "
                SELECT o.*, u.first_name, u.last_name, u.email,
                       da.city, da.barangay, da.street, da.zipcode
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                LEFT JOIN delivery_addresses da ON o.delivery_address_id = da.id
                WHERE o.user_id = :user_id
            ";
            
            $params = ['user_id' => $userId];
            
            if ($statuses && is_array($statuses)) {
                $placeholders = [];
                foreach ($statuses as $key => $status) {
                    $paramName = "status$key";
                    $placeholders[] = ":$paramName";
                    $params[$paramName] = $status;
                }
                $sql .= " AND o.status IN (" . implode(',', $placeholders) . ")";
            }

            // Add sorting
            $validSortColumns = ['order_date', 'total_amount', 'status', 'total_quantity'];
            $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'order_date';
            $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
            
            if ($sortBy === 'total_quantity') {
                $sql = "
                    SELECT o.*, u.first_name, u.last_name, u.email,
                           da.city, da.barangay, da.street, da.zipcode,
                           COALESCE(SUM(oi.quantity), 0) as total_quantity
                    FROM orders o 
                    JOIN users u ON o.user_id = u.id 
                    LEFT JOIN delivery_addresses da ON o.delivery_address_id = da.id
                    LEFT JOIN order_items oi ON o.id = oi.order_id
                    WHERE o.user_id = :user_id
                    GROUP BY o.id, u.first_name, u.last_name, u.email, da.city, da.barangay, da.street, da.zipcode
                    ORDER BY total_quantity {$sortOrder}
                    LIMIT :limit OFFSET :offset
                ";
            } else if ($sortBy === 'total_amount') {
                $sql .= " ORDER BY CAST(o.total_amount AS DECIMAL(10,2)) {$sortOrder}";
                $sql .= " LIMIT :limit OFFSET :offset";
            } else {
                $sql .= " ORDER BY o.{$sortBy} {$sortOrder}";
                $sql .= " LIMIT :limit OFFSET :offset";
            }
            
            $params['limit'] = (int)$limit;
            $params['offset'] = (int)$offset;
            
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                if ($key === 'limit' || $key === 'offset') {
                    $stmt->bindValue(":$key", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":$key", $value);
                }
            }
            
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get items for each order and structure address data
            foreach ($orders as &$order) {
                $order['items'] = $this->getItems($order['id']);
                
                // Structure the delivery address
                $order['delivery_address'] = [
                    'city' => $order['city'] ?? null,
                    'barangay' => $order['barangay'] ?? null,
                    'street' => $order['street'] ?? null,
                    'zipcode' => $order['zipcode'] ?? null
                ];
                
                // Clean up redundant fields
                unset(
                    $order['city'],
                    $order['barangay'],
                    $order['street'],
                    $order['zipcode']
                );
            }
            
            return $orders;
            
        } catch (Exception $e) {
            error_log("Error in getUserOrders: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get order items
     * @param int $orderId Order ID
     * @return array Order items
     */
    private function getOrderItems($orderId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT oi.*, p.name, p.product_image 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = :order_id
            ");
            $stmt->execute(['order_id' => $orderId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error fetching order items: " . $e->getMessage());
            return [];
        }
    }
    
    public function getUserOrdersCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
    
    public function getUserTotalRevenue($userId) {
        $sql = "SELECT COALESCE(SUM(oi.price * oi.quantity), 0) as total 
                FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.status = 'delivered' 
                AND p.created_by = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    public function getMonthlySales($userId, $months = 6) {
        $sql = "SELECT 
                    DATE_FORMAT(o.delivery_date, '%Y-%m') as month,
                    COALESCE(SUM(oi.price * oi.quantity), 0) as total
                FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE o.status = 'delivered'
                AND p.created_by = ?
                AND o.delivery_date >= DATE_SUB(CURRENT_DATE, INTERVAL ? MONTH)
                GROUP BY DATE_FORMAT(o.delivery_date, '%Y-%m')
                ORDER BY month ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $months]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getRecentOrders($userId, $limit = 5) {
        $sql = "SELECT id, order_date, total_amount, status 
                FROM orders 
                WHERE user_id = ? 
                ORDER BY order_date DESC 
                LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update order status
     * @param int $orderId Order ID
     * @param string $status New status
     * @param string|null $notes Optional notes
     * @return bool Success status
     */
    public function updateStatus($orderId, $status, $notes = null) {
        try {
            $updateData = ['status' => $status];
            
            // Set dates based on status
            switch ($status) {
                case 'shipped':
                    $updateData['shipped_date'] = date('Y-m-d H:i:s');
                    break;
                case 'delivered':
                    $updateData['delivery_date'] = date('Y-m-d H:i:s');
                    break;
                case 'cancelled':
                    $updateData['cancelled_date'] = date('Y-m-d H:i:s');
                    break;
            }
            
            if ($notes) {
                $updateData['notes'] = $notes;
            }
            
            $setParts = [];
            $params = [];
            foreach ($updateData as $key => $value) {
                $setParts[] = "$key = :$key";
                $params[$key] = $value;
            }
            $params['id'] = $orderId;
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $setParts) . " WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (Exception $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
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
    
    public function getTotalRevenue() {
        $sql = "SELECT SUM(total_amount) as revenue FROM {$this->table} WHERE status = 'cancelled'";
        $result = db_select_one($sql);
        return (float)($result['revenue'] ?? 0);
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
        
        try {
            $this->pdo->beginTransaction();

            // Get order items before cancelling
            $items = $this->getItems($orderId);

            // Update order status
            $result = $this->updateStatus($orderId, 'cancelled', $reason);
            if (!$result) {
                throw new Exception("Failed to update order status");
            }

            // Restore stock for each item
            foreach ($items as $item) {
                $stmt = $this->pdo->prepare("
                    UPDATE products 
                    SET stock_quantity = stock_quantity + ?
                    WHERE id = ?
                ");
                $stmt->execute([
                    $item['quantity'],
                    $item['product_id']
                ]);
            }

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            error_log("Error cancelling order: " . $e->getMessage());
            return false;
        }
    }

    // Get all orders with pagination for admin view
    public function getAdminPaginated($limit, $offset, $query = null, $status = null, $startDate = null, $endDate = null, $sortBy = 'order_date', $sortOrder = 'DESC') {
        // Changed: Added search, status, and date range filters
        $sql = "SELECT o.*, u.first_name, u.last_name, u.email, da.city, da.barangay, da.street, da.zipcode 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                LEFT JOIN delivery_addresses da ON o.delivery_address_id = da.id
                WHERE 1"; // Start with WHERE 1 to easily append conditions

        $params = [];

        // Add search query filter
        if ($query) {
            $sql .= " AND (o.order_number LIKE ? OR o.id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
        }

        // Add status filter
        if ($status) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }

        // Add date range filter
        if ($startDate) {
            $sql .= " AND o.order_date >= ?";
            $params[] = $startDate . ' 00:00:00'; // Include start of the day
        }
        if ($endDate) {
            $sql .= " AND o.order_date <= ?";
            $params[] = $endDate . ' 23:59:59'; // Include end of the day
        }

        // Add sorting
        $validSortColumns = ['order_date', 'first_name', 'last_name', 'total_amount', 'status'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'order_date';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';

        // Handle numeric sorting for total_amount
        if ($sortBy === 'total_amount') {
            $sql .= " ORDER BY CAST(o.total_amount AS DECIMAL(10,2)) {$sortOrder}";
        } else if ($sortBy === 'first_name' || $sortBy === 'last_name') {
            $sql .= " ORDER BY u.{$sortBy} {$sortOrder}, o.order_date DESC";
        } else {
            $sql .= " ORDER BY o.{$sortBy} {$sortOrder}";
        }

        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Manually add delivery address details if needed (already done in getAll, replicating here)
        foreach ($orders as &$order) {
            // Ensure delivery_address is structured as an array
            $order['delivery_address'] = [
                'city' => $order['city'] ?? null,
                'barangay' => $order['barangay'] ?? null,
                'street' => $order['street'] ?? null,
                'zipcode' => $order['zipcode'] ?? null
            ];
             // Unset redundant keys if necessary (optional)
             unset($order['city'], $order['barangay'], $order['street'], $order['zipcode']);

            // Fetch order items
            $order['items'] = $this->getItems($order['id']);
        }

        return $orders;
    }

    // Get the total count of all orders with search and optional filters for admin view
    public function getAdminTotalCount($query = null, $status = null, $startDate = null, $endDate = null) {
        $sql = "SELECT COUNT(*) 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                LEFT JOIN delivery_addresses da ON o.delivery_address_id = da.id
                WHERE 1"; // Start with WHERE 1 to easily append conditions

        $params = [];

        // Add search query filter
        if ($query) {
            $sql .= " AND (o.order_number LIKE ? OR o.id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
        }

        // Add status filter
        if ($status) {
            $sql .= " AND o.status = ?";
            $params[] = $status;
        }

        // Add date range filter
        if ($startDate) {
            $sql .= " AND o.order_date >= ?";
            $params[] = $startDate . ' 00:00:00'; // Include start of the day
        }
        if ($endDate) {
            $sql .= " AND o.order_date <= ?";
            $params[] = $endDate . ' 23:59:59'; // Include end of the day
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    /**
     * Generate unique order number
     * @return string Order number
     */
    private function generateOrderNumber() {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Sanitize string input
     * @param string $input Input string
     * @return string Sanitized string
     */
    private function sanitizeString($input) {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

    public function getMonthlyRevenue($startDate = null, $endDate = null) {
        $sql = "SELECT 
                    DATE_FORMAT(delivery_date, '%Y-%m') as month,
                    COALESCE(SUM(total_amount), 0) as revenue
                FROM orders 
                WHERE status = 'delivered'";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND delivery_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND delivery_date <= ?";
            $params[] = $endDate . ' 23:59:59';
        }
        
        $sql .= " GROUP BY month ORDER BY month ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get orders for products created by a specific seller
     * @param int $sellerId Seller's user ID
     * @param int $limit Number of records per page
     * @param int $offset Offset for pagination
     * @param array|null $statuses Filter by order statuses
     * @param string $sortBy Column to sort by
     * @param string $sortOrder Sort order (ASC/DESC)
     * @return array Orders with items
     */
    public function getSellerOrders($sellerId, $limit = 10, $offset = 0, $statuses = null, $sortBy = 'order_date', $sortOrder = 'DESC') {
        try {
            $params = ['seller_id' => $sellerId];
            
            // Base query with joins
            $sql = "
                WITH OrderTotals AS (
                    SELECT 
                        o.id,
                        ABS(SUM(oi.quantity)) as total_quantity
                    FROM orders o
                    JOIN order_items oi ON o.id = oi.order_id
                    JOIN products p ON oi.product_id = p.id 
                    WHERE p.created_by = :seller_id
                    GROUP BY o.id
                )
                SELECT DISTINCT o.*, u.first_name, u.last_name, u.email,
                       da.city, da.barangay, da.street, da.zipcode,
                       COALESCE(ot.total_quantity, 0) as total_quantity
                FROM orders o 
                JOIN OrderTotals ot ON o.id = ot.id
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id 
                JOIN users u ON o.user_id = u.id 
                LEFT JOIN delivery_addresses da ON o.delivery_address_id = da.id
                WHERE p.created_by = :seller_id
            ";
            
            // Add status filter if provided
            if ($statuses && is_array($statuses)) {
                $placeholders = [];
                foreach ($statuses as $key => $status) {
                    $paramName = "status$key";
                    $placeholders[] = ":$paramName";
                    $params[$paramName] = $status;
                }
                $sql .= " AND o.status IN (" . implode(',', $placeholders) . ")";
            }

            // Add sorting
            $validSortColumns = ['order_date', 'total_amount', 'status', 'total_quantity'];
            $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'order_date';
            $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
            
            if ($sortBy === 'total_quantity') {
                $sql .= " ORDER BY ABS(total_quantity) {$sortOrder}";
            } else if ($sortBy === 'total_amount') {
                $sql .= " ORDER BY CAST(o.total_amount AS DECIMAL(10,2)) {$sortOrder}";
            } else {
                $sql .= " ORDER BY o.{$sortBy} {$sortOrder}";
            }
            
            $sql .= " LIMIT :limit OFFSET :offset";
            
            $params['limit'] = (int)$limit;
            $params['offset'] = (int)$offset;
            
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get items for each order
            foreach ($orders as &$order) {
                $order['items'] = $this->getItems($order['id']);
                $order['delivery_address'] = [
                    'city' => $order['city'] ?? null,
                    'barangay' => $order['barangay'] ?? null,
                    'street' => $order['street'] ?? null,
                    'zipcode' => $order['zipcode'] ?? null
                ];
                unset($order['city'], $order['barangay'], $order['street'], $order['zipcode']);
            }

            return $orders;
        } catch (Exception $e) {
            error_log("Error fetching seller orders: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of orders for products created by a specific seller
     * @param int $sellerId Seller's user ID
     * @param array|null $statuses Filter by order statuses
     * @return int Total number of orders
     */
    public function getSellerOrdersCount($sellerId, $statuses = null) {
        try {
            $sql = "
                SELECT COUNT(DISTINCT o.id) 
                FROM orders o 
                JOIN order_items oi ON o.id = oi.order_id
                JOIN products p ON oi.product_id = p.id
                WHERE p.created_by = :seller_id
            ";
            
            $params = ['seller_id' => $sellerId];
            
            if ($statuses && is_array($statuses)) {
                $placeholders = [];
                foreach ($statuses as $key => $status) {
                    $paramName = "status$key";
                    $placeholders[] = ":$paramName";
                    $params[$paramName] = $status;
                }
                $sql .= " AND o.status IN (" . implode(',', $placeholders) . ")";
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Error counting seller orders: " . $e->getMessage());
            return 0;
        }
    }
}
?>
