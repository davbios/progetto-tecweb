<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();
if ($user) {
    header("Location: index.php");
    exit;
}

$formData = [
    'login' => ['username' => ''],
    'register' => ['email' => '', 'username' => '']
];

if (isset($_SESSION['form_data'])) {
    $formData = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? null;
    $redirectLoc = "login.php?" . $_SERVER['QUERY_STRING'];

    if ($action === "login") {
        $errorMessage = "L'username e/o password inseriti sono errati. Si prega di riprovare.";
        if (
            !isset($_POST["username"]) || empty(trim($_POST["username"])) ||
            !isset($_POST["password"]) || empty(trim($_POST["password"]))
        ) {
            setPageError(__FILE__, $errorMessage, "login");
            redirectTo($redirectLoc);
            exit;
        }

        // accesso utente
        $formData["login"]["username"] = htmlspecialchars(trim($_POST["username"]));
        $user = $userDao->findByUsernameAndPassword(trim($_POST["username"]), trim($_POST["password"]));

        if ($user) {
            $_SESSION["user_id"] = $user->getId();
            $_SESSION["username"] = $user->getUsername();
            $_SESSION["is_admin"] = $user->isAdmin();
            if (isset($_GET["from"])) {
                $redirectLoc = urldecode($_GET["from"]);
            } else {
                $redirectLoc = "index.php";
            }
        } else {
            setPageError(__FILE__, $errorMessage, "login");
            $_SESSION['form_data'] = $formData;
            $_SESSION['active_form'] = 'login';
        }
    } elseif ($action === "register") {
        // registrazione
        if (!isset($_POST["email"]) || empty(trim($_POST["email"]))) {
            setPageError(__FILE__, "Email non valida.", "register_email");
            redirectTo($redirectLoc);
            exit;
        }
        if (!isset($_POST["username"]) || empty(trim($_POST["username"]))) {
            setPageError(__FILE__, "Username non valido.", "register_username");
            redirectTo($redirectLoc);
            exit;
        }
        if (!isset($_POST["password"]) || empty(trim($_POST["password"]))) {
            setPageError(__FILE__, "Password non valida.", "register_password");
            redirectTo($redirectLoc);
            exit;
        }
        $existingUser = $userDao->findByUsername(trim($_POST["username"]));
        $formData['register']['email'] = htmlspecialchars(trim($_POST["email"]));
        $formData['register']['username'] = htmlspecialchars(trim($_POST["email"]));

        if (isset($existingUser)) {
            setPageError(__FILE__, "L'username è già utilizzato da un altro utente. Scegli un altro username.", "register_username");
            $_SESSION['form_data'] = $formData;
            $_SESSION['active_form'] = 'register';
        } else {
            $passwordHash = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
            $user = new User(trim($_POST["username"]), trim($_POST["email"]), $passwordHash, '', null, false);
            $user = $userDao->insert($user);

            $_SESSION["user_id"] = $user->getId();
            $_SESSION["username"] = $user->getUsername();
            $_SESSION["is_admin"] = false;
            $redirectLoc = "index.php";
        }
    }

    redirectTo($redirectLoc);
    exit;
}

$activeForm = $_SESSION['active_form'] ?? 'login';
if (isset($_SESSION['active_form'])) {
    unset($_SESSION['active_form']);
}

$template = getTemplate("login");

$template = str_replace("[active_form_class]", ($activeForm === 'register') ? 'active' : '', $template);
$template = str_replace("[login_username]", htmlspecialchars($formData['login']['username']), $template);
$template = str_replace("[register_email]", htmlspecialchars($formData['register']['email']), $template);
$template = str_replace("[register_username]", htmlspecialchars($formData['register']['username']), $template);
$template = str_replace("[register_pct]", "", $template);
$template = str_replace("[reg_btn_tabindex]", ($activeForm === 'register') ? 'tabindex="-1"' : '', $template);
$template = str_replace("[login_btn_tabindex]", ($activeForm === 'login') ? 'tabindex="-1"' : '', $template);
$template = str_replace("[form_param]", isset($_GET["from"]) ? '?from='. $_GET["from"] : '', $template);

$template = displayFormError(__FILE__, "login", $template);
$template = displayFormError(__FILE__, "login_username", $template);
$template = displayFormError(__FILE__, "login_password", $template);
$template = displayFormError(__FILE__, "register_username", $template);
$template = displayFormError(__FILE__, "register_email", $template);
$template = displayFormError(__FILE__, "register_password", $template);

echo $template;
?>