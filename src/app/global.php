<?php

require_once dirname(__FILE__) . "/navbar.php";
require_once dirname(__FILE__, 2) . "/db/db.php";
session_start();

function getLoggedUser(): ?User
{
    global $userDao;
    if (!isset($_SESSION["user_id"])) {
        return null;
    }
    return $userDao->findById($_SESSION["user_id"]);
}

function setPageError(string $page, string $message, string $field = "_global"): void
{
    if (!isset($_SESSION["error"])) {
        $_SESSION["error"] = [];
    }
    if (!isset($_SESSION["error"][$page])) {
        $_SESSION["error"][$page] = [];
    }
    $_SESSION["error"][$page][$field] = $message;
}

function getPageError(string $page, string $field = "_global"): ?string
{
    if (isset($_SESSION["error"][$page]) && isset($_SESSION["error"][$page][$field])) {
        $message = $_SESSION["error"][$page][$field];
        unset($_SESSION["error"][$page][$field]);
        return $message;
    }
    return null;
}

function displayFormError(string $page, string $field, string $template): string
{
    $error = getPageError($page, $field);
    return str_replace(
        "[error_" . $field . "]",
        (isset($error) ? '<p class="input-error">' . $error . '</p>' : ''),
        $template
    );
}

function getTemplate(string $name): string
{
    return file_get_contents(dirname(__FILE__, 2) . "/templates/" . $name . ".html");
}

?>