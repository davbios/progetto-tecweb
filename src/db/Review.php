<?php
require_once dirname(__FILE__) . "/BaseModel.php";

class Review extends BaseModel
{
    public string $text;
    public int $rate;
    private User $author;
    // N.B.:
    // viene utilizzato `drinkId` nel modello al posto dell'oggetto `Drink` 
    // per allegerire le query, considerando che l'accesso alla tabella 
    // delle review (modifica/visualizzazione) viene fatto principalmente 
    // dalla pagina del drink, dove l'oggetto drink e' gia' a disposizione.
    private int $drinkId;

    public function __construct(string $text, int $rate, User $author, int $drinkId, ?int $id, ?DateTime $created_at, ?DateTime $updated_at)
    {
        parent::__construct($id, $created_at, $updated_at);
        $this->text = $text;
        $this->rate = $rate;
        $this->author = $author;
        $this->drinkId = $drinkId;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getDrinkId(): int
    {
        return $this->drinkId;
    }
}

interface ReviewDao
{
    /** @return Review[] */
    public function getAllForDrink(int $drinkId): array;
    public function findById(int $id): ?Review;
    public function findByUserId(int $userId): ?Review;
    public function insert(Review $review): Review;
    public function update(Review $review): Review;
    public function delete(Review $review): Review;
}

class PdoReviewDao implements ReviewDao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToReview(array $row): Review
    {
        return new Review(
            $row["text"],
            (int) $row["rate"],
            new User(
                $row["user__username"],
                $row["user__email"],
                $row["user__password"],
                $row["user__is_admin"],
                (int) $row["user_id"],
                new DateTime($row["user__created_at"]),
                new DateTime($row["user__updated_at"])
            ),
            (int) $row["drink_id"],
            (int) $row["id"],
            new DateTime($row["created_at"]),
            new DateTime($row["updated_at"]),
        );
    }

    /** @return Review[] */
    public function getAllForDrink(int $drinkId): array
    {
        $stmt = $this->pdo->prepare("SELECT R.id AS id, R.text AS text, R.rate AS rate, 
        R.user_id AS user_id, R.created_at AS created_at, R.updated_at AS updated_at, 
        R.drink_id AS drink_id, U.username AS user__username, U.email AS user__email, 
        U.password AS user__password, U.is_admin AS user__is_admin, 
        U.created_at AS user__created_at, U.updated_at AS user__updated_at 
        FROM reviews R JOIN users U ON R.user_id = U.id WHERE R.drink_id = :id 
        ORDER BY R.created_at DESC");
        $stmt->bindParam("id", $drinkId, PDO::PARAM_INT);
        $stmt->execute();
        $reviews = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($reviews, $this->mapRowToReview($row));
        }
        return $reviews;
    }

    public function findById(int $id): ?Review
    {
        $stmt = $this->pdo->prepare("SELECT R.id AS id, R.text AS text, R.rate AS rate, 
        R.user_id AS user_id, R.created_at AS created_at, R.updated_at AS updated_at, 
        R.drink_id AS drink_id, U.username AS user__username, U.email AS user__email, 
        U.password AS user__password, U.is_admin AS user__is_admin, 
        U.created_at AS user__created_at, U.updated_at AS user__updated_at 
        FROM reviews R JOIN users U ON R.user_id = U.id WHERE R.id = :id");
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToReview($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function findByUserId(int $userId): ?Review
    {
        $stmt = $this->pdo->prepare("SELECT R.id AS id, R.text AS text, R.rate AS rate, 
        R.user_id AS user_id, R.created_at AS created_at, R.updated_at AS updated_at, 
        R.drink_id AS drink_id, U.username AS user__username, U.email AS user__email, 
        U.password AS user__password, U.is_admin AS user__is_admin, 
        U.created_at AS user__created_at, U.updated_at AS user__updated_at 
        FROM reviews R JOIN users U ON R.user_id = U.id WHERE user_id = :id");
        $stmt->bindParam("id", $userId, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToReview($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function insert(Review $review): Review
    {
        try {
            $this->pdo->beginTransaction();

            $insertStmt = $this->pdo->prepare("INSERT INTO reviews (text, rate, user_id, drink_id) VALUES (:text, :rate, :user_id, :drink_id);");
            $insertStmt->bindParam("text", $review->text, PDO::PARAM_STR);
            $insertStmt->bindParam("rate", $review->rate, PDO::PARAM_INT);
            $userId = $review->getAuthor()->getId();
            $insertStmt->bindParam("user_id", $userId, PDO::PARAM_INT);
            $drinkId = $review->getDrinkId();
            $insertStmt->bindParam("drink_id", $drinkId, PDO::PARAM_INT);
            $insertStmt->execute();

            $id = $this->pdo->lastInsertId();

            $newReview = $this->findById($id);

            $this->pdo->commit();

            return $newReview;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(Review $review): Review
    {
        try {
            $this->pdo->beginTransaction();

            $updateStmt = $this->pdo->prepare("UPDATE reviews SET text = :text, rate = :rate WHERE id = :id");
            $updateStmt->bindParam("text", $review->text, PDO::PARAM_STR);
            $updateStmt->bindParam("rate", $review->rate, PDO::PARAM_INT);
            $id = $review->getId();
            $updateStmt->bindParam("id", $id, PDO::PARAM_INT);
            $updateStmt->execute();

            $updatedReview = $this->findById($review->getId());

            $this->pdo->commit();

            return $updatedReview;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(Review $review): Review
    {
        $stmt = $this->pdo->prepare("DELETE FROM reviews WHERE id = :id");
        $id = $review->getId();
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $review;
    }
}