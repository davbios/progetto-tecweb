<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST["action"] === "create") {
        $uploaddir = "/var/www/uploads/";
        $posterFilename = basename($_FILES["poster"]["name"]);

        if (!move_uploaded_file($_FILES["poster"]["tmp_name"], $uploaddir . $posterFilename)) {
            setPageError(__FILE__, "Immagine non valida.", "poster");
            header("Location: categorie.php");
            exit;
        }

        if (empty(trim($_POST["name"]))) {
            setPageError(__FILE__, "Nome della categoria non valido.", "name");
            header("Location: categorie.php");
            exit;
        }
    } else {
        setPageError(__FILE__, "Azione sconosciuta");
        header("Location: categorie.php");
        exit;
    }

} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("Location: categorie.php");
    exit;
}

if (
    isset($user) && $user->isAdmin() &&
    isset($_GET["action"]) && $_GET["action"] === "delete" && isset($_GET["id"]) && is_numeric($_GET["id"])
) {
    $category = $categoryDao->findById(intval($_GET["id"]));
    $categoryDao->delete($category);
    header("Location: categorie.php");
    exit;
}

$template = getTemplate("layout");
$template = str_replace("[title]", "Categorie | Arte del Cocktail", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("categorie", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<a href="/" lang="en">Home</a> » Categorie', $template);

$content = getTemplate("categorie");

$formContent = "";
if (isset($user) && $user->isAdmin()) {
    $formContent = getTemplate("category_form");
    $formContent = displayFormError(__FILE__, "poster", $formContent);
    $formContent = displayFormError(__FILE__, "name", $formContent);
}
$content = str_replace("[create_form]", $formContent, $content);

$page = 1;
if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
    $page = intval($_GET["pagina"]);
}
if ($page < 1) {
    $page = 1;
}
$categories = $categoryDao->getAll(21, $page - 1);
$categoriesListContent = "";
foreach ($categories as $category) {
    $drinkCard = getTemplate("category_card");
    $drinkCard = str_replace("[name]", $category->name, $drinkCard);
    $drinkCard = str_replace("[image]", $category->poster, $drinkCard);
    $drinkCard = str_replace("[id]", $category->getId(), $drinkCard);
    $drinkCard = str_replace(
        "[actions]",
        (isset($user) && $user->isAdmin()) ? '<a href="categorie.php?action=delete&id=' . $category->getId() . '" class="btn btn-danger">Elimina</a>' : "",
        $drinkCard
    );
    $categoriesListContent .= $drinkCard;
}
$content = str_replace("[categories]", $categoriesListContent, $content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>