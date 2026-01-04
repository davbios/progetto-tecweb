<?php
require_once dirname(__FILE__) . "/db/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST"):
    $category = $categoryDao->findById($_POST["category"]);
    if ($category === null) {
        die("Categoria non trovata");
    }

    $user = $userDao->findById(1);

    $drink = new Drink($_POST["name"], $_POST["description"], $_POST["poster"], $user, $category, null, null, null);

    // try {
    $drink = $drinkDao->insert($drink);
    // } catch (PDOException $e) {
    //     die($e->getMessage());
    // }
    foreach ($_POST["steps"] as $key => $step) {
        // try {
        $stepDao->insert(new Step($key + 1, $step, $drink->getId(), null, null, null));
        // } catch (PDOException $e) {
        //     die($e->getMessage());
        // }
    }

    if (count($_POST["ingredient-names"]) !== count($_POST["ingredient-quantities"])) {
        die("Il numero di ingredienti e le quantita' non coincidono");
    }
    foreach ($_POST["ingredient-names"] as $key => $name) {
        // try {
        $ingredientDao->insert(new Ingredient($name, intval($_POST["ingredient-quantities"][$key]), $drink->getId(), null, null, null));
        // } catch (PDOException $e) {
        //     die($e->getMessage());
        // }
    }
    header("Location: drink.php?id=" . $drink->getId());
else:
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuovo drink</title>
        <link rel="stylesheet" href="css/drink.css">
    </head>

    <body>
        <main class="container">
            <h1>Nuovo drink</h1>
            <form action="nuovo-drink.php" method="POST" class="drink-form">
                <div class="row">
                    <div class="form-group col-4">
                        <label for="category">Categoria</label>
                        <select name="category" id="category" required>
                            <option disabled selected>Nessuna</option>
                            <?php
                            $categories = $categoryDao->getAll(100, 0);
                            foreach ($categories as $category) {
                                echo "<option value=\"" . $category->getId() . "\">" . $category->name . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-8">
                        <label for="name">Nome</label>
                        <input type="text" id="name" name="name" placeholder="es. Gin Tonic" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="description">Descrizione</label>
                    <textarea id="description" name="description" placeholder="es. Un grande classico" required></textarea>
                </div>
                <div class="form-group">
                    <fieldset>
                        <legend>Immagine</legend>
                        <input type="file" id="poster" name="poster" required>
                    </fieldset>
                </div>
                <div class="form-group">
                    <fieldset>
                        <legend>Ingredienti</legend>
                        <ul id="ingredients-list">
                            <li>
                                <div class="row">
                                    <input type="text" class="ingredient-quantity" id="ingredient-quanity-1"
                                        name="ingredient-quantities[]" placeholder="Quantità" required>
                                    <input type="text" class="ingredient-name" id="ingredient-name-1"
                                        name="ingredient-names[]" placeholder="Nome" required>
                                    <button type="button" class="btn-remove" onclick="removeIngredient(1)"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="feather feather-trash">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path
                                                d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </li>
                        </ul>
                        <button type="button" class="btn-add" onclick="addIngredient()">Aggiungi</button>
                    </fieldset>
                </div>
                <div class="form-group">
                    <fieldset>
                        <legend>Preparazione</legend>
                        <ol id="steps-list">
                            <li>
                                <div class="row">
                                    <textarea class="preparation-step" id="preparation-1" name="steps[]"
                                        placeholder="Procedimento" required></textarea>
                                    <div><button type="button" class="btn-remove" onclick="removeStep(1)"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-trash">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path
                                                    d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                </path>
                                            </svg></button></div>
                                </div>
                            </li>
                        </ol>
                        <button type="button" class="btn-add" onclick="addStep()">Aggiungi</button>
                    </fieldset>
                </div>
                <button type="submit" class="btn-submit">Crea</button>
            </form>
        </main>

        <script defer>
            function addIngredient() {
                var list = document.getElementById('ingredients-list');
                var fieldId = list.childElementCount + 1;
                var item = document.createElement('li');
                item.innerHTML += '<div class="row">' +
                    `<input type="text" class="ingredient-quantity" id="ingredient-quanity-${fieldId}" name="ingredient-quantities[]" placeholder="Quantità" required>` +
                    `<input type="text" class="ingredient-name" id="ingredient-name-${fieldId}" name="ingredient-names[]" placeholder="Nome" required>` +
                    `<button type="button" class="btn-remove" onclick="removeIngredient(${fieldId})"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button>` +
                    '</div>';
                list.appendChild(item);
            }

            function addStep() {
                var list = document.getElementById('steps-list');
                var fieldId = list.childElementCount + 1;
                var item = document.createElement('li');
                item.innerHTML = `<div class="row">` +
                    `<textarea class="preparation-step" id="preparation-${fieldId}" name="steps[]" placeholder="Procedimento" required></textarea>` +
                    `<div><button type="button" class="btn-remove" onclick="removeStep(${fieldId})"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></button></div>` +
                    '</div>';
                list.appendChild(item)
            }

            function removeIngredient(id) {
                var list = document.getElementById('ingredients-list');
                var el = document.getElementById('ingredient-name-' + id).parentNode.parentNode;
                list.removeChild(el);
            }

            function removeStep(id) {
                var list = document.getElementById('steps-list');
                var el = document.getElementById('preparation-' + id).parentNode.parentNode;
                list.removeChild(el);
            }
        </script>
    </body>

    </html>
    <?php
endif;
?>