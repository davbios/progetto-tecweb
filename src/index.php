<?php
require_once dirname(__FILE__) . "/db/db.php";
require_once dirname(__FILE__) . "/app/navbar.php";
session_start();

$template = file_get_contents(dirname(__FILE__) . "/templates/layout.html");
$template = str_replace("[title]", "Drinks", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("home", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<span lang="en">Home</span>', $template);

$content = '<section class="title">
<h2>A quale drink stai pensando?</h2>
</section>
<section class="search-section">
<form action="cerca.html" method="GET">
    <label for="site-search" class="visually-hidden">Cerca un drink</label>
    <input type="search" id="site-search" name="q" placeholder="Cerca..." autocomplete="off">
    <button type="submit" class="search-btn" aria-label="Avvia ricerca">
        <span aria-hidden="true"><img src="img/search.svg" alt=""></span>
    </button>
</form>
</section>';

$template = str_replace("[content]", $content, $template);

echo $template;
?>