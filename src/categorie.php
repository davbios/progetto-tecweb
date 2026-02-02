<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();

$form = new Form(
    __FILE__,
    "",
    [
        "name" => "",
        "poster" => "",
    ]
);
$form->loadDataFromSession();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST["action"] === "create") {
        $form->saveValues($_POST);

        $redirectLoc = "categorie.php";
        $poster = handleImageUpload("poster", "category");
        if (!isset($poster)) {
            setPageError(__FILE__, "Immagine non valida.", "poster");
            redirectTo($redirectLoc);
            exit;
        }

        if (empty(trim($_POST["name"]))) {
            setPageError(__FILE__, "Nome della categoria non valido.", "name");
            redirectTo($redirectLoc);
            exit;
        }

        $category = new Category(htmlspecialchars(trim($_POST["name"])), $poster, null, null, null);
        try {
            $categoryDao->insert($category);
        } catch (PDOException $e) {
            setPageError(__FILE__, $e->getMessage());
        }
    } else {
        setPageError(__FILE__, "Azione sconosciuta");
    }
    $form->clearSession();
    redirectTo($redirectLoc);
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    redirectTo($redirectLoc);
    exit;
}

if (
    isset($user) && $user->isAdmin() &&
    isset($_GET["action"]) && $_GET["action"] === "delete" && isset($_GET["id"]) && is_numeric($_GET["id"])
) {
    try {
        $category = $categoryDao->findById(intval($_GET["id"]));
        if ($category === null) {
            redirectNotFound();
            exit;
        }
        $categoryDao->delete($category);
    } catch (PDOException $e) {
        setPageError(__FILE__, $e->getMessage());
    }

    redirectTo("categorie.php");
    exit;
}

$template = getTemplate("layout");
$template = str_replace("[title]", "Categorie | Arte del Cocktail", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar(__FILE__, "", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » Categorie', $template);

$content = getTemplate("categorie");

$categories = [];
try {
    $categories = $categoryDao->getAll();
} catch (PDOException $e) {
    setPageError(__FILE__, $e->getMessage());
}

$error = getPageError(__FILE__);
$content = str_replace(
    "[error]",
    isset($error) ? str_replace("[message]", $error, getTemplate("section_error")) : "",
    $content
);

$formContent = "";
if (isset($user) && $user->isAdmin()) {
    $formContent = getTemplate("category_form");
    $formContent = $form->render($formContent);
}
$content = str_replace("[create_form]", $formContent, $content);

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