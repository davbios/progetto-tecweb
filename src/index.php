<?php
require_once dirname(__FILE__) . "/app/global.php";

$template = getTemplate("layout");
$template = str_replace("[title]", "Arte del Cocktail", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("home", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<span lang="en">Home</span>', $template);

$content = getTemplate("home");

$bestDrinksContent = "";
foreach ($drinkDao->getAllOfficial(10, 0) as $drink) {
    $drinkCard = getTemplate("drink_card");
    $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
    $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
    $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
    $bestDrinksContent .= $drinkCard;
}
$content = str_replace("[best_drinks]", $bestDrinksContent, $content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>