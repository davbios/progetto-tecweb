<?php
require_once dirname(__FILE__) . "/BaseModel.php";

class Ingredient extends BaseModel
{
    public string $name;

    public ?int $quantity;

    public function __construct(string $name, ?int $quantity, ?int $id, ?DateTime $created_at, ?DateTime $updated_at)
    {
        parent::__construct($id, $created_at, $updated_at);
        $this->name = $name;
        $this->quantity = $quantity;
    }
}

interface IngredientDao
{
    public function getAll(): array;
    public function getAllForDrink(int $drinkId): array;
    public function findById(int $id): ?Ingredient;
    public function addToDrink(int $drinkId, int $ingredientId, int $quanity): void;
    public function removeFromDrink(int $drinkId, int $ingredientId): void;
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
            isset($row["quantity"]) ? (int) $row["quantity"] : null,
            (int) $row["id"],
            new DateTime($row["created_at"]),
            new DateTime($row["updated_at"]),
        );
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT name, id, created_at, updated_at FROM ingredients");
        $stmt->execute();
        $ingredients = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($ingredients, $this->mapRowToIngredient($row));
        }
        return $ingredients;
    }

    public function getAllForDrink(int $drinkId): array
    {
        $stmt = $this->pdo->prepare("SELECT I.name AS name, I.id AS id, I.created_at AS created_at, 
        I.updated_at AS updated_at, DI.quantity AS quantity 
        FROM drinks_ingredients DI
        JOIN ingrediens I ON DI.ingredient_id = I.id
        WHERE DI.drink_id = :id");
        $stmt->bindParam("id", $drinkId, PDO::PARAM_INT);
        $stmt->execute();
        $ingredients = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($ingredients, $this->mapRowToIngredient($row));
        }
        return $ingredients;
    }

    public function findById(int $id): ?Ingredient
    {
        $stmt = $this->pdo->prepare("SELECT name, id, created_at, updated_at WHERE id = :id");
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToIngredient($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function addToDrink(int $drinkId, int $ingredientId, int $quantity): void
    {
        $insertStmt = $this->pdo->prepare("INSERT INTO drinks_ingredients (drink_id, ingredient_id, quantity) VALUES (:drink_id, :ingredient_id, :quantity);");
        $insertStmt->bindParam("drink_id", $drinkId, PDO::PARAM_INT);
        $insertStmt->bindParam("ingredient_id", $ingredientId, PDO::PARAM_INT);
        $insertStmt->bindParam("quantity", $quantity, PDO::PARAM_INT);
        $insertStmt->execute();
    }

    public function removeFromDrink(int $drinkId, int $ingredientId): void
    {
        $insertStmt = $this->pdo->prepare("DELETE FROM drinks_ingredients WHERE drink_id = :drink_id AND ingredient_id = :ingredient_id;");
        $insertStmt->bindParam("drink_id", $drinkId, PDO::PARAM_INT);
        $insertStmt->bindParam("ingredient_id", $ingredientId, PDO::PARAM_INT);
        $insertStmt->execute();
    }

    public function insert(Ingredient $ingredient): Ingredient
    {
        try {
            $this->pdo->beginTransaction();

            $insertStmt = $this->pdo->prepare("INSERT INTO ingredients (name) VALUES (:name);");
            $insertStmt->bindParam("name", $ingredient->name, PDO::PARAM_STR);
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

            $updateStmt = $this->pdo->prepare("UPDATE ingredients SET name = :name WHERE id = :id");
            $updateStmt->bindParam("name", $ingredient->name, PDO::PARAM_STR);
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