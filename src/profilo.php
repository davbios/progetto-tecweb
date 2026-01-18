<?php
require_once dirname(__FILE__) . "/db/db.php";
require_once dirname(__FILE__) . "/app/navbar.php";
session_start();

$template = file_get_contents(dirname(__FILE__) . "/templates/layout.html");
$template = str_replace("[title]", "Arte del Cocktail | Profilo", $template);
$template = str_replace("[description]", "Il ricettario social per i tuoi drink. Cerca ispirazione tra il nostro catalogo e le creazioni degli altri utenti.", $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("profilo", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<a href="/" lang="en">Home</a> » Profilo', $template);

echo $template;
?>