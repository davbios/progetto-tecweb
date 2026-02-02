<?php
require_once dirname(__FILE__) . "/app/global.php";

$user = getLoggedUser();

if ($user === null) {
    redirectTo("login.php");
    exit;
}

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    redirectNotFound();
    exit;
}

$drink = null;
try {
    $drink = $drinkDao->findById(intval($_GET["id"]));
    if ($drink === null) {
        redirectNotFound();
        exit;
    }
} catch (PDOException $e) {
    setPageError(__FILE__, $e->getMessage());
}

$drinkIngredients = [];
$drinkSteps = [];
try {
    $drinkIngredients = $ingredientDao->getAllForDrink($drink->getId());
    $drinkSteps = $stepDao->getAllForDrink($drink->getId());
} catch (PDOException $e) {
    setPageError(__FILE__, $e->getMessage());
}

$form = new Form(
    __FILE__,
    "",
    [
        "category" => $drink->getCategory()->getId(),
        "name" => $drink->name,
        "description" => $drink->description,
        "ingredients" => $drinkIngredients,
        "steps" => $drinkSteps,
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

    $redirectLoc = "modifica-drink.php";
    $redirectParam = ["id" => $drink->getId()];
    $poster = handleImageUpload("poster", "drink");
    if ($poster === null) {
        setPageError(__FILE__, "Immagine non valida.", "poster");
        redirectTo($redirectLoc, $redirectParam);
        exit;
    }

    if (!isset($_POST["category"]) || !is_numeric($_POST["category"])) {
        setPageError(__FILE__, "Categoria non trovata.", "category");
        redirectTo($redirectLoc, $redirectParam);
        exit;
    }
    $category = $categoryDao->findById($_POST["category"]);
    if ($category === null) {
        setPageError(__FILE__, "Categoria non trovata.", "category");
        redirectTo($redirectLoc, $redirectParam);
        exit;
    }

    if (!isset($_POST["name"]) || empty(trim($_POST["name"]))) {
        setPageError(__FILE__, "Il nome non può essere vuoto.", "name");
        redirectTo($redirectLoc, $redirectParam);
        exit;
    }

    if (!isset($_POST["description"]) || empty(trim($_POST["description"]))) {
        setPageError(__FILE__, "La descrizione non può essere vuota.", "description");
        redirectTo($redirectLoc, $redirectParam);
        exit;
    }

    $drink->name = htmlspecialchars(trim($_POST["name"]));
    $drink->description = htmlspecialchars(trim($_POST["description"]));
    $drink->poster = $poster;
    $drink->category = $category;
    try {
        $drinkDao->update($drink);
    } catch (PDOException $e) {
        setPageError(__FILE__, 'Si è verificato un errore nel salvare il <span lang="en">drink</span>: ' . $e->getMessage());
        redirectTo($redirectLoc, $redirectParam);
        exit;
    }

    // Per semplicita' nell'aggiornamento del link i passi di preparazione e gli ingredienti vengono e eliminati e ricreati

    foreach ($drinkSteps as $step) {
        try {
            $stepDao->delete($step);
        } catch (PDOException $e) {
            setPageError(__FILE__, $e->getMessage());
            redirectTo($redirectLoc, $redirectParam);
            exit;
        }
    }
    foreach ($_POST["steps"] as $key => $step) {
        try {
            $stepDao->insert(new Step($key + 1, $step, $drink->getId(), null, null, null));
        } catch (PDOException $e) {
            setPageError(__FILE__, "Si è verificato un errore nel salvare i passaggi della preparazione: " . $e->getMessage());
            redirectTo($redirectLoc, $redirectParam);
            exit;
        }
    }

    if (count($_POST["ingredient-names"]) !== count($_POST["ingredient-quantities"])) {
        setPageError(__FILE__, "Il numero di ingredienti e le quantità non coincidono.");
        redirectTo($redirectLoc, $redirectParam);
        exit;
    }
    foreach ($drinkIngredients as $ingredient) {
        try {
            $ingredientDao->delete($ingredient);
        } catch (PDOException $e) {
            setPageError(__FILE__, $e->getMessage());
            redirectTo($redirectLoc, $redirectParam);
            exit;
        }
    }
    foreach ($_POST["ingredient-names"] as $key => $name) {
        try {
            $ingredientDao->insert(new Ingredient($name, $_POST["ingredient-quantities"][$key], $drink->getId(), null, null, null));
        } catch (PDOException $e) {
            setPageError(__FILE__, "Si è verificato un errore nel salvare gli ingredienti: " . $e->getMessage());
            redirectTo($redirectLoc, $redirectParam);
            exit;
        }
    }

    $form->clearSession();
    redirectTo("drink.php", ["id" => $drink->getId()]);
    exit;
} elseif ($_SERVER["REQUEST_METHOD"] !== "GET") {
    redirectTo("index.php");
    exit;
}

$template = getTemplate("layout");
$template = str_replace("[title]", "Modifica drink | Arte del Cocktail", $template);
$template = str_replace("[description]", "Modifica il drink " . $drink->name . ".", $template);
$template = str_replace("[keywords]", "", $template);
$template = str_replace("[navbar]", getNavbar(__FILE__, "id=" . $drink->getId(), true), $template);
$template = str_replace("[breadcrumb]", '<a href="index.php" lang="en">Home</a> » <a href="esplora.php">Esplora</a> » <a href="drink.php?id=' . $drink->getId() . '">' . $drink->name . '</a> » Modifica <span class="en">drink</span>', $template);

$content = getTemplate("nuovo_drink");
$content = str_replace("[page_title]", 'Modifica <span lang="en">drink</span>', $content);
$content = str_replace("[cancel_link]", 'drink.php?id=' . $drink->getId(), $content);
$content = str_replace("[submit_text]", 'Salva', $content);
$content = str_replace("[form_url]", "modifica-drink.php?id=" . $drink->getId(), $content);

$content = displayFormError(__FILE__, "poster", $content);

$categories = [];
try {
    $categories = $categoryDao->getAll();
} catch (PDOException $e) {
    setPageError(__FILE__, $e->getMessage());
}
$categoriesContent = "";
foreach ($categories as $category) {
    $categoriesContent .= '<option value="' . $category->getId() . '"';
    if ($form->getValue("category") === $category->getId()) {
        $categoriesContent .= ' selected';
    }
    $categoriesContent .= '>' . $category->name . "</option>\n";
}
$content = str_replace("[categories]", $categoriesContent, $content);

$error = getPageError(__FILE__);
$content = str_replace(
    "[error]",
    isset($error) ? str_replace("[message]", $error, getTemplate("section_error")) : "",
    $content
);

$ingredientsListContent = "";
foreach ($drinkIngredients as $number => $ingredient) {
    $ingredientContent = getTemplate("drink_form_ingredient");
    $ingredientContent = str_replace("[id]", $ingredient->getId(), $ingredientContent);
    $ingredientContent = str_replace("[number]", $number, $ingredientContent);
    $ingredientContent = str_replace("[quantity]", $ingredient->quantity, $ingredientContent);
    $ingredientContent = str_replace("[name]", $ingredient->name, $ingredientContent);
    $ingredientsListContent .= $ingredientContent;
}
$content = str_replace("[ingredients]", $ingredientsListContent, $content);

$stepsListContent = "";
foreach ($drinkSteps as $number => $step) {
    $stepContent = getTemplate("drink_form_step");
    $stepContent = str_replace("[id]", $step->getId(), $stepContent);
    $stepContent = str_replace("[number]", $number, $stepContent);
    $stepContent = str_replace("[value]", $step->description, $stepContent);
    $stepsListContent .= $stepContent;
}
$content = str_replace("[steps]", $stepsListContent, $content);

$content = $form->render($content);

$template = str_replace("[content]", $content, $template);

echo $template;
?>