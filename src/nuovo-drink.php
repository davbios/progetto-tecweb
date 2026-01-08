<?php
require_once dirname(__FILE__) . "/db/db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST"):
    $uploaddir = '/var/www/uploads/';
    $uploadfile = $uploaddir . basename($_FILES['file']['name']);

    if (!move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
        die("Possible file upload attack!");
    }

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
            <form enctype="multipart/form-data" action="__URL__" method="POST" class="drink-form">
                <fieldset class="info-fieldset">
                    <legend>Informazioni generali</legend>

                    <div class="form-group input-category">
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

                    <div class="form-group input-name">
                        <label for="name">Nome</label>
                        <input type="text" id="name" name="name" placeholder="es. Gin Tonic" required>
                    </div>

                    <div class="form-group input-description">
                        <label for="description">Descrizione</label>
                        <textarea id="description" name="description" placeholder="es. Un grande classico"
                            required></textarea>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Immagine</legend>

                    <label for="poster">Carica un file</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="30000" />
                    <input type="file" id="poster" name="poster" accept="image/jpeg,image/png" required>
                </fieldset>

                <fieldset class="list-fieldset">
                    <legend>Ingredienti</legend>

                    <ul id="ingredients-list" class="form-list">
                        <li>
                            <div class="form-row">
                                <div class="form-group input-quantity">
                                    <label for="ingredient-quanity-1">Quantità</label>
                                    <input type="text" class="ingredient-quantity" id="ingredient-quanity-1"
                                        name="ingredient-quantities[]" placeholder="es. 12oz" required>
                                </div>
                                <div class="form-group">
                                    <label for="ingredient-name-1">Nome</label>
                                    <input type="text" class="ingredient-name" id="ingredient-name-1"
                                        name="ingredient-names[]" placeholder="es. Vodka" required>
                                </div>

                                <button type="button" class="btn-remove" onclick="removeIngredient(1)">
                                    <img src="img/trash.svg" alt="Rimuovi ingrediente">
                                </button>
                            </div>
                        </li>
                    </ul>

                    <button type="button" class="btn-add" onclick="addIngredient()">Aggiungi</button>
                </fieldset>

                <fieldset class="list-fieldset">
                    <legend>Preparazione</legend>

                    <ol id="steps-list" class="form-list">
                        <li>
                            <div class="row">
                                <div class="form-group">
                                    <label for="preparation-1">Procedimento</label>
                                    <textarea class="preparation-step" id="preparation-1" name="steps[]"
                                        placeholder="Procedimento" required></textarea>
                                </div>

                                <button type="button" class="btn-remove" onclick="removeStep(1)">
                                    <img src="img/trash.svg" alt="Rimuovi passo di preparazione">
                                </button>
                            </div>
                        </li>
                    </ol>

                    <button type="button" class="btn-add" onclick="addStep()">Aggiungi</button>
                </fieldset>

                <button type="submit" class="btn-submit">Crea</button>
            </form>
        </main>

        <script defer>
            function addIngredient() {
                var list = document.getElementById('ingredients-list');
                var fieldId = list.childElementCount + 1;
                var item = document.createElement('li');
                item.innerHTML = '<div class="form-row">' +
                    '<div class="form-group input-quantity">' +
                    `<label for="ingredient-quanity-${fieldId}">Quantità</label>` +
                    `<input type="text" class="ingredient-quantity" id="ingredient-quanity-${fieldId}" ` +
                    ' name="ingredient-quantities[]" placeholder="es. 12oz" required>' +
                    '</div>' +
                    '<div class="form-group">' +
                    `<label for="ingredient-name-${fieldId}">Nome</label>` +
                    `<input type="text" class="ingredient-name" id="ingredient-name-${fieldId}" ` +
                    ' name="ingredient-names[]" placeholder="es. Vodka" required>' +
                    '</div>' +
                    `<button type="button" class="btn-remove" onclick="removeIngredient(${fieldId})">` +
                    '<img src="img/trash.svg" alt="Rimuovi ingrediente">' +
                    '</button>' +
                    '</div>'
                list.appendChild(item);
            }

            function addStep() {
                var list = document.getElementById('steps-list');
                var fieldId = list.childElementCount + 1;
                var item = document.createElement('li');
                item.innerHTML = '<div class="row">' +
                    '<div class="form-group">' +
                    `<label for="preparation-${fieldId}">Procedimento</label>` +
                    `<textarea class="preparation-step" id="preparation-${fieldId}" name="steps[]" ` +
                    ` placeholder="Procedimento" required></textarea>` +
                    '</div>' +
                    `<button type="button" class="btn-remove" onclick="removeStep(${fieldId})">` +
                    '<img src="img/trash.svg" alt="Rimuovi passo di preparazione">' +
                    '</button>' +
                    '</div>'
                list.appendChild(item)
            }

            function removeIngredient(id) {
                var list = document.getElementById('ingredients-list');
                var el = document.getElementById('ingredient-name-' + id).parentNode.parentNode.parentNode;
                list.removeChild(el);
            }

            function removeStep(id) {
                var list = document.getElementById('steps-list');
                var el = document.getElementById('preparation-' + id).parentNode.parentNode.parentNode;
                list.removeChild(el);
            }
        </script>
    </body>

    </html>
    <?php
endif;
?>