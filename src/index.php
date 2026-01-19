<?php
require_once dirname(__FILE__) . "/app/global.php";

$template = file_get_contents(dirname(__FILE__) . "/templates/layout.html");
$template = str_replace("[title]", "Arte del Cocktail", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("home", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<span lang="en">Home</span>', $template);

$content = '<section class="search-section">
<h1>Cerca, scopri e perfeziona i migliori cocktail alcolici da tutto il mondo.</h1>
<form action="cerca.html" method="GET">
    <label for="site-search" class="visually-hidden">Cerca un drink</label>
    <input type="search" id="site-search" name="q" placeholder="Hai già in mente un drink? Trovalo in un attimo." autocomplete="off">
    <button type="submit" class="search-btn" aria-label="Avvia ricerca">
        <span aria-hidden="true"><img src="img/search.svg" alt=""></span>
    </button>
</form>
</section>
<section>
<h2>I <span lang="en">cocktail</span> più amati</h2>
<ul class="drink-list">';

$drinks = $drinkDao->getAllOfficial(10, 0);
foreach ($drinks as $drink) {
    $drinkCard = file_get_contents(dirname(__FILE__) . "/templates/drink_card.html");
    $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
    $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
    $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
    $content .= $drinkCard;
}
$content .= '</ul>
</section>';

$template = str_replace("[content]", $content, $template);

echo $template;
?>