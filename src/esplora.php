<?php
require_once dirname(__FILE__) . "/db/db.php";
require_once dirname(__FILE__) . "/app/global.php";

$template = file_get_contents(dirname(__FILE__) . "/templates/layout.html");
$template = str_replace("[title]", "Arte del Cocktail | Esplora", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("esplora", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<a href="/" lang="en">Home</a> » Esplora', $template);

$content = '<section class="title">
<h2>Esplora</h2>
</section>
<ul class="drink-list">';

$page = 1;
if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
    $page = intval($_GET["pagina"]);
}
if ($page < 1) {
    $page = 1;
}
$drinks = $drinkDao->getAllOfficial(10, $page - 1);
foreach ($drinks as $drink) {
    $drinkCard = file_get_contents(dirname(__FILE__) . "/templates/drink_card.html");
    $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
    $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
    $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
    $content .= $drinkCard;
}
$content .= '</ul>';
$template = str_replace("[content]", $content, $template);

echo $template;
?>