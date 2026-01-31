<?php

require_once dirname(__FILE__) . "/navbar.php";
require_once dirname(__FILE__) . "/form.php";
require_once dirname(__FILE__, 2) . "/db/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!str_ends_with($_SERVER['REQUEST_URI'], "disclaimer.php") && !isset($_SESSION['disclaimer_accepted'])) {
    redirectTo("disclaimer.php");
    exit();
}

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
        (isset($error) ? '<p role="alert" class="input-error" id="errorMessage' . $field . '">' . $error . '</p>' : ''),
        $template
    );
}

function getTemplate(string $name): string
{
    return file_get_contents(dirname(__FILE__, 2) . "/templates/" . $name . ".html");
}

function handleImageUpload(string $name, string $type): ?string
{
    global $_FILES;
    if (!isset($_FILES[$name]) || $_FILES[$name]["error"] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedTypes = ["image/jpeg", "image/png", "image/webp"];
    $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($fileInfo, $_FILES[$name]["tmp_name"]);
    finfo_close($fileInfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return null;
    }


    $extension = pathinfo($_FILES[$name]["name"], PATHINFO_EXTENSION);
    $picture = uniqid($type . "_") . "." . $extension;
    $uploadPath = "uploads/" . $type . "/" . $picture;
    if (!is_dir("uploads/" . $type)) {
        mkdir("uploads/" . $type, 0755, true);
    }

    if (!move_uploaded_file($_FILES[$name]["tmp_name"], $uploadPath)) {
        return null;
    }

    return $uploadPath;
}

function redirectTo(string $location, array $params = []): void
{
    if (!empty($params)) {
        $location .= "?";
        foreach ($params as $key => $value) {
            $location .= $key . '=' . urlencode($value) . '&';
        }
        $location = substr($location, 0, -1);
    }
    header("Location: " . $location);
}

function redirectNotFound(): void
{
    header("Location: 404.html", true, 404);
}

?>