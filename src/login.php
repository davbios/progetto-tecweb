<?php
require_once dirname(__FILE__) . "/db/db.php";
session_start();

$errori = [];
$formData = [
    'login' => ['username' => ''],
    'register' => ['email' => '', 'username' => '']
];

if(isset($_SESSION['login_errors'])) {
    $errori = $_SESSION['login_errors'];
    unset($_SESSION['login_errors']);
}

if(isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? null;
    $redirectTo = "login.php";
    if ($action === "login") {
        // accesso utente
        $username = trim($_POST["username"]) ?? null;
        $password = trim($_POST["password"]) ?? null;
        $user = $userDao->findByUsernameAndPassword($username, $password);
        if ($user) {
            $_SESSION["user_id"]  = $user->getId();
            $_SESSION["username"] = $user->getUsername();
            $_SESSION["is_admin"] = $user->isAdmin();
            $redirectTo = "index.php";
        } else {
            $errori[] ="L'username e/o password inseriti sono errati. Si prega di riprovare.";
            $_SESSION['login_errors'] = $errori;
            $_SESSION['form_data']['login']['username'] = htmlspecialchars($username);
            $_SESSION['active_form'] = 'login';
        }
    } elseif ($action === "register") {
        // registrazione
        $email        = trim($_POST["email"]) ?? null;
        $username     = trim($_POST["username"]) ?? null;
        $password     = trim($_POST["password"]) ?? null;
        $existingUser = $userDao->findByUsername($username);
        if ($existingUser) {
            $errori[] = "L'username è già utilizzato da un altro utente. Scegli un altro username.";
            $_SESSION['login_errors'] = $errori;
            $_SESSION['form_data']['register']['email'] = htmlspecialchars($email);
            $_SESSION['form_data']['register']['username'] = htmlspecialchars($username);
            $_SESSION['active_form'] = 'register';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $user = new User($username, $email, $passwordHash, false);
            $user = $userDao->insert($user);

            $_SESSION["user_id"]  = $user->getId();
            $_SESSION["username"] = $user->getUsername();
            $_SESSION["is_admin"] = false;
            $redirectTo = "index.php";
        }
    }
    header("Location: " . $redirectTo);
    exit;
}
$activeForm = $_SESSION['active_form'] ?? 'login';
if(isset($_SESSION['active_form'])) {
    unset($_SESSION['active_form']);
}
?>





<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arte del Cocktail | Accesso e Registrazione</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page" >
    <header>    
        <h1>Arte del Cocktail</h1>
    </header>

    <?php if (!empty($errori)): ?>
        <div id="error-messages" class="error-messages" role="alert" aria-live="assertive" aria-atomic="true">
            <?php foreach ($errori as $errore): ?>
                <p><?php echo htmlspecialchars($errore); ?> </p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <main id="container" class="container <?php echo ($activeForm === 'register') ? 'active' : ''; ?>">
        <div class="box login">
            <form action="login.php" method="POST" aria-labelledby="login-heading">
                <input type="hidden" name="action" value="login">
                <h2><span lang="en">Log-in</span></h2>
                <section class="input">
                    <label for="login-username">Username</label>
                    <input id="login-username" type="text" name="username" placeholder="Username" value="<?php echo $formData['login']['username']; ?>" required>
                    <img src="img/user.svg" alt="" aria-hidden="true">
                </section>
                <section class="input">
                    <label for="login-password">Password</label>
                    <input id="login-password" type="password" name="password" placeholder="Password" required>
                    <img src="img/lock.svg" alt="" aria-hidden="true">
                </section>
                <button type="submit" class="btn" id="login-submit" >Accedi</button>
            </form>
        </div>
        <div class="box register">
            <form action="login.php" method="POST" aria-labelledby="register-heading">
                <input type="hidden" name="action" value="register">
                <h2>Registrati</h2>
                <!-- possibilità di mettere un paragrafo con le istruzioni -->
                <section class="input">
                    <label for="reg-email">Email</label>
                    <input id="reg-email" type="email" name="email" placeholder="email" value="<?php echo $formData['register']['email']; ?>" required>
                    <img src="img/mail.svg" alt="" aria-hidden="true">
                </section>
                <section class="input">
                    <label for="reg-username">Username</label>
                    <input id="reg-username" type="text" name="username" placeholder="Username" value="<?php echo $formData['register']['username']; ?>" required>
                    <img src="img/user.svg" alt="" aria-hidden="true">
                </section>
                <section class="input">
                    <label for="reg-password">Password</label>
                    <input id="reg-password" type="password" name="password" placeholder="Password" required>
                    <img src="img/lock.svg" alt="" aria-hidden="true">
                </section>
                <button type="submit" class="btn" id="reg-submit">Crea un nuovo profilo </button>
            </form>
        </div>
        <div class="curtain-box">
            <div class="curtain left">
                <h2><span lang="en">Welcome!</span></h2>
                <p>Non hai un <span lang="en">account</span>?</p>
                <p>Nessun problema, crea un nuovo <span lang="en">account</span> ora!</p>
                <button type="button" id="reg-btn" class="btn reg-btn" aria-label="Passa al form di registrazione"
                 <?php echo ($activeForm === 'register') ? 'tabindex="-1"' : ''; ?>>
                    Registrati
                </button>
            </div>
            <div class="curtain right">
                <h2><span lang="en">Welcome Back!</span></h2>
                <p>Hai già un <span lang="en">account</span>?</p>
                <p>Siamo felici di rivederti 😊 </p> 
                <p>Accedi ora al tuo <span lang="en">account</span>:</p>
                <button type="button" id="login-btn" class="btn login-btn" aria-label="Passa al form di login"
                 <?php echo ($activeForm === 'login') ? 'tabindex="-1"' : ''; ?>>
                    Accedi
                </button>
            </div>
        </div> 
    </main>
    <script src="js/script.js"></script>
</body>
</html>