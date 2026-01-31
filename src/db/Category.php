<?php
require_once dirname(__FILE__) . "/BaseModel.php";

class Category extends BaseModel
{
    public string $name;
    public ?string $poster;

    public function __construct(string $name, ?string $poster, ?int $id, ?DateTime $created_at, ?DateTime $updated_at)
    {
        parent::__construct($id, $created_at, $updated_at);
        $this->name = $name;
        $this->poster = $poster;
    }
}

interface CategoryDao
{
    /** @return Category[] */
    public function getAll(): array;
    public function findById(int $id): ?Category;
    public function insert(Category $category): Category;
    public function update(Category $category): Category;
    public function delete(Category $category): Category;
}

class PdoCategoryDao implements CategoryDao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToCategory(array $row): Category
    {
        return new Category(
            $row["name"],
            $row["poster"],
            (int) $row["id"],
            new DateTime($row["created_at"]),
            new DateTime($row["updated_at"]),
        );
    }

    /** @return Category[] */
    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT id, name, poster, created_at, updated_at FROM categories;");
        $stmt->execute();
        $users = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($users, $this->mapRowToCategory($row));
        }
        return $users;
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare("SELECT id, name, poster, created_at, updated_at FROM categories WHERE id = :id");
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToCategory($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function insert(Category $category): Category
    {
        try {
            $this->pdo->beginTransaction();

            $insertStmt = $this->pdo->prepare("INSERT INTO categories (name, poster) VALUES (:name, :poster);");
            $insertStmt->bindParam("name", $category->name, PDO::PARAM_STR);
            $insertStmt->bindParam("poster", $category->poster, PDO::PARAM_NULL | PDO::PARAM_STR);
            $insertStmt->execute();

            $id = $this->pdo->lastInsertId();

            $newCategory = $this->findById($id);

            $this->pdo->commit();

            return $newCategory;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(Category $category): Category
    {
        try {
            $this->pdo->beginTransaction();

            $updateStmt = $this->pdo->prepare("UPDATE categories SET name = :name, poster = :poster WHERE id = :id");
            $updateStmt->bindParam("text", $category->name, PDO::PARAM_STR);
            $updateStmt->bindParam("poster", $category->poster, PDO::PARAM_NULL | PDO::PARAM_STR);
            $id = $category->getId();
            $updateStmt->bindParam("id", $id, PDO::PARAM_INT);
            $updateStmt->execute();

            $updatedCategory = $this->findById($category->getId());

            $this->pdo->commit();

            return $updatedCategory;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(Category $category): Category
    {
        $stmt = $this->pdo->prepare("DELETE FROM categories WHERE id = :id");
        $id = $category->getId();
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $category;
    }
}