<?php

function getNavbar(string $current, bool $userIsLogged): string
{
    $content = '<nav class="navbar" aria-label="Menu principale">
            <ul class="main-navbar">';
    $content .= '<li id="homeNav" lang="en"' . ($current === "home" ? ' class="current" >Home' : '><a href="/" lang="en">Home</a>') . '</li>';
    $content .= '<li id="catNav"' . ($current === "categorie" ? ' class="current" >Categorie' : '><a href="/categorie.php">Categorie</a>') . '</li>';
    $content .= '<li id="exploreNav"' . ($current === "esplora" ? ' class="current" >Esplora' : '><a href="/esplora.php">Esplora</a>') . '</li>';
    $content .= '<li id="creationsNav"' . ($current === "creazioni" ? ' class="current" >Creazioni' : '><a href="/creazioni.php">Creazioni</a>') . '</li>';
    $content .= '</ul>
            <ul class="user-navbar">';
    if ($userIsLogged) {
        $content .= '<li id="createNav"' . ($current === "nuovo" ? ' class="current" >Crea' : '><a href="/nuovo-drink.php">Crea</a>') . '</li>';
        $content .= '<li id="favsNav"' . ($current === "preferiti" ? ' class="current" >Preferiti' : '><a href="/preferiti.php">Preferiti</a>') . '</li>';
        $content .= '<li id="profileNav"' . ($current === "profilo" ? ' class="current" >Profilo' : '><a href="/profilo.php">Profilo</a>') . '</li>';
        $content .= '<li id="logoutNav"' . ($current === "logout" ? ' class="current" lang="en">Logout' : '><a href="/logout.php" lang="en">Logout</a>') . '</li>';
    } else {
        $content .= '<li id="loginNav"' . ($current === "login" ? ' class="current" >Login' : '><a href="/login.php">Entra o Registrati</a>') . '</li>';
    }
    
    $content .= '</ul>
        </nav>';
    return $content;
}