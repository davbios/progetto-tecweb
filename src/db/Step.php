<?php
require_once dirname(__FILE__) . "/BaseModel.php";

class Step extends BaseModel
{
    public int $number;
    public string $description;
    // N.B.:
    // vedi Review#$drinkId
    private int $drinkId;

    public function __construct(int $number, string $description, int $drinkId, ?int $id, ?DateTime $created_at, ?DateTime $updated_at)
    {
        parent::__construct($id, $created_at, $updated_at);
        $this->number = $number;
        $this->description = $description;
        $this->drinkId = $drinkId;
    }

    public function getDrinkId(): int {
        return $this->drinkId;
    }
}

interface StepDao
{
    /** @return Step[] */
    public function getAllForDrink(int $drinkId): array;
    public function findById(int $id): ?Step;
    public function insert(Step $step): Step;
    public function update(Step $step): Step;
    public function delete(Step $step): Step;
}

class PdoStepDao implements StepDao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToStep(array $row): Step
    {
        return new Step(
            $row["num"],
            $row["description"],
            (int) $row["drinkId"],
            (int) $row["id"],
            new DateTime($row["created_at"]),
            new DateTime($row["updated_at"]),
        );
    }

    /** @return Step[] */
    public function getAllForDrink(int $drinkId): array
    {
        $stmt = $this->pdo->prepare("SELECT id, num, description, drink_id, created_at, updated_at FROM steps 
        WHERE drink_id = :id ORDER BY num ASC");
        $stmt->bindParam("id", $drinkId, PDO::PARAM_INT);
        $stmt->execute();
        $steps = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($steps, $this->mapRowToStep($row));
        }
        return $steps;
    }

    public function findById(int $id): ?Step
    {
        $stmt = $this->pdo->prepare("SELECT id, num, description, drink_id, created_at, updated_at FROM steps WHERE id = :id");
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToStep($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function insert(Step $step): Step
    {
        try {
            $this->pdo->beginTransaction();

            $insertStmt = $this->pdo->prepare("INSERT INTO steps (num, description, drink_id) VALUES (:num, :description, :drinkId);");
            $insertStmt->bindParam("num", $step->number, PDO::PARAM_INT);
            $insertStmt->bindParam("description", $step->description, PDO::PARAM_STR);
            $drinkId = $step->getDrinkId();
            $insertStmt->bindParam("drinkId", $drinkId, PDO::PARAM_INT);
            $insertStmt->execute();

            $id = $this->pdo->lastInsertId();

            $newStep = $this->findById($id);

            $this->pdo->commit();

            return $newStep;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(Step $step): Step
    {
        $updateStmt = $this->pdo->prepare("UPDATE steps SET description = :description WHERE drink_id = :drinkId AND num = :num");
        $updateStmt->bindParam("description", $step->description, PDO::PARAM_STR);
        $drinkId = $step->getDrinkId();
        $updateStmt->bindParam("drinkId", $drinkId, PDO::PARAM_INT);
        $updateStmt->bindParam("num", $step->number, PDO::PARAM_STR);
        $updateStmt->execute();
        return $step;
    }

    public function delete(Step $step): Step
    {
        $stmt = $this->pdo->prepare("DELETE FROM steps WHERE id = :id");
        $id = $step->getId();
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $step;
    }
}