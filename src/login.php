<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();
if ($user) {
    header("Location: index.php");
    exit;
}

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
            $errori[] = "L'username e/o password inseriti sono errati. Si prega di riprovare.";
            $_SESSION['login_errors'] = $errori;
            $_SESSION['form_data']['login']['username'] = htmlspecialchars($username);
            $_SESSION['active_form'] = 'login';
        }
    } elseif ($action === "register") {
        // registrazione
        $email = trim($_POST["email"]) ?? null;
        $username = trim($_POST["username"]) ?? null;
        $password = trim($_POST["password"]) ?? null;
        $existingUser = $userDao->findByUsername($username);
        
        if ($existingUser) {
            $errori[] = "L'username è già utilizzato da un altro utente. Scegli un altro username.";
            $_SESSION['login_errors'] = $errori;
            $_SESSION['form_data']['register']['email'] = htmlspecialchars($email);
            $_SESSION['form_data']['register']['username'] = htmlspecialchars($username);
            $_SESSION['active_form'] = 'register';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $user = new User($username, $email, $passwordHash, '', null, false);
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

$template = getTemplate("login");

$errorMessages = "";
if (!empty($errori)) {
    $errorTemplate = getTemplate("error_messages");
    $messages = "";
    foreach ($errori as $errore) {
        $messages .= '<p>' . htmlspecialchars($errore) . '</p>';
    }
    $errorMessages = str_replace("[messages]", $messages, $errorTemplate);
}

$template = str_replace("[error_messages]", $errorMessages, $template);
$template = str_replace("[active_form_class]", ($activeForm === 'register') ? 'active' : '', $template);
$template = str_replace("[login_username]", htmlspecialchars($formData['login']['username']), $template);
$template = str_replace("[register_email]", htmlspecialchars($formData['register']['email']), $template);
$template = str_replace("[register_username]", htmlspecialchars($formData['register']['username']), $template);
$template = str_replace("[register_pct]", "",  $template);
$template = str_replace("[reg_btn_tabindex]", ($activeForm === 'register') ? 'tabindex="-1"' : '', $template);
$template = str_replace("[login_btn_tabindex]", ($activeForm === 'login') ? 'tabindex="-1"' : '', $template);

echo $template;
?>