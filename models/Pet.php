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
        $stmt = $this->pdo->query("SELECT * FROM pets WHERE is_adopted = FALSE ORDER BY id DESC");
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
            JOIN users u ON p.adopted_by_user_id = u.id 
            WHERE p.is_adopted = TRUE 
            ORDER BY p.id DESC
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
        $stmt = $this->pdo->prepare("INSERT INTO pets (name, pet_image, type, gender, age, breed) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$data['name'], $data['pet_image'], $data['type'], $data['gender'], $data['age'], $data['breed']]);
    }

    public function update($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE pets SET name = ?, pet_image = ?, type = ?, gender = ?, age = ?, breed = ? WHERE id = ?");
        return $stmt->execute([$data['name'], $data['pet_image'], $data['type'], $data['gender'], $data['age'], $data['breed'], $id]);
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
        $stmt = $this->pdo->query("SELECT * FROM pets WHERE is_adopted = FALSE ORDER BY id DESC");
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
    public function getPaginated($limit, $offset, $type = null, $gender = null, $breed = null, $minAge = null, $maxAge = null, $query = null) {
        $sql = "SELECT * FROM pets WHERE is_adopted = FALSE";
        $params = [];

        // Add type filter
        if ($type && in_array($type, ['dog', 'cat'])) {
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
            $params[] = $minAge;
        }
        if ($maxAge !== null && $maxAge !== '') {
            $sql .= " AND age <= ?";
            $params[] = $maxAge;
        }

        // Add search query filter (by name or breed)
        if ($query) {
            $sql .= " AND (name LIKE ? OR breed LIKE ?)";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
        }

        $sql .= " ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get the total count of pets with search and optional filters
    public function getTotalCount($type = null, $gender = null, $breed = null, $minAge = null, $maxAge = null, $query = null) {
        $sql = "SELECT COUNT(*) FROM pets WHERE is_adopted = FALSE";
        $params = [];

        // Add type filter
        if ($type && in_array($type, ['dog', 'cat'])) {
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
            $params[] = $minAge;
        }
        if ($maxAge !== null && $maxAge !== '') {
            $sql .= " AND age <= ?";
            $params[] = $maxAge;
        }

        // Add search query filter (by name or breed)
        if ($query) {
            $sql .= " AND (name LIKE ? OR breed LIKE ?)";
            $params[] = "%" . $query . "%";
            $params[] = "%" . $query . "%";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    // Get all pets with pagination, search, and optional filters for admin view
    public function getAdminPaginated($limit, $offset, $query = null, $type = null, $gender = null, $breed = null, $minAge = null, $maxAge = null) {
        $sql = "SELECT * FROM pets WHERE 1"; // Start with WHERE 1 to easily append conditions
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
            $params[] = $minAge;
        }
        if ($maxAge !== null && $maxAge !== '') {
            $sql .= " AND age <= ?";
            $params[] = $maxAge;
        }

        $sql .= " ORDER BY id DESC LIMIT {$limit} OFFSET {$offset}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Get the total count of all pets with search and optional filters for admin view
    public function getAdminTotalCount($query = null, $type = null, $gender = null, $breed = null, $minAge = null, $maxAge = null) {
        $sql = "SELECT COUNT(*) FROM pets WHERE 1"; // Start with WHERE 1
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
            $params[] = $minAge;
        }
        if ($maxAge !== null && $maxAge !== '') {
            $sql .= " AND age <= ?";
            $params[] = $maxAge;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
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
}
