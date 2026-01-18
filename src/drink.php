<?php
require_once dirname(__FILE__) . "/db/db.php";
require_once dirname(__FILE__) . "/app/navbar.php";
session_start();

if (empty($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$drink = $drinkDao->findById($_GET["id"]);
if (empty($drink)) {
    header("Location: index.php");
    exit;
}

$template = file_get_contents(dirname(__FILE__) . "/templates/layout.html");
$template = str_replace("[title]", "Arte del Cocktail | " . $drink->name, $template);
$template = str_replace("[description]", $drink->description, $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<a href="/" lang="en">Home</a> » <a href="/esplora.php">Esplora</a> » ' . $drink->name, $template);

$content = '<div class="row">
            <section class="drink-image">
                <img src="' . $drink->poster . '" alt="' . $drink->name . '">
            </section>
            <section class="drink-info">
                <h2>' . $drink->name . '</h2>
                <button type="submit" class="btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="feather feather-heart">
                        <path
                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                        </path>
                    </svg>
                    Aggiungi ai preferiti
                </button>
                <p>' . $drink->description . '</p>
            </section>
        </div>';

$content .= '<div class="row">
            <section class="drink-ingredients">
                <h3>Ingredienti</h3>
                <ul>';
foreach ($ingredientDao->getAllForDrink($drink->getId()) as $ingredient) {
    $content .= "<li>" . $ingredient->quantity . " " . $ingredient->name . "</li>\n";
}
$content .= '</ul>
            </section>
            <section class="drink-prep">
                <h3>Preparazione</h3>
                <ol>';
foreach ($stepDao->getAllForDrink($drink->getId()) as $step) {
    $content .= "<li>" . $step->description . "</li>\n";
}
$content .= '</ol>
            </section>
        </div>
        <section>
            <h3>Recensioni</h3>
            <ul class="reviews">';
for ($i = 0; $i < 10; $i++) {
    $review = file_get_contents(dirname(__FILE__) . "/templates/review.html");
    $review = str_replace("[rating]", "3", $review);
    $content .= $review;
}
$content .= '</ul>
        </section>';

$template = str_replace("[content]", $content, $template);

echo $template;

?>