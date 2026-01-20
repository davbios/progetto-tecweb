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
            $rate = intval(floatval($_POST["rating"]) * 2);
            $review = new Review($_POST["text"], $rate, $user, $queryDrinkId, null, null, null);
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

$template = getTemplate("layout");
$template = str_replace("[title]", "Arte del Cocktail | " . $drink->name, $template);
$template = str_replace("[description]", $drink->description, $template);
$template = str_replace("[keywords]", "drink, cocktails, alcolici, ricette, alcol, bar, ingredienti, come fare", $template);
$template = str_replace("[navbar]", getNavbar("", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<a href="/" lang="en">Home</a> » <a href="/esplora.php">Esplora</a> » ' . $drink->name, $template);

$content = getTemplate("drink");

$error = getPageError(__FILE__);
$content = str_replace(
    "[error]",
    isset($error) ? str_replace("[message]", $error, getTemplate("section_error")) : "",
    $content
);

$content = str_replace("[poster]", $drink->poster, $content);
$content = str_replace("[name]", $drink->name, $content);
$content = str_replace("[description]", $drink->description, $content);

$isDrinkUserFavourite = false;
if ($user !== null) {
    $isDrinkUserFavourite = $userDao->hasUserFavouriteDrink($user->getId(), $drink->getId());

    $actionsContent = '<form action="drink.php?id=' . $drink->getId() . '&action=' . ($isDrinkUserFavourite ? 'removeFavourite' : 'addFavourite') . '" method="POST">
                    <button type="submit" class="btn">
                        <img src="img/like.svg" alt="">
                        ' . ($isDrinkUserFavourite ? 'Rimuovi dai preferiti' : 'Aggiungi ai preferiti') .
        '</button>
    </form>';
    $content = str_replace("[actions]", $actionsContent, $content);
} else {
    $content = str_replace("[actions]", "", $content);
}

$ingredientsListContent = "";
foreach ($ingredientDao->getAllForDrink($drink->getId()) as $ingredient) {
    $ingredientsListContent .= "<li>" . $ingredient->quantity . " " . $ingredient->name . "</li>\n";
}
$content = str_replace("[ingredients]", $ingredientsListContent, $content);

$stepsListContent = "";
foreach ($stepDao->getAllForDrink($drink->getId()) as $step) {
    $stepsListContent .= "<li>" . $step->description . "</li>\n";
}
$content = str_replace("[steps]", $stepsListContent, $content);

$reviewsContent = "";
if ($user !== null) {
    $form = getTemplate("review_form");
    $form = str_replace("[username]", $user->username, $form);
    $form = str_replace("[drink_id]", $drink->getId(), $form);
    $form = str_replace("[user_icon]", "img/user-icon.png", $form);
    $reviewsContent .= $form;
}

foreach ($reviewDao->getAllForDrink($drink->getId()) as $review) {
    $reviewCard = getTemplate("review");
    $reviewCard = str_replace("[rating]", $review->rate / 2.0, $reviewCard);
    $reviewCard = str_replace("[text]", $review->text, $reviewCard);
    $reviewCard = str_replace("[username]", $review->getAuthor()->username, $reviewCard);
    $reviewCard = str_replace("[user_icon]", "img/user-icon.png", $reviewCard);
    $reviewsContent .= $reviewCard;
}
$content = str_replace("[reviews]", $reviewsContent, $content);

$template = str_replace("[content]", $content, $template);

echo $template;

?>