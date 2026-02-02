<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();

if ($user === null) {
    redirectTo("login.php", ["from" => "preferiti.php"]);
    exit;
}

$template = getTemplate("layout");
$template = str_replace("[title]", "Preferiti | Arte del Cocktail", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar(__FILE__, "", true), $template);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » <a href="profilo.php">Profilo</a> » Preferiti', $template);
$content = '<section class="page">
<h2>Preferiti</h2>
<ul class="drink-list">';
$drinks = $drinkDao->getUserFavourites($user->getId());
foreach ($drinks as $drink) {
    $drinkCard = getTemplate("drink_card");
    $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
    $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
    $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
    $drinkCard = str_replace("[avg_rating]", $drink->getAvgRating(), $drinkCard);
    $content .= $drinkCard;
}
$content .= '</ul>
</section>';
$template = str_replace("[content]", $content, $template);

echo $template;
?>