<?php
require_once dirname(__FILE__) . "/app/global.php";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['accept'])) {
    $_SESSION['disclaimer_accepted'] = true;
    header('Location: index.php');
    exit();
}
$template = getTemplate("disclaimer");
echo $template;
?>