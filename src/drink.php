<?php
require_once dirname(__FILE__) . "/app/global.php";

if (empty($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$user = getLoggedUser();

$drink = $drinkDao->findById(intval($_GET["id"]));
if (empty($drink)) {
    header("Location: index.php");
    exit;
}

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
            $review = new Review($_POST["text"], $rate, $user, $drink->getId(), null, null, null);
            try {
                $reviewDao->insert($review);
            } catch (PDOException $e) {
                setPageError(__FILE__, 'Si è verificato un errore: non è stato possibile creare la recensione.');
            }
        }
    } else {
        setPageError(__FILE__, "Azione sconosciuta");
    }
    header("Location: drink.php?id=" . $drink->getId());
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("Location: index.php");
    exit;
}

if (isset($_GET["action"])) {
    // per qualsiasi azione l'utente deve essere loggato
    if ($user === null) {
        header("Location: login.php");
        exit;
    }

    $redirectTo = "drink.php?id=" . $drink->getId();
    if ($_GET["action"] === "addFavourite") {
        try {
            $drinkDao->addUserFavourite($user->getId(), $drink->getId());
        } catch (PDOException $e) {
            setPageError(__FILE__, 'Si è verificato un errore: non è stato possibile inserire il <span lang="en">drink</span> nei preferiti.');
        }
    } elseif ($_GET["action"] === "removeFavourite") {
        try {
            $drinkDao->removeUserFavourite($user->getId(), $drink->getId());
        } catch (PDOException $e) {
            setPageError(__FILE__, 'Si è verificato un errore: non è stato possibile rimuovere il <span lang="en">drink</span> dai preferiti.');
        }
    } elseif ($_GET["action"] === "delete" && $user->getId() === $drink->getCreator()->getId()) {
        try {
            $drinkDao->delete($drink);
            $redirectTo = "/";
        } catch (PDOException $e) {
            setPageError(__FILE__, 'Si è verificato un errore: non è stato possibile eliminare il <span lang="en">drink</span>.');
        }
    } elseif ($_GET["action"] === "deleteReview" && is_numeric($_GET["reviewId"])) {
        $review = $reviewDao->findById(intval($_GET["reviewId"]));
        if (!isset($review) || $review->getAuthor()->getId() !== $user->getid()) {
            setPageError(__FILE__, 'Non è possibile eliminare questa recensione');
        } else {
            try {
                $reviewDao->delete($review);
            } catch (PDOException $e) {
                setPageError(__FILE__, 'Non è stato possibile eliminare la recensione.');
            }
        }
    } else {
        setPageError(__FILE__, "Azione sconosciuta");
    }

    header("Location: " . $redirectTo);
    exit;
}

$template = getTemplate("layout");
$template = str_replace("[title]", $drink->name . " | Arte del Cocktail", $template);
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

    $actionsContent = '<div class="actions">';
    if ($isDrinkUserFavourite) {
        $actionsContent .= '<a href="drink.php?id=' . $drink->getId() . '&action=removeFavourite" 
        class="btn btn-icon" id="btnFavourite" aria-label="Rimuovi questo drink dai preferiti" role="button">
            Rimuovi dai preferiti
        </a>';
    } else {
        $actionsContent .= '<a href="drink.php?id=' . $drink->getId() . '&action=addFavourite" 
        class="btn btn-icon" id="btnFavourite" aria-label="Aggiungi questo drink ai preferiti" role="button">
            Aggiungi ai preferiti
        </a>';
    }

    if ($user->getId() === $drink->getCreator()->getId()) {
        $actionsContent .= '<a href="modifica-drink.php?id=' . $drink->getId() . '" class="btn btn-icon btn-warning" id="btnEdit" 
         role="button" aria-label="Modifica questo drink">Modifica</a>';
        $actionsContent .= '<a href="drink.php?id=' . $drink->getId() . '&action=delete" class="btn btn-icon btn-danger" id="btnDelete" 
         role="button" aria-label="Elimina questo drink">
            Elimina
        </a>';
    }

    $actionsContent .= "</div>";
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
    $reviewCard = str_replace("[datetime]", $review->getUpdatedAt()->format("d/m/Y - H:i:s"), $reviewCard);
    $reviewCard = str_replace("[user_icon]", "img/user-icon.png", $reviewCard);

    if (isset($user) && $user->getId() === $review->getAuthor()->getId()) {
        $reviewCard = str_replace("[actions]", '<a href="drink.php?id=' . $drink->getId() . '&action=deleteReview&reviewId=' . $review->getId() . '" class="btn btn-icon btn-danger" id="btnDeleteReview">Elimina</a>', $reviewCard);
    } else {
        $reviewCard = str_replace("[actions]", "", $reviewCard);
    }
    $reviewsContent .= $reviewCard;
}
$content = str_replace("[reviews]", $reviewsContent, $content);

$template = str_replace("[content]", $content, $template);

echo $template;

?>