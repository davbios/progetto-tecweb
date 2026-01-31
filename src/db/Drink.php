<?php
require_once dirname(__FILE__) . "/BaseModel.php";

class Drink extends BaseModel
{
    public string $name;
    public string $description;
    public string $poster;
    private User $creator;
    private ?float $avgRating;
    public ?Category $category;

    public function __construct(string $name, string $description, string $poster, User $creator, ?float $avgRating, ?Category $category, ?int $id, ?DateTime $created_at, ?DateTime $updated_at)
    {
        parent::__construct($id, $created_at, $updated_at);
        $this->name = $name;
        $this->description = $description;
        $this->poster = $poster;
        $this->creator = $creator;
        $this->avgRating = $avgRating;
        $this->category = $category;
    }

    public function getCreator(): User
    {
        return $this->creator;
    }

    public function getAvgRating(): ?float
    {
        return $this->avgRating;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function isOfficial(): bool
    {
        return $this->creator->isAdmin();
    }
}

class DrinkList
{
    public array $drinks;
    public int $count;

    public function __construct(array $drinks, int $count)
    {
        $this->drinks = $drinks;
        $this->count = $count;
    }
}

interface DrinkDao
{
    public function getAllAndCount(int $limit = 10, int $offset = 0): DrinkList;
    public function getAllInCategoryAndCount(int $categoryId, int $limit = 10, int $offset = 0): DrinkList;
    public function getAllByUserAndCount(int $userId, int $limit = 10, int $offset = 0): DrinkList;
    public function searchAndCount(string $query, int $limit = 10, int $offset = 0): DrinkList;
    /** @return Drink[] */
    public function getUserFavourites(int $userId): array;
    /** @return Drink[] */
    public function getTopRated(int $limit = 3, int $offset = 0): array;
    /** @return Drink[] */
    public function getMostReviewed(int $limit = 3, int $offset = 0): array;
    public function addUserFavourite(int $userId, int $drinkId): void;
    public function removeUserFavourite(int $userId, int $drinkId): void;
    public function findById(int $id): ?Drink;
    public function insert(Drink $drink): Drink;
    public function update(Drink $drink): Drink;
    public function delete(Drink $drink): Drink;
}

class PdoDrinkDao implements DrinkDao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToDrink(array $row): Drink
    {
        return new Drink(
            $row["name"],
            $row["description"],
            $row["poster"],
            new User(
                $row["creator__username"],
                $row["creator__email"],
                $row["creator__password"],
                $row["creator__bio"],
                $row["creator__picture"],
                $row["creator__is_admin"] === "1",
                (int) $row["creator__id"],
                new DateTime($row["creator__created_at"]),
                new DateTime($row["creator__updated_at"])
            ),
            $row["avg_rating"] !== null ? (float) $row["avg_rating"] : null,
            $row["category_id"] !== null ? new Category(
                $row["category__name"],
                $row["category__poster"],
                (int) $row["category__id"],
                new DateTime($row["category__created_at"]),
                new DateTime($row["category__updated_at"]),
            ) : null,
            (int) $row["id"],
            new DateTime($row["created_at"]),
            new DateTime($row["updated_at"]),
        );
    }

