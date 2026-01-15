<?php
session_start();

if (isset($_SESSION["user_id"])) {
    unset($_SESSION["user_id"]);
    unset($_SESSION["username"]);
    unset($_SESSION["is_admin"]);
}

header("Location: /");