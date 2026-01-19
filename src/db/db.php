<?php

require_once dirname(__FILE__) . "/User.php";
require_once dirname(__FILE__) . "/Drink.php";
require_once dirname(__FILE__) . "/Ingredient.php";
require_once dirname(__FILE__) . "/Step.php";
require_once dirname(__FILE__) . "/Category.php";
require_once dirname(__FILE__) . "/Review.php";

$pdo = null;
try {
    $pdo = new PDO(
        "mysql:host=db;port=3306;dbname=progetto",
        "progetto",
        "progetto",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("PDO error: " . $e->getMessage());
}

$categoryDao = new PdoCategoryDao($pdo);
$userDao = new PdoUserDao($pdo);
$drinkDao = new PdoDrinkDao($pdo);
$stepDao = new PdoStepDao($pdo);
$ingredientDao = new PdoIngredientDao($pdo);
$reviewDao = new PdoReviewDao($pdo);