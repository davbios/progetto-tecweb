<?php
require_once dirname(__FILE__) . "/app/global.php";

if (isset($_POST['accept'])) {
    $_SESSION['disclaimer_accepted'] = true;
    redirectTo("index.php");
    exit();
}
$template = getTemplate("disclaimer");
echo $template;
?>