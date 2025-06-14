<?php
class Pet
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("
            SELECT p.* 
            FROM pets p 
            LEFT JOIN pet_order_items poi ON p.id = poi.pet_id 
            LEFT JOIN pet_orders po ON poi.order_id = po.id 
            WHERE p.is_adopted = FALSE 
            AND (po.status IS NULL OR po.status != 'pending')
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pets WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getAdoptedPets()
    {
        $stmt = $this->pdo->query("
            SELECT p.*, u.first_name, u.last_name 
            FROM pets p 
            JOIN pet_order_items poi ON p.id = poi.pet_id
            JOIN pet_orders po ON poi.order_id = po.id
            JOIN users u ON po.user_id = u.id 
            WHERE po.status = 'approved'
            ORDER BY po.approved_date DESC
        ");
        return $stmt->fetchAll();
    }

    public function adopt($petId, $userId)
    {
        $stmt = $this->pdo->prepare("UPDATE pets SET is_adopted = TRUE, adopted_by_user_id = ? WHERE id = ?");
        return $stmt->execute([$userId, $petId]);
    }

    public function create($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO pets (name, pet_image, type, gender, age, birthday, breed, description, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['pet_image'], $data['type'], $data['gender'], $data['age'], $data['birthday'], $data['breed'], $data['description'], $data['price']]);
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE pets SET name = ?, pet_image = ?, type = ?, gender = ?, age = ?, birthday = ?, breed = ?, description = ?, price = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['pet_image'], $data['type'], $data['gender'], $data['age'], $data['birthday'], $data['breed'], $data['description'], $data['price'], $id]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM pets WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getStats()
    {
        $stmt = $this->pdo->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN is_adopted = TRUE THEN 1 ELSE 0 END) as adopted,
            SUM(CASE WHEN type = 'dogs' THEN 1 ELSE 0 END) as dogs,
            SUM(CASE WHEN type = 'cats' THEN 1 ELSE 0 END) as cats
            FROM pets");
        return $stmt->fetch();
    }

    public function search($query, $columns = ['name', 'breed'], $limit = 20)
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

        $sql = "SELECT * FROM pets WHERE (" . implode(' OR ', $searchConditions) . ") AND is_adopted = FALSE";

        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByType($type)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pets WHERE type = ? AND is_adopted = FALSE ORDER BY id DESC");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    public function getAvailablePets()
    {
        $stmt = $this->pdo->query("
            SELECT p.* 
            FROM pets p 
            LEFT JOIN pet_order_items poi ON p.id = poi.pet_id 
            LEFT JOIN pet_orders po ON poi.order_id = po.id 
            WHERE p.is_adopted = FALSE 
            AND (po.status IS NULL OR po.status != 'pending')
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function getPetsByGender($gender)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pets WHERE gender = ? AND is_adopted = FALSE ORDER BY id DESC");
        $stmt->execute([$gender]);
        return $stmt->fetchAll();
    }

    public function getPetsByAge($minAge, $maxAge)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM pets WHERE age BETWEEN ? AND ? AND is_adopted = FALSE ORDER BY id DESC");
        $stmt->execute([$minAge, $maxAge]);
        return $stmt->fetchAll();
    }

    // Get pets with pagination, search, and optional filters
    public function getPaginated($limit, $offset, $type = null, $gender = null, $status = null, $minAge = null, $maxAge = null, $searchQuery = '') {
        try {
            error_log("Executing getPaginated with parameters: limit=$limit, offset=$offset, type=$type, gender=$gender, status=$status, minAge=$minAge, maxAge=$maxAge, searchQuery=$searchQuery");
            
            $sql = "SELECT * FROM pets WHERE is_adopted = 0";
            $params = [];
            
            // Add type filter
            if ($type && in_array($type, ['dogs', 'cats'])) {
                $sql .= " AND type = ?";
                $params[] = $type;
            }
            
            // Add gender filter
            if ($gender && in_array($gender, ['male', 'female'])) {
                $sql .= " AND gender = ?";
                $params[] = $gender;
            }
            
            // Add status filter
            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            // Add age range filter
            if ($minAge !== null && $minAge !== '') {
                $sql .= " AND age >= ?";
                $params[] = (int)$minAge;
            }
            if ($maxAge !== null && $maxAge !== '') {
                $sql .= " AND age <= ?";
                $params[] = (int)$maxAge;
            }
            
            // Add search query filter
            if ($searchQuery) {
                $sql .= " AND (name LIKE ? OR breed LIKE ? OR description LIKE ?)";
                $searchParam = "%" . $searchQuery . "%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            $sql .= " ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";
            
            error_log("Final SQL query: " . $sql);
            error_log("Query parameters: " . print_r($params, true));
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Query returned " . count($result) . " pets");
            if (empty($result)) {
                error_log("No pets found matching the criteria");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getPaginated: " . $e->getMessage());
            return [];
        }
    }

    // Get the total count of pets with search and optional filters
    public function getTotalCount($type = null, $gender = null, $status = null, $minAge = null, $maxAge = null, $searchQuery = '') {
        try {
            error_log("Executing getTotalCount with parameters: type=$type, gender=$gender, status=$status, minAge=$minAge, maxAge=$maxAge, searchQuery=$searchQuery");
            
            $sql = "SELECT COUNT(*) as total FROM pets WHERE is_adopted = 0";
            $params = [];
            
            // Add type filter
            if ($type && in_array($type, ['dogs', 'cats'])) {
                $sql .= " AND type = ?";
                $params[] = $type;
            }
            
            // Add gender filter
            if ($gender && in_array($gender, ['male', 'female'])) {
                $sql .= " AND gender = ?";
                $params[] = $gender;
            }
            
            // Add status filter
            if ($status) {
                $sql .= " AND status = ?";
                $params[] = $status;
            }
            
            // Add age range filter
            if ($minAge !== null && $minAge !== '') {
                $sql .= " AND age >= ?";
                $params[] = (int)$minAge;
            }
            if ($maxAge !== null && $maxAge !== '') {
                $sql .= " AND age <= ?";
                $params[] = (int)$maxAge;
            }
            
            // Add search query filter
            if ($searchQuery) {
                $sql .= " AND (name LIKE ? OR breed LIKE ? OR description LIKE ?)";
                $searchParam = "%" . $searchQuery . "%";
                $params[] = $searchParam;
                $params[] = $searchParam;
                $params[] = $searchParam;
            }
            
            error_log("Final SQL query for count: " . $sql);
            error_log("Query parameters for count: " . print_r($params, true));
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            error_log("Total count: " . $result);
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getTotalCount: " . $e->getMessage());
            return 0;
        }
    }

    // Get all pets with pagination, search, and optional filters for admin view
    public function getAdminPaginated($limit, $offset, $query = null, $type = null, $gender = null, $breed = null, $minAge = null, $maxAge = null, $sortBy = 'id', $sortOrder = 'DESC') {
        $sql = "SELECT p.*, u.first_name as owner_name 
                FROM pets p 
                LEFT JOIN users u ON p.adopted_by_user_id = u.id 
                WHERE 1";
            $params = [];

            if ($query) {
            $sql .= " AND (p.name LIKE ? OR p.breed LIKE ? OR p.description LIKE ?)";
            $params[] = "%" . $query . "%";
                $params[] = "%" . $query . "%";
                $params[] = "%" . $query . "%";
            }

        if ($type) {
            $sql .= " AND p.type = ?";
                $params[] = $type;
            }

        if ($gender) {
            $sql .= " AND p.gender = ?";
                $params[] = $gender;
            }

            if ($breed) {
            $sql .= " AND p.breed = ?";
            $params[] = $breed;
            }

        if ($minAge !== null) {
            $sql .= " AND p.age >= ?";
            $params[] = $minAge;
            }

        if ($maxAge !== null) {
            $sql .= " AND p.age <= ?";
            $params[] = $maxAge;
            }

        // Add sorting
        $validSortColumns = ['name', 'type', 'breed', 'age', 'price', 'id'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'id';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        // Ensure proper table alias in ORDER BY clause
        $sql .= " ORDER BY p.{$sortBy} {$sortOrder}";
        
        // Add LIMIT and OFFSET directly in the SQL string
        $sql .= " LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminTotalCount($query = null, $type = null, $gender = null, $breed = null, $minAge = null, $maxAge = null) {
        try {
            error_log("Executing getAdminTotalCount with parameters: query=$query, type=$type, gender=$gender, breed=$breed, minAge=$minAge, maxAge=$maxAge");
            
            $sql = "SELECT COUNT(*) as total FROM pets WHERE 1=1";
            $params = [];

            // Add search query filter (by name or breed)
            if ($query) {
                $sql .= " AND (name LIKE ? OR breed LIKE ?)";
                $params[] = "%" . $query . "%";
                $params[] = "%" . $query . "%";
            }

            // Add type filter
            if ($type && in_array($type, ['dogs', 'cats'])) {
                $sql .= " AND type = ?";
                $params[] = $type;
            }

            // Add gender filter
            if ($gender && in_array($gender, ['male', 'female'])) {
                $sql .= " AND gender = ?";
                $params[] = $gender;
            }

            // Add breed filter
            if ($breed) {
                $sql .= " AND breed LIKE ?";
                $params[] = "%" . $breed . "%";
            }

            // Add age range filter
            if ($minAge !== null && $minAge !== '') {
                $sql .= " AND age >= ?";
                $params[] = (int)$minAge;
            }
            if ($maxAge !== null && $maxAge !== '') {
                $sql .= " AND age <= ?";
                $params[] = (int)$maxAge;
            }
            
            error_log("Final SQL query for count: " . $sql);
            error_log("Query parameters for count: " . print_r($params, true));
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            error_log("Total count: " . $result);
            return $result;
        } catch (PDOException $e) {
            error_log("Database error in getAdminTotalCount: " . $e->getMessage());
            return 0;
        }
    }

    public function getAllPets() {
        $sql = "SELECT * FROM pets ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get pets with pagination, search, and optional filters for user view
    public function getPetsWithFiltersAndPagination($query = null, $species = null, $limit, $offset)
    {
        // Reuse the existing getPaginated method, passing only relevant filters
        return $this->getPaginated($limit, $offset, $species, null, null, null, null, $query);
    }

    // Get the total count of pets with search and optional filters for user view
    public function getTotalPetsWithFilters($query = null, $species = null)
    {
        // Reuse the existing getTotalCount method, passing only relevant filters
        return $this->getTotalCount($species, null, null, null, null, $query);
    }

    public function updateStatus($petId, $status) {
        $stmt = $this->pdo->prepare("
            UPDATE pets 
            SET is_adopted = ?, 
                adopted_by_user_id = CASE WHEN ? = 'adopted' THEN ? ELSE NULL END
            WHERE id = ?
        ");
        return $stmt->execute([
            $status === 'adopted' ? 1 : 0,
            $status,
            $_SESSION['user_id'] ?? null,
            $petId
        ]);
    }

    public function updatePetStatus($petId, $status, $adoptedBy = null)
    {
        $sql = "UPDATE pets SET status = ?, adopted_by = ? WHERE id = ?";
        $params = [$status, $adoptedBy, $petId];
        
        return $this->pdo->prepare($sql)->execute($params);
    }

    public function getAdoptedPetsByUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT p.*, po.created_at as adoption_date, po.status as order_status
            FROM pets p 
            JOIN pet_order_items poi ON p.id = poi.pet_id
            JOIN pet_orders po ON poi.order_id = po.id
            WHERE po.user_id = ? AND po.status = 'approved'
            ORDER BY po.approved_date DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getMonthlyAdoptions($startDate = null, $endDate = null) {
        $sql = "SELECT 
                    DATE_FORMAT(po.created_at, '%Y-%m') as month,
                    COUNT(*) as count
                FROM pet_orders po
                WHERE po.status = 'approved'";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND po.created_at >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND po.created_at <= ?";
            $params[] = $endDate . ' 23:59:59';
        }
        
        $sql .= " GROUP BY month ORDER BY month ASC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getUserPaginated($limit, $offset, $type = null, $gender = null, $breed = null, $minAge = null, $maxAge = null, $sortBy = 'created_at', $sortOrder = 'DESC') {
        $sql = "SELECT * FROM pets WHERE is_adopted = 0";
        $params = [];

        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }

        if ($gender) {
            $sql .= " AND gender = ?";
            $params[] = $gender;
        }

        if ($breed) {
            $sql .= " AND breed = ?";
            $params[] = $breed;
        }

        if ($minAge !== null) {
            $sql .= " AND age >= ?";
            $params[] = $minAge;
        }

        if ($maxAge !== null) {
            $sql .= " AND age <= ?";
            $params[] = $maxAge;
        }

        // Add sorting
        $validSortColumns = ['name', 'type', 'breed', 'age', 'price', 'created_at'];
        $sortBy = in_array($sortBy, $validSortColumns) ? $sortBy : 'created_at';
        $sortOrder = strtoupper($sortOrder) === 'ASC' ? 'ASC' : 'DESC';
        
        $sql .= " ORDER BY {$sortBy} {$sortOrder}";
        $sql .= " LIMIT ? OFFSET ?";
        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPetTypeDistribution() {
        $sql = "SELECT 
                    type,
                    COUNT(*) as count,
                    SUM(CASE WHEN is_adopted = 1 THEN 1 ELSE 0 END) as adopted_count
                FROM pets 
                GROUP BY type";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
