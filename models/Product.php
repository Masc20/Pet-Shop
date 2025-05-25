<?php
class Product {
    private $pdo;
    
    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM products WHERE is_archived = FALSE ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getByType($type) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE type = ? AND is_archived = FALSE ORDER BY id DESC");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
    
    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO products (name, product_image, stock_quantity, type, price, description) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['product_image'], $data['stock_quantity'], $data['type'], $data['price'], $data['description']]);
    }
    
    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE products SET name = ?, product_image = ?, stock_quantity = ?, type = ?, price = ?, description = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['product_image'], $data['stock_quantity'], $data['type'], $data['price'], $data['description'], $id]);
    }
    
    public function archive($id) {
        $stmt = $this->pdo->prepare("UPDATE products SET is_archived = TRUE WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function restore($id) {
        $stmt = $this->pdo->prepare("UPDATE products SET is_archived = FALSE WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getArchived() {
        $stmt = $this->pdo->query("SELECT * FROM products WHERE is_archived = TRUE ORDER BY id DESC");
        return $stmt->fetchAll();
    }
    
    public function updateStock($id, $quantity) {
        $stmt = $this->pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?");
        return $stmt->execute([$quantity, $id]);
    }
    
    public function getStats() {
        $stmt = $this->pdo->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN type = 'accessories' THEN 1 ELSE 0 END) as accessories,
            SUM(CASE WHEN type = 'foods' THEN 1 ELSE 0 END) as foods,
            SUM(CASE WHEN stock_quantity = 0 THEN 1 ELSE 0 END) as out_of_stock
            FROM products WHERE is_archived = FALSE");
        return $stmt->fetch();
    }

    public function search($query, $columns = ['name', 'description'], $limit = 20)
    {
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

        $sql = "SELECT * FROM pets WHERE (" . implode(' OR ', $searchConditions) . ") AND is_archived = FALSE";

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
?>
