<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();
if ($user === null) {
    redirectTo("login.php", ["from" => "profilo.php"]);
    exit;
}

$profileUser = $user;
$isOwnProfile = true;
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $profileUser = $userDao->findById(intval($_GET["id"]));
    if ($profileUser === null) {
        redirectNotFound();
        exit;
    }
    $isOwnProfile = false;
}

$userDrinks = [];
$favoriteDrinks = [];
try {
    $result = $drinkDao->getAllByUserAndCount($profileUser->getId(), 100, 0);
    $userDrinks = $result->drinks;
    $favoriteDrinks = $drinkDao->getUserFavourites($profileUser->getId());
} catch (PDOException $e) {
    setPageError(__FILE__, $e->getMessage());
}

$template = getTemplate("layout");
$template = str_replace("[title]", $profileUser->getUsername() . " | Profilo | Arte del Cocktail", $template);
$template = str_replace("[description]", "Profilo di " . $profileUser->getUsername() . ". Scopri le sue creazioni e i cocktail preferiti.", $template);
$template = str_replace("[keywords]", "profilo, " . $profileUser->getUsername() . ", cocktail, drink, utente", $template);
$template = str_replace("[navbar]", getNavbar(__FILE__, $isOwnProfile ? '' : "altro", true), $template);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » ' . "Profilo", $template);

$content = getTemplate("profilo");
$content = str_replace("[picture]", $profileUser->getPicture() ?? 'img/user-default-icon.jpg', $content);
$content = str_replace("[Username]", htmlspecialchars($profileUser->getUsername()), $content);
$content = str_replace("[Email]", htmlspecialchars($profileUser->getEmail()), $content);
$content = str_replace("[bio]", htmlspecialchars($profileUser->bio), $content);

$editButton = '';
if ($isOwnProfile) {
    $editButton = '<a href="modifica-profilo.php" class="edit-btn">Modifica Profilo</a>';
}
$content = str_replace("[edit_button]", $editButton, $content);

// Sezione drink preferiti
if ($favoriteDrinks && count($favoriteDrinks) > 0) {
    $favhtml = '<ul class="drink-list">';
    foreach ($favoriteDrinks as $drink) {
        $drinkCard = getTemplate("drink_card");
        $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
        $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
        $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
        $drinkCard = str_replace("[avg_rating]", $drink->getAvgRating(), $drinkCard);
        $favhtml .= $drinkCard;
    }
    $favhtml .= '</ul>';
} else {
    $favhtml = '<p class="no-content">Nessun cocktail preferito.</p>';
}
$content = str_replace('[favorites_list]', $favhtml, $content);

// Sezione creazioni
if ($userDrinks && count($userDrinks) > 0) {
    $creationsHtml = '<ul class="drink-list">';
    foreach ($userDrinks as $drink) {
        $drinkCard = getTemplate("drink_card");
        $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
        $drinkCard = str_replace("[drink]", $drink->name, $drinkCard);
        $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
        $drinkCard = str_replace("[avg_rating]", $drink->getAvgRating(), $drinkCard);
        $creationsHtml .= $drinkCard;
    }
    $creationsHtml .= '</ul>';
} else {
    $creationsHtml = '<p class="no-content">Nessuna creazione ancora pubblicata.</p>';
}
$content = str_replace('[creations_list]', $creationsHtml, $content);
$content = str_replace('[creations_pronom]', $isOwnProfile ? 'mie' : 'sue', $content);

$error = getPageError(__FILE__);
$content = str_replace(
    "[error]",
    isset($error) ? str_replace("[message]", $error, getTemplate("section_error")) : "",
    $content
);

$template = str_replace("[content]", $content, $template);

echo $template;
?>