<?php
require_once dirname(__FILE__) . "/BaseModel.php";

class User extends BaseModel
{
    public string $username;
    public string $email;
    private string $password;
    private bool $is_admin;

    public function __construct(string $username, string $email, string $password, bool $is_admin, ?int $id = null, ?DateTime $created_at = null, ?DateTime $updated_at = null)
    {
        parent::__construct($id, $created_at, $updated_at);
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->is_admin = $is_admin;
    }

    public function __set($name, $value)
    {
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}

interface UserDao
{
    public function findById(int $id): ?User;
    public function findByUsername(string $username): ?User;
    public function findByUsernameAndPassword(string $username, string $password): ?User;
    public function hasUserFavouriteDrink(int $userId, int $drinkId): bool;
    public function insert(User $user): User;
    public function update(User $user): User;
    public function delete(User $user): User;
}

class PdoUserDao implements UserDao
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function mapRowToUser(array $row): User
    {
        return new User(
            $row["username"],
            $row["email"],
            $row["password"],
            $row["is_admin"] === "1",
            (int) $row["id"],
            new DateTime($row["created_at"]),
            new DateTime($row["updated_at"]),
        );
    }

    public function getAll(int $limit = 10, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare("SELECT id, username, email, password, is_admin, created_at, updated_at FROM users LIMIT :lmt OFFSET :os;");
        $stmt->bindParam("lmt", $limit, PDO::PARAM_INT);
        $stmt->bindParam("os", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            array_push($users, $this->mapRowToUser($row));
        }
        return $users;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT id, username, email, password, is_admin, created_at, updated_at FROM users WHERE id = :id");
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToUser($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->pdo->prepare("SELECT id, username, email, password, is_admin, created_at, updated_at FROM users WHERE username = :username");
        $stmt->bindParam("username", $username, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() === 1) {
            return $this->mapRowToUser($stmt->fetch(PDO::FETCH_ASSOC));
        } else {
            return null;
        }
    }

    public function findByUsernameAndPassword(string $username, string $password): ?User
    {
        $user = $this->findByUsername($username);
        if (!$user || !password_verify($password, $user->getPassword())) {
            return NULL;
        }
        return $user;
    }

    public function hasUserFavouriteDrink(int $userId, int $drinkId): bool {
        $stmt = $this->pdo->prepare("SELECT 1 FROM users_fav_drinks WHERE user_id = :userId AND drink_id = :drinkId");
        $stmt->bindParam("userId", $userId, PDO::PARAM_INT);
        $stmt->bindParam("drinkId", $drinkId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() === 1;
    }

    public function insert(User $user): User
    {
        try {
            $this->pdo->beginTransaction();

            $insertStmt = $this->pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (:username, :email, :password, :is_admin);");
            $username = $user->getUsername();
            $insertStmt->bindParam("username", $username, PDO::PARAM_STR);
            $email = $user->getEmail();
            $insertStmt->bindParam("email", $email, PDO::PARAM_STR);
            $password = $user->getPassword();
            $insertStmt->bindParam("password", $password, PDO::PARAM_STR);
            $isAdmin = $user->isAdmin();
            $insertStmt->bindParam("is_admin", $isAdmin, PDO::PARAM_BOOL);
            $insertStmt->execute();

            $id = $this->pdo->lastInsertId();

            $newUser = $this->findById($id);

            $this->pdo->commit();

            return $newUser;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function update(User $user): User
    {
        try {
            $this->pdo->beginTransaction();

            $updateStmt = $this->pdo->prepare("UPDATE users SET username = :username, email = :email, password = :password, is_admin = :is_admin WHERE id = :id");
            $username = $user->getUsername();
            $updateStmt->bindParam("username", $username, PDO::PARAM_STR);
            $email = $user->getEmail();
            $updateStmt->bindParam("email", $email, PDO::PARAM_STR);
            $password = $user->getPassword();
            $updateStmt->bindParam("password", $password, PDO::PARAM_STR);
            $isAdmin = $user->isAdmin();
            $updateStmt->bindParam("is_admin", $isAdmin, PDO::PARAM_BOOL);
            $id = $user->getId();
            $updateStmt->bindParam("id", $id, PDO::PARAM_INT);
            $updateStmt->execute();

            $updatedUser = $this->findById($user->getId());

            $this->pdo->commit();

            return $updatedUser;
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function delete(User $user): User
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $id = $user->getId();
        $stmt->bindParam("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $user;
    }
}