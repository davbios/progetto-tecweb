<?php

require_once dirname(__FILE__) . "/navbar.php";
require_once dirname(__FILE__, 2) . "/db/db.php";
session_start();

function getLoggedUser(): ?User {
    global $userDao;
    if (!isset($_SESSION["user_id"])) {
        return null;
    }
    return $userDao->findById($_SESSION["user_id"]);
}

function setPageError(string $page, string $message): void {
    if (!isset($_SESSION["error"])) {
        $_SESSION["error"] = [];
    }
    $_SESSION["error"][$page] = $message;
}

function getPageError(string $page): ?string {
    if (isset($_SESSION["error"][$page])) {
        $message = $_SESSION["error"][$page];
        unset($_SESSION["error"][$page]);
        return $message;
    }
    return null;
}

?>