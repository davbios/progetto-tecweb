<?php
require_once dirname(__FILE__) . "/app/global.php";

$template = getTemplate("layout");
$template = str_replace("[title]", "Home | Arte del Cocktail", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar(__FILE__, "", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<span lang="en">Home</span>', $template);

$content = getTemplate("home");

$topRatedContent = "";
foreach ($drinkDao->getTopRated(3, 0) as $drink) {
    $drinkCard = getTemplate("drink_card");
    $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
    $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
    $drinkCard = str_replace("[avg_rating]", $drink->getAvgRating(), $drinkCard);
    $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
    $topRatedContent .= $drinkCard;
}
$content = str_replace("[top_rated]", $topRatedContent, $content);


$mostReviewedContent = "";
foreach ($drinkDao->getMostReviewed(3, 0) as $drink) {
    $drinkCard = getTemplate("drink_card");
    $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
    $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
    $drinkCard = str_replace("[avg_rating]", $drink->getAvgRating(), $drinkCard);
    $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
    $mostReviewedContent .= $drinkCard;
}
$content = str_replace("[most_reviewed]", $mostReviewedContent, $content);


$template = str_replace("[content]", $content, $template);

echo $template;
?>