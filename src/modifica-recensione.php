<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();

if (!isset($user)) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$review = $reviewDao->findById(intval($_GET["id"]));
if (!isset($review) || $review->getAuthor()->getId() !== $user->getId()) {
    header("Location: index.php");
    exit;
}

$form = new Form(
    __FILE__,
    "",
    [
        "rating" => $review->rate / 2,
        "text" => $review->text
    ]
);
$form->loadDataFromSession();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $redirectTo = "modifica-recensione.php?id=" . $review->getId();
    // Salva il contenuto del form nella sessione in modo che nel caso ci fossero errori 
    // i valori inseriti dall'utente possono essere recuperati.
    $form->saveValues($_POST);

    if (!isset($_POST["text"]) || empty(trim($_POST["text"]))) {
        setPageError(__FILE__, "Descrizione non valida", "text");
        header("Location: " . $redirectTo);
        exit;
    }
    if (!isset($_POST["rating"]) || !is_numeric($_POST["rating"])) {
        setPageError(__FILE__, "Voto non valido", "rating");
        header("Location: " . $redirectTo);
        exit;
    }
    $rating = floatval($_POST["rating"]);
    if ($rating < 0.5 || $rating > 5 || intval($rating * 2) != ($rating * 2.0)) {
        setPageError(__FILE__, "Il voto deve essere compreso tra 0.5 e 5", "rating");
        header("Location: " . $redirectTo);
        exit;
    }

    $review->text = trim($_POST["text"]);
    $review->rate = $rating * 2;

    try {
        $reviewDao->update($review);
        $redirectTo = "drink.php?id=" . $review->getDrinkId();
    } catch (PDOException $e) {
        setPageError(__FILE__, $e->getMessage());
    }

    $form->clearSession();
    header("Location: " . $redirectTo);
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("Location: index.php");
    exit;
}

$drink = $drinkDao->findById($review->getDrinkId());

$template = getTemplate("layout");
$template = str_replace("[title]", "Modifica Recensione | Arte del Cocktail", $template);
$template = str_replace("[description]", "", $template);
$template = str_replace("[keywords]", "", $template);
$template = str_replace("[navbar]", getNavbar("modifica-recensione", true), $template);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » <a href="esplora.php">Esplora</a> » <a href="drink.php?id=' . $drink->getId() . '">' . $drink->name . '</a> » Modifica Recensione', $template);

$content = getTemplate("modifica_recensione");

$error = getPageError(__FILE__);
$content = str_replace(
    "[error]",
    isset($error) ? str_replace("[message]", $error, getTemplate("section_error")) : "",
    $content
);

$content = str_replace("[review_id]", $review->getId(), $content);
$content = $form->render($content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>