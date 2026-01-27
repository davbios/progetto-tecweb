<?php
require_once dirname(__FILE__) . "/app/global.php";

$template = getTemplate("layout");
$template = str_replace("[title]", "Esplora | Arte del Cocktail", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("esplora", isset($_SESSION["user_id"])), $template);

$content = getTemplate("esplora");

$page = 1;
if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
    $page = intval($_GET["pagina"]);
}
if ($page < 1) {
    $page = 1;
}

$drinks = [];
$drinksPerPage = 21;

$place = '';
$subtitle = '';
if (isset($_GET["q"])) {
    $drinks = $drinkDao->search($_GET["q"], $drinksPerPage, $page - 1);
    $subtitle = 'Ricerca per "' . $_GET["q"] . '"';
    $place = 'Ricerca';
} elseif (isset($_GET["category"]) && is_numeric($_GET["category"])) {
    $category = $categoryDao->findById(intval($_GET["category"]));
    $drinks = $drinkDao->getAllInCategory($category->getId(), $drinksPerPage, $page - 1);
    $subtitle = '<span lang="en">Drink</span> ' . $category->name;
    $place = '<a href="categorie.php">Categorie</a> » <span lang="en">Drink</span> ' . $category->name;
} else {
    $drinks = $drinkDao->getAllOfficial($drinksPerPage, $page - 1);
    $subtitle = 'I nostri <span lang="en">Drink</span>';
    $place = 'Esplora';
}
$content = str_replace("[subtitle]", $subtitle, $content);
$template = str_replace("[breadcrumb]", '<a href="/" lang="en">Home</a> » ' . $place, $template);

$drinksListContent = '';
if (count($drinks) > 0) {
    $drinksListContent = '<ul class="drink-list">';
    foreach ($drinks as $drink) {
        $drinkCard = getTemplate("drink_card");
        $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
        $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
        $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
        $drinksListContent .= $drinkCard;
    }
    $drinksListContent .= '</ul>';
} else {
    $drinksListContent = '<p class="empty-result">Nessun risultato</p>';
}
$content = str_replace("[drinks]", $drinksListContent, $content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>