<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();
if (!$user) {
    header("Location: login.php");
    exit;
}

$userId = $_GET['id'] ?? $_SESSION['user_id'] ?? null;
if (!$userId) {
    header("Location: login.php");
    exit;
}

$profileUser = $userDao->findById($userId);
if (!$profileUser) {
    header("Location: index.php");
    exit;
}

$userDrinks = $drinkDao->getAllByUser($userId); 
$favoriteDrinks = $drinkDao->getUserFavourites($userId);
$isOwnProfile = ($user->getId() === $profileUser->getId());

$errori = [];
if(isset($_SESSION['profile_errors'])) {
    $errori = $_SESSION['profile_errors'];
    unset($_SESSION['profile_errors']);
}

$template = getTemplate("layout");
$template = str_replace("[title]", $profileUser->getUsername() . " | Profilo | Arte del Cocktail", $template);
$template = str_replace("[description]", "Profilo di " . $profileUser->getUsername() . ". Scopri le sue creazioni e i cocktail preferiti.", $template);
$template = str_replace("[keywords]", "profilo, " . $profileUser->getUsername() . ", cocktail, drink, utente", $template);
$template = str_replace("[navbar]", getNavbar("profilo", true), $template);
$template = str_replace("[breadcrumb]", '<a href="/" lang="en">Home</a> » ' . "Profilo", $template);

$content = getTemplate("profilo");
$content = str_replace("[picture]", $profileUser->getPicture() ? $profileUser->getPicture() : 'img/user-icon.png', $content);
$content = str_replace("[Username]", htmlspecialchars($profileUser->getUsername()), $content);
$content = str_replace("[Email]", htmlspecialchars($profileUser->getEmail()), $content);
$content = str_replace("[bio]", htmlspecialchars($profileUser->bio), $content);        

$editButton = '';
if ($isOwnProfile) {
    $editButton = '<a href="modifica-profilo.php?id=' . $userId . '" class="edit-btn">Modifica Profilo</a>';
}
$content = str_replace("[edit_button]", $editButton, $content);

// Sezione drink preferiti
if ($favoriteDrinks && count($favoriteDrinks) > 0) {
    $favhtml = '<div class="drink-grid">';
    foreach ($favoriteDrinks as $drink) {
        $drinkCard = getTemplate("drink_card");
        $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
        $drinkCard = str_replace("[drink]", htmlspecialchars($drink->name), $drinkCard);
        $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
        $favhtml .= $drinkCard;
    }
    $favhtml .= '</div>';
} else {
    $favhtml = '<p class="no-content">Nessun cocktail preferito.</p>';
}
$content = str_replace('[favorites_list]', $favhtml, $content);

// Sezione creazioni
if ($userDrinks && count($userDrinks) > 0) {
    $creationsHtml = '<div class="drink-grid">';
    foreach ($userDrinks as $drink) {
        $drinkCard = getTemplate("drink_card");
        $drinkCard = str_replace("[id]", $drink->getId(), $drinkCard);
        $drinkCard = str_replace("[drink]", htmlspecialchars($drink->name), $drinkCard);
        $drinkCard = str_replace("[image]", $drink->poster, $drinkCard);
        $creationsHtml .= $drinkCard;
    }
    $creationsHtml .= '</div>';
} else {
    $creationsHtml = '<p class="no-content">Nessuna creazione ancora pubblicata.</p>';
}
$content = str_replace('[creations_list]', $creationsHtml, $content);

// Gestione messaggi di errore
$errorMessages = "";
if (!empty($errori)) {
    $errorTemplate = getTemplate("error_messages");
    $messages = "";
    foreach ($errori as $errore) {
        $messages .= '<p>' . htmlspecialchars($errore) . '</p>';
    }
    $errorMessages = str_replace("[messages]", $messages, $errorTemplate);
}
$content = str_replace('[error_messages]', $errorMessages, $content);
$template = str_replace("[content]", $content, $template);

echo $template;
?>