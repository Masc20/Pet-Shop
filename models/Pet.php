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
}
