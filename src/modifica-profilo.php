<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();

if (!isset($user)) {
    header("Location: login.php");
    exit;
}

$formData = [
    "username" => $user->username,
    "email" => $user->email,
    "bio" => $user->bio,
];
$sessionFormDataKey = "edit_user_form_data";
if (isset($_SESSION[$sessionFormDataKey])) {
    $formData = $_SESSION[$sessionFormDataKey];
    unset($_SESSION[$sessionFormDataKey]);
}
function saveFormDataValue(string $key, string|array $value): void
{
    global $sessionFormDataKey;
    $_SESSION[$sessionFormDataKey][$key] = htmlspecialchars($value);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $redirectTo = "modifica-profilo.php";
    if ($_POST["action"] === "info") {
        // Salva il contenuto del form nella sessione in modo che nel caso ci fossero errori 
        // i valori inseriti dall'utente possono essere recuperati.
        saveFormDataValue("username", $_POST["username"]);
        saveFormDataValue("email", $_POST["email"]);
        saveFormDataValue("bio", $_POST["bio"]);

        if (empty(trim($_POST["username"]))) {
            setPageError(__FILE__, "Nome utente non valido", "username");
            header("Location: modifica-profilo.php");
            exit;
        }
        if (empty(trim($_POST["email"])) || filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === false) {
            setPageError(__FILE__, "Email non valida", "email");
            header("Location: modifica-profilo.php");
            exit;
        }
        if (empty(trim($_POST["bio"]))) {
            setPageError(__FILE__, "Bio non valida", "bio");
            header("Location: modifica-profilo.php");
            exit;
        }

        $user->username = trim($_POST["username"]);
        $user->email = $_POST["email"];
        $user->bio = trim($_POST["bio"]);
    } elseif ($_POST["action"] === "picture") {
        $image = handleImageUpload("picture", "user");
        if (!isset($image)) {
            setPageError(__FILE__, "Immagine non valida.", "picture");
            header("Location: modifica-profilo.php");
            exit;
        }
        $user->picture = $image;
    } elseif ($_POST["action"] === "password") {
        if (!password_verify($_POST["oldpassword"], $user->getPassword())) {
            setPageError(__FILE__, "Password errata", "oldusername");
            header("Location: modifica-profilo.php");
            exit;
        }

        if (empty($_POST["password"])) {
            setPageError(__FILE__, "Password non valida", "username");
            header("Location: modifica-profilo.php");
            exit;
        }

        if ($_POST["password"] !== $_POST["repassword"]) {
            setPageError(__FILE__, "Le password non coincidono non valida", "repassword");
            header("Location: modifica-profilo.php");
            exit;
        }

        $user->setPassword($_POST["password"]);
    } else {
        setPageError(__FILE__, "Azione sconosciuta");
        header("Location: modifica-profilo.php");
        exit;
    }

    try {
        $userDao->update($user);
        $redirectTo = "profilo.php";
    } catch (PDOException $e) {
        setPageError(__FILE__, $e->getMessage());
    }

    unset($_SESSION[$sessionFormDataKey]);
    header("Location: " . $redirectTo);
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("Location: index.php");
    exit;
}

$template = getTemplate("layout");
$template = str_replace("[title]", "Modifica Profilo | Arte del Cocktail", $template);
$template = str_replace("[description]", "Modifica il tuo profilo.", $template);
$template = str_replace("[keywords]", "", $template);
$template = str_replace("[navbar]", getNavbar("modifica-profilo", true), $template);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » <a href="profilo.php" lang="en">Profilo</a> » Modifica Profilo', $template);

$content = getTemplate("modifica_profilo");

$error = getPageError(__FILE__);
$content = str_replace(
    "[error]",
    isset($error) ? str_replace("[message]", $error, getTemplate("section_error")) : "",
    $content
);

$content = displayFormError(__FILE__, "picture", $content);

$content = str_replace("[username]", $formData["username"], $content);
$content = displayFormError(__FILE__, "username", $content);
$content = str_replace("[email]", $formData["email"], $content);
$content = displayFormError(__FILE__, "email", $content);
$content = str_replace("[bio]", $formData["bio"], $content);
$content = displayFormError(__FILE__, "bio", $content);

$content = displayFormError(__FILE__, "oldpassword", $content);
$content = displayFormError(__FILE__, "password", $content);
$content = displayFormError(__FILE__, "repassword", $content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>