<?php
class Product {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM products ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByType($type) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE type = ? ORDER BY id DESC");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO products (name, product_image, stock_quantity, type, price, description, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['name'], 
            $data['product_image'], 
            $data['stock_quantity'], 
            $data['type'], 
            $data['price'], 
            $data['description'],
            $data['created_by'] ?? null
        ]);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE products SET 
                name = ?, 
                product_image = ?, 
                stock_quantity = ?, 
                type = ?, 
                price = ?, 
                description = ? 
                WHERE id = ?";
                
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['product_image'],
            $data['stock_quantity'],
            $data['type'],
            $data['price'],
            $data['description'],
            $id
        ]);
    }
    
    public function updateStock($id, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }
    
    public function restoreStock($id, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }
    
    public function getStats() {
        $stmt = $this->pdo->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN type = 'accessories' THEN 1 ELSE 0 END) as accessories,
            SUM(CASE WHEN type = 'foods' THEN 1 ELSE 0 END) as foods,
            SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock
            FROM products");
        return $stmt->fetch();
    }

    public function search($query, $columns = ['name', 'description'], $limit = 20) {
        if (empty($columns) || empty($query)) {
            return [];
        }

        $searchConditions = [];
        $params = [];

        foreach ($columns as $i => $column) {
            $paramName = 'search_' . $i;
            $searchConditions[] = "{$column} LIKE :{$paramName}";
            $params[$paramName] = "%{$query}%";
        }

        $sql = "SELECT * FROM products WHERE " . implode(' OR ', $searchConditions);

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getPaginated($limit, $offset, $categoryId = null, $minPrice = null, $maxPrice = null, $query = null, $sortBy = 'id', $sortOrder = 'DESC') {
        $sql = "SELECT p.*
                FROM products p  
                WHERE 1=1";
        $params = [];

        if ($query) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
        }

        if ($minPrice !== null) {
            $sql .= " AND p.price >= ?";
            $params[] = $minPrice;
        }

        if ($maxPrice !== null) {
            $sql .= " AND p.price <= ?";
            $params[] = $maxPrice;
        }

        // Add sorting
        $validSortColumns = ['name', 'price', 'stock_quantity', 'id', 'created_at'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'id';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql .= " ORDER BY p.{$sortBy} {$sortOrder}";
        
        // Add LIMIT and OFFSET
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalCount($type = null, $minPrice = null, $maxPrice = null, $query = null, $stockStatus = null) {
        $sql = "SELECT COUNT(*) FROM products WHERE 1=1";
        $params = [];

        if ($type && in_array($type, ['foods', 'accessories'])) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }

        if ($minPrice !== null && $minPrice !== '') {
            $sql .= " AND price >= ?";
            $params[] = $minPrice;
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $sql .= " AND price <= ?";
            $params[] = $maxPrice;
        }

        if ($stockStatus) {
            switch ($stockStatus) {
                case 'in_stock':
                    $sql .= " AND stock_quantity > 5";
                    break;
                case 'low_stock':
                    $sql .= " AND stock_quantity > 0 AND stock_quantity <= 5";
                    break;
                case 'out_of_stock':
                    $sql .= " AND stock_quantity = 0";
                    break;
            }
        }

        if ($query) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getAllProducts() {
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeaturedProducts() {
        // First, get top sold products by type that are in stock
        $sql = "WITH RankedProducts AS (
                    SELECT p.*, 
                           COALESCE(SUM(oi.quantity), 0) as total_sold,
                           ROW_NUMBER() OVER (PARTITION BY p.type ORDER BY COALESCE(SUM(oi.quantity), 0) DESC) as type_rank
                    FROM products p
                    LEFT JOIN order_items oi ON p.id = oi.product_id 
                    LEFT JOIN orders o ON oi.order_id = o.id AND o.status IN ('delivered', 'shipped')
                    WHERE p.stock_quantity > 0
                    GROUP BY p.id, p.type
                )
                SELECT * FROM RankedProducts 
                WHERE type_rank <= 2
                ORDER BY total_sold DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $soldProducts = $stmt->fetchAll();
        
        // If we have less than 4 products with sales, get additional products that are in stock
        if (count($soldProducts) < 4) {
            $needed = 4 - count($soldProducts);
            $soldProductIds = array_column($soldProducts, 'id');
            
            // Get additional products that haven't been sold yet and are in stock
            if (empty($soldProductIds)) {
                // If no sold products, just get the newest in-stock products
                $sql = "SELECT p.*, 0 as total_sold 
                        FROM products p 
                        WHERE p.stock_quantity > 0
                        ORDER BY p.id DESC 
                        LIMIT " . (int)$needed;
            } else {
                $placeholders = str_repeat('?,', count($soldProductIds) - 1) . '?';
                $sql = "SELECT p.*, 0 as total_sold 
                        FROM products p 
                        WHERE p.id NOT IN ($placeholders)
                        AND p.stock_quantity > 0
                        ORDER BY p.id DESC 
                        LIMIT " . (int)$needed;
            }
            
            $stmt = $this->pdo->prepare($sql);
            if (!empty($soldProductIds)) {
                $stmt->execute($soldProductIds);
            } else {
                $stmt->execute();
            }
            $additionalProducts = $stmt->fetchAll();
            
            // Combine sold and additional products
            return array_merge($soldProducts, $additionalProducts);
        }
        
        return $soldProducts;
    }

    public function getTopOutOfStockProducts($limit = 4) {
        $sql = "SELECT * FROM products 
                WHERE stock_quantity < 5 
                ORDER BY stock_quantity ASC 
                LIMIT " . (int)$limit;
                
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getProductSales($startDate = null, $endDate = null) {
        $sql = "SELECT 
                    p.name,
                    SUM(oi.quantity) as total_sold
                FROM products p
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id
                WHERE o.status = 'delivered'";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND o.order_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND o.order_date <= ?";
            $params[] = $endDate . ' 23:59:59';
        }
        
        $sql .= " GROUP BY p.id, p.name
                  ORDER BY total_sold DESC
                  LIMIT 5";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductTypeDistribution($userId = null) {
        if ($userId === null) {
            // Admin dashboard - get all products
            $sql = "SELECT type, COUNT(*) as count FROM products GROUP BY type";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
        } else {
            // User dashboard - get user's products
            $sql = "SELECT type, COUNT(*) as count FROM products WHERE created_by = ? GROUP BY type";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserProducts($userId, $limit = null, $offset = null, $search = '', $type = '', $stockStatus = '', $sortBy = 'id', $sortOrder = 'DESC') {
        $sql = "SELECT * FROM products WHERE created_by = ?";
        $params = [$userId];
        
        // Add search condition
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        // Add type filter
        if (!empty($type)) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        
        // Add stock status filter
        if (!empty($stockStatus)) {
            switch ($stockStatus) {
                case 'in_stock':
                    $sql .= " AND stock_quantity > 5";
                    break;
                case 'low_stock':
                    $sql .= " AND stock_quantity > 0 AND stock_quantity <= 5";
                    break;
                case 'out_of_stock':
                    $sql .= " AND stock_quantity = 0";
                    break;
            }
        }
        
        // Add sorting
        $validSortColumns = ['name', 'price', 'stock_quantity', 'id'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'id';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql .= " ORDER BY {$sortBy} {$sortOrder}";
        
        if ($limit !== null) {
            $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUserProductsCount($userId, $search = '', $type = '', $stockStatus = '') {
        $sql = "SELECT COUNT(*) FROM products WHERE created_by = ?";
        $params = [$userId];
        
        // Add search condition
        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        // Add type filter
        if (!empty($type)) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        
        // Add stock status filter
        if (!empty($stockStatus)) {
            switch ($stockStatus) {
                case 'in_stock':
                    $sql .= " AND stock_quantity > 5";
                    break;
                case 'low_stock':
                    $sql .= " AND stock_quantity > 0 AND stock_quantity <= 5";
                    break;
                case 'out_of_stock':
                    $sql .= " AND stock_quantity = 0";
                    break;
            }
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function delete($id, $userId = null) {
        $sql = "DELETE FROM products WHERE id = ?";
        $params = [$id];
        
        if ($userId !== null) {
            $sql .= " AND created_by = ?";
            $params[] = $userId;
        }
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function getLowStockProductsCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM products WHERE created_by = ? AND stock_quantity < 5";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function getLowStockProducts($userId, $limit = 5) {
        $sql = "SELECT id, name, type, stock_quantity FROM products 
                WHERE created_by = ? AND stock_quantity < 5 
                ORDER BY stock_quantity ASC LIMIT ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
