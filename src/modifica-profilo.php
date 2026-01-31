<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();

if (!isset($user)) {
    redirectTo("login.php?from=modifica-profilo.php");
    exit;
}

$formInfo = new Form(
    __FILE__,
    "",
    [
        "username" => $user->username,
        "email" => $user->email,
        "bio" => $user->bio,
    ]
);
$formInfo->loadDataFromSession();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $redirectLoc = "modifica-profilo.php";
    if ($_POST["action"] === "info") {
        // Salva il contenuto del form nella sessione in modo che nel caso ci fossero errori 
        // i valori inseriti dall'utente possono essere recuperati.
        $formInfo->saveValues($_POST);

        if (!isset($_POST["username"]) || empty(trim($_POST["username"]))) {
            setPageError(__FILE__, "Nome utente non valido", "username");
            redirectTo($redirectLoc);
            exit;
        }
        if (!isset($_POST["email"]) || empty(trim($_POST["email"])) || filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === false) {
            setPageError(__FILE__, "Email non valida", "email");
            redirectTo($redirectLoc);
            exit;
        }
        if (!isset($_POST["bio"]) || empty(trim($_POST["bio"]))) {
            setPageError(__FILE__, "Bio non valida", "bio");
            redirectTo($redirectLoc);
            exit;
        }

        $user->username = htmlspecialchars(trim($_POST["username"]));
        $user->email = htmlspecialchars($_POST["email"]);
        $user->bio = htmlspecialchars(trim($_POST["bio"]));
        $formInfo->clearSession();
    } elseif ($_POST["action"] === "picture") {
        $image = handleImageUpload("picture", "user");
        if (!isset($image)) {
            setPageError(__FILE__, "Immagine non valida.", "picture");
            redirectTo($redirectLoc);
            exit;
        }
        $user->picture = $image;
    } elseif ($_POST["action"] === "password") {
        if (!password_verify($_POST["oldpassword"], $user->getPassword())) {
            setPageError(__FILE__, "Password errata", "oldusername");
            redirectTo($redirectLoc);
            exit;
        }

        if (!isset($_POST["password"]) || empty($_POST["password"])) {
            setPageError(__FILE__, "Password non valida", "username");
            redirectTo($redirectLoc);
            exit;
        }

        if ($_POST["password"] !== $_POST["repassword"]) {
            setPageError(__FILE__, "Le password non coincidono non valida", "repassword");
            redirectTo($redirectLoc);
            exit;
        }

        $user->setPassword($_POST["password"]);
    } else {
        setPageError(__FILE__, "Azione sconosciuta");
        redirectTo($redirectLoc);
        exit;
    }

    try {
        $userDao->update($user);
        $redirectLoc = "profilo.php";
    } catch (PDOException $e) {
        setPageError(__FILE__, $e->getMessage());
    }

    redirectTo($redirectLoc);
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    redirectTo("index.php");
    exit;
}

$template = getTemplate("layout");
$template = str_replace("[title]", "Modifica Profilo | Arte del Cocktail", $template);
$template = str_replace("[description]", "Modifica il tuo profilo.", $template);
$template = str_replace("[keywords]", "", $template);
$template = str_replace("[navbar]", getNavbar(__FILE__, "", true), $template);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » <a href="profilo.php" lang="en">Profilo</a> » Modifica Profilo', $template);

$content = getTemplate("modifica_profilo");

$error = getPageError(__FILE__);
$content = str_replace(
    "[error]",
    isset($error) ? str_replace("[message]", $error, getTemplate("section_error")) : "",
    $content
);

$content = displayFormError(__FILE__, "picture", $content);

$content = $formInfo->render($content);

$content = displayFormError(__FILE__, "oldpassword", $content);
$content = displayFormError(__FILE__, "password", $content);
$content = displayFormError(__FILE__, "repassword", $content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>