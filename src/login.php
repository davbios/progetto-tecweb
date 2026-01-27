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
        $picture = null;

        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $_FILES['picture']['tmp_name']);
            finfo_close($fileInfo);
            if (in_array($mimeType, $allowedTypes)) {
                $extension = pathinfo($_FILES['picture']['name'], PATHINFO_EXTENSION);
                $picture = uniqid('user_') . '.' . $extension;
                $uploadPath = 'uploads/profile_pictures/' . $picture;
                if (!is_dir('uploads/profile_pictures')) {
                    mkdir('uploads/profile_pictures', 0777, true);
                }
                move_uploaded_file($_FILES['picture']['tmp_name'], $uploadPath);
            }
        }
        $existingUser = $userDao->findByUsername($username);
        
        if ($existingUser) {
            $errori[] = "L'username è già utilizzato da un altro utente. Scegli un altro username.";
            $_SESSION['login_errors'] = $errori;
            $_SESSION['form_data']['register']['email'] = htmlspecialchars($email);
            $_SESSION['form_data']['register']['username'] = htmlspecialchars($username);
            $_SESSION['active_form'] = 'register';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $user = new User($username, $email, $passwordHash, $picture, false);
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

$template = getTemplate("layout");
$template = str_replace('<body onload="onLoad()">', '<body class="login-page" onload="onLoad()">', $template);
$template = str_replace("[title]", "Arte del Cocktail | Accesso e Registrazione", $template);
$template = str_replace("[description]", "Accedi o registrati per scoprire e creare fantastici cocktail", $template);
$template = str_replace("[keywords]", "login, registrazione, cocktail, drink, accesso, account", $template);
$template = str_replace("[navbar]", getNavbar("login", isset($_SESSION["user_id"])), $template);
$template = str_replace("[breadcrumb]", '<a href="/" lang="en">Home</a> » Accesso/Registrazione', $template);

$content = getTemplate("login");

$errorMessages = "";
if (!empty($errori)) {
    $errorTemplate = getTemplate("error_messages");
    $messages = "";
    foreach ($errori as $errore) {
        $messages .= '<p>' . htmlspecialchars($errore) . '</p>';
    }
    $errorMessages = str_replace("[messages]", $messages, $errorTemplate);
}

$content = str_replace("[error_messages]", $errorMessages, $content);
$content = str_replace("[active_form_class]", ($activeForm === 'register') ? 'active' : '', $content);
$content = str_replace("[login_username]", htmlspecialchars($formData['login']['username']), $content);
$content = str_replace("[register_email]", htmlspecialchars($formData['register']['email']), $content);
$content = str_replace("[register_username]", htmlspecialchars($formData['register']['username']), $content);
$content = str_replace("[register_pct]", "",  $content);
$content = str_replace("[reg_btn_tabindex]", ($activeForm === 'register') ? 'tabindex="-1"' : '', $content);
$content = str_replace("[login_btn_tabindex]", ($activeForm === 'login') ? 'tabindex="-1"' : '', $content);

$template = str_replace('<main>', '<div class="login-main-container">', $template);
$template = str_replace('</main>', '</div>', $template);
$template = str_replace("[content]", $content, $template);

echo $template;
?>