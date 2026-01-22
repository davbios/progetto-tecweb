<?php
require_once dirname(__FILE__) . "/BaseModel.php";

class Ingredient extends BaseModel
{
    public string $name;

    public string $quantity;

    // N.B.:
    // vedi Review#$drinkId
    private int $drinkId;

    public function __construct(string $name, string $quantity, int $drinkId, ?int $id, ?DateTime $created_at, ?DateTime $updated_at)
    {
        parent::__construct($id, $created_at, $updated_at);
        $this->name = $name;
        $this->quantity = $quantity;
        $this->drinkId = $drinkId;
    }

    public function getDrinkId(): int
    {
        return $this->drinkId;
    }
}

interface IngredientDao
{
    /** @return Ingredient[] */
    public function getAllForDrink(int $drinkId): array;
    public function findById(int $id): ?Ingredient;
    public function insert(Ingredient $ingredient): Ingredient;
    public function update(Ingredient $ingredient): Ingredient;
    public function delete(Ingredient $ingredient): Ingredient;
}

class PdoIngredientDao implements IngredientDao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToIngredient(array $row): Ingredient
    {
        return new Ingredient(
            $row["name"],
            $row["quantity"],
            (int) $row["drink_id"],
            (int) $row["id"],
            new DateTime($row["created_at"]),
            new DateTime($row["updated_at"]),
        );
    }

    /** @return Ingredient[] */
    public function getAllForDrink(int $drinkId): array
    {
        $stmt = $this->pdo->prepare("SELECT id, name, quantity, drink_id, created_at, updated_at FROM ingredients WHERE drink_id = :drinkId");
        $stmt->bindParam("drinkId", $drinkId, PDO::PARAM_INT);
        $stmt->execute();
        $ingredients = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($ingredients, $this->mapRowToIngredient($row));
        }
        return $ingredients;
    }

    public function findById(int $id): ?Ingredient
    {
        $stmt = $this->pdo->prepare("SELECT name, id, created_at, updated_at FROM ingredients WHERE id = :id");
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToIngredient($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function insert(Ingredient $ingredient): Ingredient
    {
        try {
            $this->pdo->beginTransaction();

            $insertStmt = $this->pdo->prepare("INSERT INTO ingredients (name, quantity, drink_id) VALUES (:name, :quantity, :drinkId);");
            $insertStmt->bindParam("name", $ingredient->name, PDO::PARAM_STR);
            $insertStmt->bindParam("quantity", $ingredient->quantity, PDO::PARAM_STR);
            $drinkId = $ingredient->getDrinkId();
            $insertStmt->bindParam("drinkId", $drinkId, PDO::PARAM_INT);
            $insertStmt->execute();

            $id = $this->pdo->lastInsertId();

            $newIngredient = $this->findById($id);

            $this->pdo->commit();

            return $newIngredient;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(Ingredient $ingredient): Ingredient
    {
        try {
            $this->pdo->beginTransaction();

            $updateStmt = $this->pdo->prepare("UPDATE ingredients SET name = :name, quantity = :quantity WHERE id = :id");
            $updateStmt->bindParam("name", $ingredient->name, PDO::PARAM_STR);
            $updateStmt->bindParam("quantity", $ingredient->quantity, PDO::PARAM_STR);
            $id = $ingredient->getId();
            $updateStmt->bindParam("id", $id, PDO::PARAM_INT);
            $updateStmt->execute();

            $updatedIngredient = $this->findById($id);

            $this->pdo->commit();

            return $updatedIngredient;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(Ingredient $ingredient): Ingredient
    {
        $stmt = $this->pdo->prepare("DELETE FROM ingredients WHERE id = :id");
        $id = $ingredient->getId();
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $ingredient;
    }
}