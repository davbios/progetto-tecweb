<?php

function getNavbar(string $file, string $query, bool $userIsLogged): string
{
    $current = basename($file);
    $content = '<nav class="navbar" aria-label="Menu principale">
        <button class="nav-toggle"
                id="nav-toggle" 
                aria-label="Apri menu"
                aria-expanded="false"
                aria-controls="nav-menus" onclick="toggleNavbar(event)">
            Menù
        </button>
        <div id="nav-menus" class="nav-menus">
            <ul class="main-navbar">';
    $content .= '<li id="homeNav" lang="en"' . ($current === "index.php" ? ' class="current" >Home' : '><a href="index.php" lang="en">Home</a>') . '</li>';
    $content .= '<li id="catNav"' . ($current === "categorie.php" ? ' class="current" >Categorie' : '><a href="categorie.php">Categorie</a>') . '</li>';
    $content .= '<li id="exploreNav"' . ($current === "esplora.php" ? ' class="current" >Esplora' : '><a href="esplora.php">Esplora</a>') . '</li>';
    $content .= '</ul>
            <ul class="user-navbar">';
    if ($userIsLogged) {
        $content .= '<li id="createNav"' . ($current === "nuovo-drink.php" ? ' class="current" >Crea' : '><a href="nuovo-drink.php">Crea</a>') . '</li>';
        $content .= '<li id="favsNav"' . ($current === "preferiti.php" ? ' class="current" >Preferiti' : '><a href="preferiti.php">Preferiti</a>') . '</li>';
        $content .= '<li id="profileNav"' . ($current === "profilo.php" ? ' class="current" >Profilo' : '><a href="profilo.php">Profilo</a>') . '</li>';
        $content .= '<li id="logoutNav"><a href="logout.php" lang="en">Logout</a></li>';
    } else {
        $content .= '<li id="loginNav"><a href="login.php?from=' . $current . '?' . urlencode($query) . '">Entra o Registrati</a></li>';
    }

    $content .= '</ul>
        </div>
    </nav>';
    return $content;
}