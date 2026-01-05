<?php
require_once dirname(__FILE__) . "/db/db.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? null;

    // accesso utente
    if ($action === "login") {

        $username = $_POST["username"] ?? null;
        $password = $_POST["password"] ?? null;

        if (!$username || !$password) {
            die("Dati mancanti");
        }

        $user = $userDao->findByUsername($username);
        if (!$user || !password_verify($password, $user->getPassword())) {
            die("Username o password errati");
        }

        $_SESSION["user_id"]  = $user->getId();
        $_SESSION["username"] = $user->getUsername();
        $_SESSION["is_admin"] = $user->isAdmin();

        header("Location: index.php");
        exit;
    }
    // registrazione
    if ($action === "register") {

        $email        = $_POST["email"] ?? null;
        $username     = $_POST["username"] ?? null;
        $password     = $_POST["password"] ?? null;

        if (!$email || !$username || !$password) {
            die("Dati mancanti");
        }
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $user = new User($username, $email, $passwordHash, false);
        $user = $userDao->insert($user);

        $_SESSION["user_id"]  = $user->getId();
        $_SESSION["username"] = $user->getUsername();
        $_SESSION["is_admin"] = false;

        header("Location: index.php");
        exit;
    }
    // accesso admin
    if ($action === "admin_login") {

        $username = $_POST["username"] ?? null;
        $password = $_POST["password"] ?? null;

        if (!$username || !$password) {
            die("Dati mancanti");
        }

        $user = $userDao->findByUsername($username);

        if (
            !$user ||
            !$user->isAdmin() ||
            !password_verify($password, $user->getPassword())
        ) {
            die("Accesso negato");
        }

        $_SESSION["user_id"]  = $user->getId();
        $_SESSION["username"] = $user->getUsername();
        $_SESSION["is_admin"] = true;

        header("Location: admin.php");
        exit;
    }
}
?>





<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log-in</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <header>    
        <h1>Titolo del sito</h1>
        <section class="admin-area">
            <button id="admin-btn" class="btn">Area Amministratore</button>
            <section class="admin-login">
                <form class="admin-form" action="login.php" method="POST">
                    <input type="hidden" name="action" value="admin_login">
                    <input id="admin-username" type="text" name="username" placeholder="Username" required>
                    <img id="usr" src="img/user.svg" alt="" aria-hidden="true">
                    <input id="admin-password" type="password" name="password" placeholder="Password" required>
                    <img id="psw" src="img/lock.svg" alt="" aria-hidden="true">
                    <button type="submit" class="btn admin-btn">Accedi</button>
                </form>
            </section>
        </section>
    </header>
    <main class="container">
        <div class="box login">
            <form action="login.php" method="POST">
                <input type="hidden" name="action" value="login">
                <h2><span lang="en">Log-in</span></h2>
                <section class="input">
                    <label for="login-username">Username</label>
                    <input id="login-username" type="text" name="username" placeholder="Username" required>
                    <img src="img/user.svg" alt="" aria-hidden="true">
                </section>
                <section class="input">
                    <label for="login-password">Password</label>
                    <input id="login-password" type="password" name="password" placeholder="Password" required>
                    <img src="img/lock.svg" alt="" aria-hidden="true">
                </section>
                <button type="submit" class="btn">Accedi</button>
            </form>
        </div>
        <div class="box register">
            <form action="login.php" method="POST">
                <input type="hidden" name="action" value="register">
                <h2>Registrati</h2>
                <!-- possibilità di mettere un paragrafo con le istruzioni -->
                <section class="input">
                    <label for="reg-email">Email</label>
                    <input id="reg-email" type="email" name="email" placeholder="email" required>
                    <img src="img/mail.svg" alt="" aria-hidden="true">
                </section>
                <section class="input">
                    <label for="reg-username">Username</label>
                    <input id="reg-username" type="text" name="username" placeholder="Username" required>
                    <img src="img/user.svg" alt="" aria-hidden="true">
                </section>
                <section class="input">
                    <label for="reg-password">Password</label>
                    <input id="reg-password" type="password" name="password" placeholder="Password" required>
                    <img src="img/lock.svg" alt="" aria-hidden="true">
                </section>
                <button type="submit" class="btn">Crea un <span lang="en">account</span></button>
            </form>
        </div>
        <div class="curtain-box">
            <div class="curtain left">
                <h2><span lang="en">Welcome!</span></h2>
                <p>Non hai un <span lang="en">account</span>?</p>
                <p>Nessun problema, crea un nuovo <span lang="en">account</span> ora!</p>
                <button type="button" class="btn reg-btn">Registrati</button>
            </div>
            <div class="curtain right">
                <h2><span lang="en">Welcome Back!</span></h2>
                <p>Hai già un <span lang="en">account</span>?</p>
                <p>Siamo felici di rivederti 😊 </p> 
                <p>Accedi ora al tuo <span lang="en">account</span>:</p>
                <button type="button" class="btn login-btn">Accedi</button>
            </div>
        </div>
    </main>
    <script src="js/login.js"></script>
</body>
</html>