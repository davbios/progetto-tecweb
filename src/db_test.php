<?php
declare(strict_types=1);

require_once dirname(__FILE__) . "/db/User.php";

try {
    $dbh = new PDO(
        "mysql:host=127.0.0.1;port=3306;dbname=progetto",
        "progetto",
        "progetto",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $userDao = new PdoUserDao($dbh);

    foreach ($userDao->getAll() as $user) {
        var_dump($user);
        echo "<br/>";
    }

    $user = new User("admin", "admin@example.com", "admin123", false);
    $user = $userDao->insert($user);
    echo "Created user <br/>";
    var_dump($user);

    $user->username = "root";
    $user = $userDao->update($user);
    echo "<br/><br/>Updated user <br/>";
    var_dump($user);

    $user = $userDao->delete($user);
    echo "<br/><br/>Deleted user <br/>";
    var_dump($user);
    echo "Done";
} catch (PDOException $e) {
    echo "PDO error: " . $e->getMessage();
}