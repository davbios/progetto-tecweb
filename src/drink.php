<?php
require_once dirname(__FILE__) . "/app/global.php";

if (empty($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}
$queryDrinkId = intval($_GET["id"]);

$user = getLoggedUser();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_GET["action"] ?? null;

    // per qualsiasi azione l'utente deve essere loggato
    if ($user === null) {
        header("Location: login.php");
        exit;
    }

    if ($action === "review") {
        if (empty($_POST["text"]) || !is_numeric($_POST["rating"])) {
            setPageError(__FILE__, 'Recensione non valida.');
        } else {
            $review = new Review($_POST["text"], floatval($_POST["rating"]), $user, $queryDrinkId, null, null, null);
            try {
                $reviewDao->insert($review);
            } catch (PDOException $e) {
                setPageError(__FILE__, 'Si è verificato un errore: non è stato possibile creare la recensione.');
            }
        }
    } elseif ($action === "addFavourite") {
        try {
            $drinkDao->addUserFavourite($user->getId(), $queryDrinkId);
        } catch (PDOException $e) {
            setPageError(__FILE__, 'Si è verificato un errore: non è stato possibile inserire il <span lang="en">drink</span> nei preferiti.');
        }
    } elseif ($action === "removeFavourite") {
        try {
            $drinkDao->removeUserFavourite($user->getId(), $queryDrinkId);
        } catch (PDOException $e) {
            setPageError(__FILE__, 'Si è verificato un errore: non è stato possibile rimuovere il <span lang="en">drink</span> dai preferiti.');
        }
    }
    header("Location: drink.php?id=" . $queryDrinkId);
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("Location: index.php");
    exit;
}

$drink = $drinkDao->findById($queryDrinkId);
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

$content = '';
$error = getPageError(__FILE__);
if (isset($error)) {
    $content .= '<section class="error">
    <p>';
    $content .= $error;
    $content .= '</p>
    </section>';
}
$content .= '<div class="row">';
$content .= '<section class="drink-image">
                <img src="' . $drink->poster . '" alt="' . $drink->name . '">
            </section>
            <section class="drink-info">
                <h2>' . $drink->name . '</h2>';

$isDrinkUserFavourite = false;
if ($user !== null) {
    $isDrinkUserFavourite = $userDao->hasUserFavouriteDrink($user->getId(), $drink->getId());

    $content .= '<form action="drink.php?id=' . $drink->getId() . '&action=' . ($isDrinkUserFavourite ? 'removeFavourite' : 'addFavourite') . '" method="POST">
                    <button type="submit" class="btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-heart">
                            <path
                                d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                            </path>
                        </svg>
                        ' . ($isDrinkUserFavourite ? 'Rimuovi dai preferiti' : 'Aggiungi ai preferiti') .
        '</button>
                </form>';
}

$content .= '<p>' . $drink->description . '</p>
            </section>
        </div>
        <div class="row">
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
if ($user !== null) {
    $content .= '<li class="review">
    <div class="review-icon">
        <img src="img/user-icon.png" alt="">
    </div>
    <div class="review-body">
        <strong class="review-author">' . $user->getUsername() . '</strong>
        <form action="drink.php?id=' . $drink->getId() . '&action=review" method="POST" class="review-text">
            <div class="form-group">
                <label for="rating">Voto</label>
                <input type="number" id="rating" name="rating" step="0.5" max="5" min="0.5" required>
            </div>
            <div class="form-group">
                <label for="text">Descrizione</label>
                <textarea id="text" name="text" required></textarea>
            </div>
            <button type="submit" class="btn">Pubblica</button>
        </form>
    </div>
</li>';
}

foreach ($reviewDao->getAllForDrink($drink->getId()) as $review) {
    $reviewCard = file_get_contents(dirname(__FILE__) . "/templates/review.html");
    $reviewCard = str_replace("[rating]", $review->rate, $reviewCard);
    $reviewCard = str_replace("[text]", $review->text, $reviewCard);
    $reviewCard = str_replace("[username]", $review->getAuthor()->username, $reviewCard);
    $reviewCard = str_replace("[user_icon]", "img/user-icon.png", $reviewCard);
    $content .= $reviewCard;
}
$content .= '</ul>
        </section>';

$template = str_replace("[content]", $content, $template);

echo $template;

?>