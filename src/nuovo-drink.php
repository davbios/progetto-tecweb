<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();

if (!isset($user)) {
    header("Location: index.php");
    exit;
}

$form = new Form(
    __FILE__,
    "",
    [
        "category" => "",
        "name" => "",
        "description" => "",
        "ingredients" => [],
        "steps" => [],
    ]
);
$form->loadDataFromSession();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Salva il contenuto del form nella sessione in modo che nel caso ci fossero errori 
    // i valori inseriti dall'utente possono essere recuperati.
    $form->saveValues($_POST);
    $ingredients = [];
    foreach ($_POST["ingredient-names"] as $key => $name) {
        $ingredients[] = [
            "name" => $name,
            "quantity" => $_POST["ingredient-quantities"][$key]
        ];
    }
    $form->setValue("ingredients", $ingredients);

    $poster = handleImageUpload("poster", "drink");
    if (!isset($poster)) {
        setPageError(__FILE__, "Immagine non valida.", "poster");
        header("Location: nuovo-drink.php");
        exit;
    }

    if (!isset($_POST["category"]) || !is_numeric($_POST["category"])) {
        setPageError(__FILE__, "Categoria non trovata.", "category");
        header("Location: nuovo-drink.php");
        exit;
    }
    $category = $categoryDao->findById($_POST["category"]);
    if ($category === null) {
        setPageError(__FILE__, "Categoria non trovata.", "category");
        header("Location: nuovo-drink.php");
        exit;
    }

    if (!isset($_POST["name"]) || empty(trim($_POST["name"]))) {
        setPageError(__FILE__, "Il nome non può essere vuoto.", "name");
        header("Location: nuovo-drink.php");
        exit;
    }

    if (!isset($_POST["description"]) || empty(trim($_POST["description"]))) {
        setPageError(__FILE__, "La descrizione non può essere vuota.", "description");
        header("Location: nuovo-drink.php");
        exit;
    }

    $drink = new Drink(
        trim($_POST["name"]),
        trim($_POST["description"]),
        $poster,
        $user,
        null,
        $category,
        null,
        null,
        null
    );
    try {
        $drink = $drinkDao->insert($drink);
    } catch (PDOException $e) {
        setPageError(__FILE__, 'Si è verificato un errore nel salvare il <span lang="en">drink</span>: ' . $e->getMessage());
        header("Location: nuovo-drink.php");
        exit;
    }

    foreach ($_POST["steps"] as $key => $step) {
        try {
            $stepDao->insert(new Step($key + 1, $step, $drink->getId(), null, null, null));
        } catch (PDOException $e) {
            setPageError(__FILE__, "Si è verificato un errore nel salvare i passaggi della preparazione: " . $e->getMessage());
            header("Location: nuovo-drink.php");
            exit;
        }
    }

    if (count($_POST["ingredient-names"]) !== count($_POST["ingredient-quantities"])) {
        setPageError(__FILE__, "Il numero di ingredienti e le quantità non coincidono.");
        header("Location: nuovo-drink.php");
        exit;
    }
    foreach ($_POST["ingredient-names"] as $key => $name) {
        try {
            $ingredientDao->insert(new Ingredient($name, intval($_POST["ingredient-quantities"][$key]), $drink->getId(), null, null, null));
        } catch (PDOException $e) {
            setPageError(__FILE__, "Si è verificato un errore nel salvare gli ingredienti: " . $e->getMessage());
            header("Location: nuovo-drink.php");
            exit;
        }
    }

    $form->clearSession();
    header("Location: drink.php?id=" . $drink->getId());
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    header("Location: index.php");
    exit;
}

$template = getTemplate("layout");
$template = str_replace("[title]", "Nuovo drink | Arte del Cocktail", $template);
$template = str_replace("[description]", "Crea un nuovo drink.", $template);
$template = str_replace("[keywords]", "", $template);
$template = str_replace("[navbar]", getNavbar("nuovo", true), $template);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » Nuovo drink', $template);

$content = getTemplate("nuovo_drink");
$content = str_replace("[page_title]", 'Nuovo <span lang="en">drink</span>', $content);

$error = getPageError(__FILE__);
$content = str_replace(
    "[error]",
    isset($error) ? str_replace("[message]", $error, getTemplate("section_error")) : "",
    $content
);

$content = displayFormError(__FILE__, "poster", $content);

$categories = $categoryDao->getAll(100, 0);
$categoriesContent = "";
foreach ($categories as $category) {
    $categoriesContent .= '<option value="' . $category->getId() . '"';
    if ($form->getValue("category") === $category->getId()) {
        $categoriesContent .= ' selected';
    }
    $categoriesContent .= '>' . $category->name . "</option>\n";
}
$content = str_replace("[categories]", $categoriesContent, $content);

if (empty($form->getValue("ingredients"))) {
    $form->setValue("ingredients", [
        [
            "quantity" => "",
            "name" => "",
        ]
    ]);
}
$ingredientsListContent = "";
foreach ($form->getValue("ingredients") as $id => $ingredient) {
    $ingredientContent = getTemplate("drink_form_ingredient");
    $ingredientContent = str_replace("[id]", $id, $ingredientContent);
    $ingredientContent = str_replace("[quantity]", $ingredient["quantity"], $ingredientContent);
    $ingredientContent = str_replace("[name]", $ingredient["name"], $ingredientContent);
    $ingredientsListContent .= $ingredientContent;
}
$content = str_replace("[ingredients]", $ingredientsListContent, $content);

if (empty($form->getValue("steps"))) {
    $form->setValue("steps", [""]);
}
$stepsListContent = "";
foreach ($form->getValue("steps") as $id => $value) {
    $stepContent = getTemplate("drink_form_step");
    $stepContent = str_replace("[id]", $id, $stepContent);
    $stepContent = str_replace("[value]", $value, $stepContent);
    $stepsListContent .= $stepContent;
}
$content = str_replace("[steps]", $stepsListContent, $content);

$content = $form->render($content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>