<?php
require_once dirname(__FILE__) . "/app/global.php";

$template = getTemplate("layout");
$template = str_replace("[title]", "Esplora | Arte del Cocktail", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar(__FILE__, "pagina=" . $page, isset($_SESSION["user_id"])), $template);

$content = getTemplate("esplora");

$page = 1;
if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
    $page = intval($_GET["pagina"]);
}
if ($page < 1) {
    $page = 1;
}

$result = null;
$drinksPerPage = 9;
$offset = ($page - 1) * $drinksPerPage;

$place = '';
$subtitle = '';
if (isset($_GET["q"])) {
    $result = $drinkDao->searchAndCount($_GET["q"], $drinksPerPage, $offset);
    $subtitle = 'Ricerca per "' . $_GET["q"] . '"';
    $place = 'Ricerca';
} elseif (isset($_GET["category"]) && is_numeric($_GET["category"])) {
    $category = $categoryDao->findById(intval($_GET["category"]));
    $result = $drinkDao->getAllInCategoryAndCount($category->getId(), $drinksPerPage, $offset);
    $subtitle = '<span lang="en">Drink</span> ' . $category->name;
    $place = '<a href="categorie.php">Categorie</a> » <span lang="en">Drink</span> ' . $category->name;
} else {
    $result = $drinkDao->getAllAndCount($drinksPerPage, $offset);
    $subtitle = 'I nostri <span lang="en">Drink</span>';
    $place = 'Esplora';
}
$content = str_replace("[subtitle]", $subtitle, $content);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » ' . $place, $template);

$drinksListContent = '';
if ($result->count > 0) {
    $drinksListContent = '<ul class="drink-list">';
    foreach ($result->drinks as $drink) {
        $drinkCard = getTemplate("drink_card");
        $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
        $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
        $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
        $drinkCard = str_replace("[avg_rating]", $drink->getAvgRating(), $drinkCard);
        $drinksListContent .= $drinkCard;
    }
    $drinksListContent .= '</ul>';

    $totalPages = (int) ceil($result->count / $drinksPerPage);
    $content = str_replace("[page]", $page, $content);
    $content = str_replace("[total_pages]", $totalPages, $content);
    $content = str_replace(
        "[page_prev]",
        ($page > 1) ? '<a href="esplora.php?pagina=' . ($page - 1) . '" class="page-prev">Precedente</a>' : '',
        $content
    );
    $content = str_replace(
        "[page_next]",
        ($page < $totalPages) ? '<a href="esplora.php?pagina=' . ($page + 1) . '" class="page-next">Successiva</a>' : '',
        $content
    );
} else {
    $drinksListContent = '<p class="empty-result">Nessun risultato</p>';
}
$content = str_replace("[drinks]", $drinksListContent, $content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>