    private function countRows(string $where, array $params = []): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM drinks D 
        JOIN categories C ON D.category_id = C.id 
        JOIN users U ON D.creator_id = U.id 
        WHERE " . $where);
        foreach ($params as $key => $value) {
            $stmt->bindParam($key, $value, PDO::PARAM_INT);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getAllAndCount(int $limit = 10, int $offset = 0): DrinkList
    {
        $stmt = $this->pdo->prepare("SELECT D.id AS id, D.name AS name, D.description AS description, D.poster AS poster, 
        D.creator_id AS creator_id, D.category_id AS category_id, D.created_at AS created_at, 
        D.updated_at AS updated_at,  U.id AS creator__id, U.username AS creator__username, U.email AS creator__email, 
        U.password AS creator__password, U.is_admin AS creator__is_admin, U.created_at AS creator__created_at, 
        U.picture AS creator__picture, U.bio AS creator__bio, 
        U.updated_at AS creator__updated_at, C.id AS category__id, C.name AS category__name, C.poster AS category__poster, 
        C.created_at AS category__created_at, C.updated_at AS category__updated_at, 
        (SELECT ROUND(AVG(rate), 1) FROM reviews WHERE drink_id = D.id) AS avg_rating 
        FROM drinks D 
        JOIN users U ON U.id = D.creator_id 
        LEFT JOIN categories C ON C.id = D.category_id 
        LIMIT :lt OFFSET :os;");
        $stmt->bindParam("lt", $limit, PDO::PARAM_INT);
        $stmt->bindParam("os", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $drinks = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($drinks, $this->mapRowToDrink($row));
        }

        $count = $this->countRows("1 = 1");

        return new DrinkList($drinks, $count);
    }

    public function getAllInCategoryAndCount(int $categoryId, int $limit = 10, int $offset = 0): DrinkList
    {
        $stmt = $this->pdo->prepare("SELECT D.id AS id, D.name AS name, D.description AS description, D.poster AS poster, 
        D.creator_id AS creator_id, D.category_id AS category_id, D.created_at AS created_at, 
        D.updated_at AS updated_at,  U.id AS creator__id, U.username AS creator__username, U.email AS creator__email, 
        U.password AS creator__password, U.is_admin AS creator__is_admin, U.created_at AS creator__created_at, 
        U.picture AS creator__picture, U.bio AS creator__bio, 
        U.updated_at AS creator__updated_at, C.id AS category__id, C.name AS category__name, C.poster AS category__poster, 
        C.created_at AS category__created_at, C.updated_at AS category__updated_at, 
        (SELECT ROUND(AVG(rate), 1) FROM reviews WHERE drink_id = D.id) AS avg_rating 
        FROM drinks D 
        JOIN users U ON U.id = D.creator_id 
        LEFT JOIN categories C ON C.id = D.category_id 
        WHERE D.category_id = :id LIMIT :lt OFFSET :os;");
        $stmt->bindParam("id", $categoryId, PDO::PARAM_INT);
        $stmt->bindParam("lt", $limit, PDO::PARAM_INT);
        $stmt->bindParam("os", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $drinks = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($drinks, $this->mapRowToDrink($row));
        }

        $count = $this->countRows("D.category_id = :id", ["id" => $categoryId]);

        return new DrinkList($drinks, $count);
    }

    public function getAllByUserAndCount(int $userId, int $limit = 10, int $offset = 0): DrinkList
    {
        $stmt = $this->pdo->prepare("SELECT D.id AS id, D.name AS name, D.description AS description, D.poster AS poster, 
        D.creator_id AS creator_id, D.category_id AS category_id, D.created_at AS created_at, 
        D.updated_at AS updated_at,  U.id AS creator__id, U.username AS creator__username, U.email AS creator__email, 
        U.password AS creator__password, U.is_admin AS creator__is_admin, U.created_at AS creator__created_at, 
        U.picture AS creator__picture, U.bio AS creator__bio, 
        U.updated_at AS creator__updated_at, C.id AS category__id, C.name AS category__name, C.poster AS category__poster, 
        C.created_at AS category__created_at, C.updated_at AS category__updated_at, 
        (SELECT ROUND(AVG(rate), 1) FROM reviews WHERE drink_id = D.id) AS avg_rating 
        FROM drinks D 
        JOIN users U ON U.id = D.creator_id 
        LEFT JOIN categories C ON C.id = D.category_id 
        WHERE D.creator_id = :id LIMIT :lt OFFSET :os;");
        $stmt->bindParam("id", $userId, PDO::PARAM_INT);
        $stmt->bindParam("lt", $limit, PDO::PARAM_INT);
        $stmt->bindParam("os", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $drinks = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($drinks, $this->mapRowToDrink($row));
        }

        $count = $this->countRows("D.creator_id = :id", ["id" => $userId]);

        return new DrinkList($drinks, $count);
    }

    /** @return Drink[] */
    public function getTopRated(int $limit = 3, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT D.id, D.name, D.description, D.poster, D.creator_id, D.category_id, D.created_at, D.updated_at, AVG(R.rate) AS avg_rating, COUNT(R.id) AS review_count
        FROM drinks D JOIN reviews R ON R.drink_id = D.id
        GROUP BY D.id
        ORDER BY avg_rating DESC, review_count DESC 
        LIMIT :lt OFFSET :os;");
        $stmt->bindParam("lt", $limit, PDO::PARAM_INT);
        $stmt->bindParam("os", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $drinks = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $drink = $this->findById($row["id"]);
            if ($drink !== null) {
                array_push($drinks, $drink);
            }
        }
        return $drinks;
    }

    /** @return Drink[] */
    public function getMostReviewed(int $limit = 3, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT D.id, COUNT(R.id) AS review_count, AVG(R.rate) AS avg_rating
        FROM drinks D JOIN reviews R ON R.drink_id = D.id
        GROUP BY D.id
        ORDER BY review_count DESC, avg_rating DESC 
        LIMIT :lt OFFSET :os;");
        $stmt->bindParam("lt", $limit, PDO::PARAM_INT);
        $stmt->bindParam("os", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $drinks = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $drink = $this->findById($row["id"]);
            if ($drink !== null) {
                array_push($drinks, $drink);
            }
        }
        return $drinks;
    }
    public function searchAndCount(string $query, int $limit = 10, int $offset = 0): DrinkList
    {
        $stmt = $this->pdo->prepare("SELECT D.id AS id, D.name AS name, D.description AS description, D.poster AS poster, 
        D.creator_id AS creator_id, D.category_id AS category_id, D.created_at AS created_at, 
        D.updated_at AS updated_at,  U.id AS creator__id, U.username AS creator__username, U.email AS creator__email, 
        U.password AS creator__password, U.is_admin AS creator__is_admin, U.created_at AS creator__created_at, 
        U.picture AS creator__picture, U.bio AS creator__bio, 
        U.updated_at AS creator__updated_at, C.id AS category__id, C.name AS category__name, C.poster AS category__poster, 
        C.created_at AS category__created_at, C.updated_at AS category__updated_at, 
        (SELECT ROUND(AVG(rate), 1) FROM reviews WHERE drink_id = D.id) AS avg_rating 
        FROM drinks D 
        JOIN users U ON U.id = D.creator_id 
        LEFT JOIN categories C ON C.id = D.category_id 
        WHERE D.name LIKE :name LIMIT :lt OFFSET :os;");
        $query = "%" . $query . "%";
        $stmt->bindParam("name", $query, PDO::PARAM_STR);
        $stmt->bindParam("lt", $limit, PDO::PARAM_INT);
        $stmt->bindParam("os", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $drinks = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($drinks, $this->mapRowToDrink($row));
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM drinks D 
        JOIN categories C ON D.category_id = C.id 
        JOIN users U ON D.creator_id = U.id 
        WHERE D.name LIKE :name");
        $stmt->bindParam("name", $query, PDO::PARAM_STR);
        $stmt->execute();
        $count = (int) $stmt->fetchColumn();

        return new DrinkList($drinks, $count);
    }

    /** @return Drink[] */
    public function getUserFavourites(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT D.id AS id, D.name AS name, D.description AS description, D.poster AS poster, 
        D.creator_id AS creator_id, D.category_id AS category_id, D.created_at AS created_at, 
        D.updated_at AS updated_at,  U.id AS creator__id, U.username AS creator__username, U.email AS creator__email, 
        U.password AS creator__password, U.is_admin AS creator__is_admin, U.created_at AS creator__created_at, 
        U.picture AS creator__picture, U.bio AS creator__bio, 
        U.updated_at AS creator__updated_at, C.id AS category__id, C.name AS category__name, C.poster AS category__poster, 
        C.created_at AS category__created_at, C.updated_at AS category__updated_at, 
        (SELECT ROUND(AVG(rate), 1) FROM reviews WHERE drink_id = D.id) AS avg_rating 
        FROM users_fav_drinks UD 
        JOIN drinks D ON D.id = UD.drink_id 
        JOIN users U ON U.id = D.creator_id 
        LEFT JOIN categories C ON C.id = D.category_id 
        WHERE UD.user_id = :id;");
        $stmt->bindParam("id", $userId, PDO::PARAM_INT);
        $stmt->execute();
        $drinks = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($drinks, $this->mapRowToDrink($row));
        }
        return $drinks;
    }

    public function addUserFavourite(int $userId, int $drinkId): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO users_fav_drinks (user_id, drink_id) VALUES (:user_id, :drink_id);");
        $stmt->bindParam("user_id", $userId, PDO::PARAM_INT);
        $stmt->bindParam("drink_id", $drinkId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function removeUserFavourite(int $userId, int $drinkId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM users_fav_drinks WHERE user_id = :user_id AND drink_id = :drink_id");
        $stmt->bindParam("user_id", $userId, PDO::PARAM_INT);
        $stmt->bindParam("drink_id", $drinkId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function findById(int $id): ?Drink
    {
        $stmt = $this->pdo->prepare("SELECT D.id AS id, D.name AS name, D.description AS description, D.poster AS poster, 
        D.creator_id AS creator_id, D.category_id AS category_id, D.created_at AS created_at, 
        D.updated_at AS updated_at,  U.id AS creator__id, U.username AS creator__username, U.email AS creator__email, 
        U.password AS creator__password, U.is_admin AS creator__is_admin, U.created_at AS creator__created_at, 
        U.picture AS creator__picture, U.bio AS creator__bio, 
        U.updated_at AS creator__updated_at, C.id AS category__id, C.name AS category__name, C.poster AS category__poster, 
        C.created_at AS category__created_at, C.updated_at AS category__updated_at, 
        (SELECT ROUND(AVG(rate), 1) FROM reviews WHERE drink_id = D.id) AS avg_rating  
        FROM drinks D 
        JOIN users U ON U.id = D.creator_id 
        LEFT JOIN categories C ON C.id = D.category_id 
        WHERE D.id = :id");
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToDrink($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function insert(Drink $drink): Drink
    {
        try {
            $this->pdo->beginTransaction();

            $insertStmt = $this->pdo->prepare("INSERT INTO drinks (name, description, poster, creator_id, category_id) VALUES (:name, :description, :poster, :creator_id, :category_id);");
            $insertStmt->bindParam("name", $drink->name, PDO::PARAM_STR);
            $insertStmt->bindParam("description", $drink->description, PDO::PARAM_STR);
            $insertStmt->bindParam("poster", $drink->poster, PDO::PARAM_STR);
            $creatorId = $drink->getCreator()->getId();
            $insertStmt->bindParam("creator_id", $creatorId, PDO::PARAM_INT);
            $categoryId = $drink->getCategory()?->getId();
            $insertStmt->bindParam("category_id", $categoryId, PDO::PARAM_INT | PDO::PARAM_NULL);
            $insertStmt->execute();

            $id = $this->pdo->lastInsertId();

            $newDrink = $this->findById($id);

            $this->pdo->commit();

            return $newDrink;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(Drink $drink): Drink
    {
        try {
            $this->pdo->beginTransaction();

            $updateStmt = $this->pdo->prepare("UPDATE drinks SET name = :name, description = :description, poster = :poster, category_id = :category_id WHERE id = :id");
            $updateStmt->bindParam("name", $drink->name, PDO::PARAM_STR);
            $updateStmt->bindParam("description", $drink->description, PDO::PARAM_STR);
            $updateStmt->bindParam("poster", $drink->poster, PDO::PARAM_STR);
            $categoryId = $drink->getCategory()?->getId();
            $updateStmt->bindParam("category_id", $categoryId, PDO::PARAM_INT | PDO::PARAM_NULL);
            $id = $drink->getId();
            $updateStmt->bindParam("id", $id, PDO::PARAM_INT);
            $updateStmt->execute();

            $updatedDrink = $this->findById($drink->getId());

            $this->pdo->commit();

            return $updatedDrink;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(Drink $drink): Drink
    {
        $stmt = $this->pdo->prepare("DELETE FROM drinks WHERE id = :id");
        $id = $drink->getId();
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $drink;
    }
}