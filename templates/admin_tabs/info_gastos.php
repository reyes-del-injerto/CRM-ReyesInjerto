<?php
// Mostrar todos los errores (útil para depuración)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluye la conexión a la base de datos
require_once "../../scripts/common/connection_db.php";

try {
    // Definir el mes de septiembre
    $year = date('Y'); // Usar el año actual, o puedes definir un año específico
    $startDate = new DateTime("{$year}-09-01");
    // Calcular el último día del mes de septiembre
    $endDate = new DateTime("{$year}-09-01");
    $endDate->modify('last day of this month');

    // Ajustar la hora para el inicio y fin del mes
    $startDateSQL = $startDate->format('Y-m-d 00:00:00');
    $endDateSQL = $endDate->format('Y-m-d 23:59:59');

    // Consulta SQL para categorías
    $sql_categories = "
    SELECT 
        c.id AS category_id,
        c.name AS category_name,
        IFNULL(SUM(t.amount), 0) AS total_expensed_amount,
        c.amount
    FROM 
        ad_categories c
    LEFT JOIN 
        ad_transactions t ON c.id = t.cat_id
        AND t.date BETWEEN '{$startDateSQL}' AND '{$endDateSQL}'
    GROUP BY 
        c.id, c.name;
    ";
    $result_categories = $conn->query($sql_categories);
    $categories = [];

    if ($result_categories->num_rows > 0) {
        while ($row = $result_categories->fetch_assoc()) {
            $categories[$row['category_id']] = [
                'name' => $row['category_name'],
                'id' => $row['category_id'],
                'amount' => $row['amount'],
                'total_expensed' => $row['total_expensed_amount'],
                'subcategories' => [],
                'subcategories_total' => 0
            ];
        }
    }

    // Consulta SQL para subcategorías
    $sql_subcategories = "
    SELECT 
        s.id AS subcategory_id,
        s.category_id,
        s.name AS subcategory_name,
        s.description,
        IFNULL(SUM(t.amount), 0) AS total_expensed
    FROM 
        ad_subcategories s
    LEFT JOIN 
        ad_transactions t ON s.id = t.subcategory
        AND t.date BETWEEN '{$startDateSQL}' AND '{$endDateSQL}'
    GROUP BY 
        s.id, s.category_id, s.name, s.description;
    ";
    $result_subcategories = $conn->query($sql_subcategories);

    if ($result_subcategories->num_rows > 0) {
        while ($row = $result_subcategories->fetch_assoc()) {
            if (isset($categories[$row['category_id']])) {
                $categories[$row['category_id']]['subcategories'][] = [
                    'subcategory_id' => $row['subcategory_id'], // Agregar subcategory_id aquí
                    'name' => $row['subcategory_name'],
                    'description' => $row['description'],
                    'amount' => abs($row['total_expensed']) // Convertir el monto a positivo
                ];
                $categories[$row['category_id']]['subcategories_total'] += $row['total_expensed'];
            }
        }
    }

    // Ordenar categorías por monto gastado en orden descendente
    usort($categories, function ($a, $b) {
        return $b['total_expensed'] <=> $a['total_expensed'];
    });

    // Establecer el nombre del mes en español como septiembre
    $monthNameSpanish = 'Septiembre';

    // Calcular la suma total de gastos presupuestados y gastados
    $total_expensed = abs(array_sum(array_column($categories, 'subcategories_total')));
    $total_budgeted = array_sum(array_column($categories, 'amount'));

    // Consulta SQL para sub-subcategorías
    $sql_subsubcategories = "
    SELECT 
        ss.id AS subsubcategory_id,
        ss.subcategory_id,
        ss.name AS subsubcategory_name,
        ss.description,
        IFNULL(SUM(t.amount), 0) AS total_expensed
    FROM 
        ad_sub_subcategories ss
    LEFT JOIN 
        ad_transactions t ON ss.id = t.sub_subcategory
        AND t.date BETWEEN '{$startDateSQL}' AND '{$endDateSQL}'
    GROUP BY 
        ss.id, ss.subcategory_id, ss.name, ss.description;
    ";
    $result_subsubcategories = $conn->query($sql_subsubcategories);

    if ($result_subsubcategories->num_rows > 0) {
        while ($row = $result_subsubcategories->fetch_assoc()) {
            foreach ($categories as &$category) {
                foreach ($category['subcategories'] as &$subcategory) {
                    if ($subcategory['subcategory_id'] == $row['subcategory_id']) {
                        if (!isset($subcategory['subsubcategories'])) {
                            $subcategory['subsubcategories'] = [];
                            $subcategory['subsubcategories_total'] = 0;
                        }
                        $subcategory['subsubcategories'][] = [
                            'name' => $row['subsubcategory_name'],
                            'description' => $row['description'],
                            'amount' => abs($row['total_expensed'])
                        ];
                        $subcategory['subsubcategories_total'] += $row['total_expensed'];
                    }
                }
            }
        }
    }
} catch (Exception $e) {
    // Manejo de errores
    echo "Ocurrió un error: " . $e->getMessage();
}

?>


<div class="contenedor_gral_gastos">
    <h2>Informe de gastos mensuales - <?php echo isset($monthNameSpanish) ? htmlspecialchars($monthNameSpanish) : 'N/A'; ?></h2>

    <button type="button" class="btn btn-outline-warning" id="add_new_expense">Nuevo gasto</button>

    <div class="contedor_cards_principales">
        <div class="card_gastos sweeperCard o-hidden">
            <div class="containers_gastos">
                <div class="icon_gastos">
                    <img src="./assets/img/icons/money-svgrepo-com.svg" alt="">
                </div>
                <div class="title_gastos my-3">Gastado</div>
                <div class="linkMore_gastos mt-3">
                    $<?php echo isset($total_expensed) ? number_format($total_expensed, 2, ".", ",") : '0.00'; ?>
                    <img src="./assets/img/icons/full.svg" alt="">
                </div>
            </div>
        </div>

        <div class="card_gastos sweeperCard o-hidden">
            <div class="containers_gastos">
                <div class="icon_gastos">
                    <img src="./assets/img/icons/money-svgrepo-com.svg" alt="">
                </div>
                <div class="title_gastos my-3">Presupuestado:</div>
                <div class="linkMore_gastos mt-3">
                    $<?php echo isset($total_budgeted) ? number_format($total_budgeted + 250000, 2, ".", ",") : '201.00'; ?>
                    <img src="./assets/img/icons/full.svg" alt="">
                </div>
            </div>
        </div>
    </div>

    <div class="Nomina">
        <h4>Gastos Detallados</h4>
        <div class="container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Categoría</th>
                        <th>Subcategoría</th>
                        <th>Sub-subcategoría</th>
                        <th>Descripción</th>
                        <th>Monto Gastado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)) { ?>
                        <?php foreach ($categories as $category) { ?>
                            <tr>
                                <td rowspan="<?php echo max(1, array_sum(array_map(function ($subcategory) {
                                                    return isset($subcategory['subsubcategories']) ? count($subcategory['subsubcategories']) : 1;
                                                }, $category['subcategories']))); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </td>
                                <?php if (!empty($category['subcategories'])) { ?>
                                    <?php foreach ($category['subcategories'] as $subcategory) { ?>
                                        <td rowspan="<?php echo max(1, isset($subcategory['subsubcategories']) ? count($subcategory['subsubcategories']) : 1); ?>">
                                            <?php echo htmlspecialchars($subcategory['name']); ?>
                                        </td>
                                        <?php if (!empty($subcategory['subsubcategories'])) { ?>
                                            <?php foreach ($subcategory['subsubcategories'] as $index => $subsubcategory) { ?>
                                                <?php if ($index > 0) echo "<tr>"; ?>
                                                <td><?php echo htmlspecialchars($subsubcategory['name']); ?></td>
                                                <td><?php echo htmlspecialchars($subsubcategory['description']); ?></td>
                                                <td><?php echo "$" . number_format($subsubcategory['amount'], 2, ".", ","); ?></td>
                                                <?php if ($index > 0) echo "</tr>"; ?>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <!-- Si no hay sub-subcategorías, muestra la subcategoría con su monto -->
                                            <td colspan="2">No hay sub-subcategorías</td>
                                            <td><?php echo "$" . number_format($subcategory['amount'], 2, ".", ","); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <td colspan="4">No hay subcategorías</td>
                    </tr>
                <?php } ?>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5">No hay datos disponibles</td>
            </tr>
        <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>


<!-- MODAL -->
<div class="modal fade" id="expenseModal" tabindex="-1" aria-labelledby="expenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">
                    Añadir Gasto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="expenseForm">
                    <div class="row">
                        <input type="hidden" value=1 name="type_submit" id="type_submit" class="form-control">
                        <input type="hidden" value="" name="transaction_id" id="transaction_id" class="form-control">
                        <input type="hidden" value="" name="user_id" id="user_id" class="form-control">
                        <script>
                            const user_id = localStorage.getItem("user_id")
                            document.getElementById("user_id").value = user_id;
                        </script>
                        <div class="col-md-12">
                            <div class="">
                                <textarea required id="description" name="description" type="text" class="form-control" placeholder="Descripción de la transacción" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="">

                                <label for="exampleDataList" class="form-label">Establecimiento</label>
                                <input required id="store" name="store" list="datalistOptions" type="text" class="form-control" placeholder="Establecimiento" />
                                <datalist id="datalistOptions">
                                    <option value="Sams club">
                                    <option value="Office depot">
                                    <option value="Walmart">
                                    <option value="Littel Caesar´s">
                                    <option value="Hotel Paragon">
                                    <option value="Farmacia del Ahorro">
                                    <option value="Oxxo">
                                    <option value="Home Depot">
                                    <option value="Seven eleven">
                                </datalist>
                            </div>
                        </div>

                        <div class="col-md-6 mt-6">
                            <div class="">
                                <label for="share-with">Categoría:</label>
                                <select required id="cat_id" name="cat_id" class="form-control" required>
                                    <option value="" selected disabled></option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mt-6">
                            <div class="">
                                <label for="share-with">Subcategoría:</label>
                                <select required id="sub_cat_id" name="sub_cat_id" class="form-control" required>
                                    <option value="" selected disabled></option>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-6 mt-6" id="contenedor_sub" style="display: none;">
                            <div class="">
                                <label for="share-with">Sub_sub_categoría:</label>
                                <select required id="sub_sub_cat_id" name="sub_sub_cat_id" class="form-control" required>
                                    <option value="" selected disabled></option>
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6 mt-4">
                            <div class="">
                                <input required id="date" name="date" type="date" class="form-control" />
                            </div>
                        </div>


                        <div class="col-md-6 mt-4">
                            <div>
                                <label for="payment_method_id">Método de Pago:</label>
                                <select required id="payment_method_id" name="payment_method_id" class="form-control">
                                    <option value="" selected disabled>Selecciona ...</option>
                                    <option value="1">Efectivo</option>
                                    <option value="2">Tarjeta</option>
                                    <option value="3">Transferencia</option>
                                    <option value="4">Depósito</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mt-4">
                            <div class="">
                                <label for="share-with">Monto de la compra: </label>
                                <input required id="amount" name="amount" type="number" step="0.01" class="form-control" placeholder="" />
                            </div>
                        </div>
                        <div class="col-md-6 mt-4">
                            <div>
                                <label for="clinicModal">Sucursal</label>
                                <select required id="clinicModal" name="clinic" class="form-control">
                                    <option value="" selected disabled>Selecciona ...</option>
                                    <option value="Santafe">Santa Fe</option>
                                    <option value="Pedregal">Pedregal</option>
                                </select>
                            </div>
                        </div>

                    </div>


                    <button type="submit" class="btn btn-secondary">Enviar</button>
                </form>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {



            $("#add_new_expense").click(function(e) {

                e.preventDefault();
                $('#expenseForm')[0].reset();
                $(".btn-update-event").hide();
                $(".btn-add-event").show();
                $("#type_submit").val(1);
                $("#expenseModal").modal("show");
            });

            function addTransaction(formData) {
                $.ajax({
                    method: "POST",
                    url: "scripts/finance/expenses/add_transaction_admin.php",
                    dataType: 'json',
                    data: formData
                }).done(function(response) {
                    showSweetAlert("Listo", response.message, "success");
                    $("#expenseModal").modal("hide");
                    table.ajax.reload();
                }).fail(function(response) {
                    console.log(response);
                    showSweetAlert();
                });
            }
            console.log("holii")
            $("#expenseForm").submit(function(e) {
                console.log("enviando")
                e.preventDefault();
                const formData = $(this).serialize();
                const type_submit = $("#type_submit").val();

                (type_submit == 1) ? addTransaction(formData): updateTransaction(formData);
            });

            loadCats();


            function loadCats() {
                $.ajax({
                    method: "POST",
                    url: "scripts/finance/expenses/load_cats.php",
                    dataType: 'json'
                }).done(function(response) {
                    const cats = response.cats;

                    if (response.success) {
                        cats.forEach(function(value) {
                            const cat_option = `<option  value=${value.id}>${value.name}</option>`;
                            $("#cat_id").append(cat_option);
                        });
                    }
                }).fail(function(response) {
                    showSweetAlert();
                });
            }


            function loadCats() {
                $.ajax({
                    method: "POST",
                    url: "scripts/finance/expenses/load_cats.php",
                    dataType: 'json'
                }).done(function(response) {
                    const cats = response.cats;

                    if (response.success) {
                        cats.forEach(function(value) {
                            const cat_option = `<option  value=${value.id}>${value.name}</option>`;
                            $("#cat_id").append(cat_option);
                        });
                    }
                }).fail(function(response) {
                    showSweetAlert();
                });
            }

            $("#cat_id").change(function() {
                const selectedOption = $(this).find('option:selected');
                console.log(selectedOption)
                const cat_name = selectedOption.data('value');
                const cat_id = selectedOption.val();
                console.log(cat_id)

                if (cat_id == 15) {
                    console.log("sies")
                    $("#sub_cat_id").replaceWith('<input id="sub_cat_id" name="sub_cat_id" type="text" class="form-control" placeholder="Subcategoría" required>');
                } else {
                    if ($("#sub_cat_id").is("input")) {
                        $("#sub_cat_id").replaceWith('<select id="sub_cat_id" name="sub_cat_id" class="form-control" required><option value="" selected disabled></option></select>');
                    }
                    loadSubCats(cat_id);
                }

                var subSubCategoryDiv = document.getElementById('contenedor_sub');
                if (cat_id == 18) {
                    console.log("nomina seleccioanda")
                    subSubCategoryDiv.style.display = 'block';

                } else {
                    subSubCategoryDiv.style.display = 'none';
                    console.log("no es nominca")
                }
            });

            function loadSubCats(cat_id) {
                $.ajax({
                    method: "POST",
                    url: "scripts/finance/expenses/load_sub_cats.php",
                    data: {
                        cat_id: cat_id
                    },
                    dataType: 'json'
                }).done(function(response) {
                    const sub_cats = response.sub_cats;
                    $("#sub_cat_id").empty(); // Clear previous subcategories

                    if (response.success) {
                        sub_cats.forEach(function(value) {
                            const sub_cat_option = `<option value=${value.id}>${value.name}</option>`;
                            $("#sub_cat_id").append(sub_cat_option);
                        });
                    }
                }).fail(function(response) {
                    showSweetAlert();
                });
            }


            $("#sub_cat_id").change(function() {
                const selectedOption = $(this).find('option:selected');
                console.log(selectedOption)
                const subcat_name = selectedOption.data('value');
                const cat_sub_id = selectedOption.val();
                console.log(cat_sub_id)

                load_sub_SubCats(cat_sub_id)

                /* if (cat_id == 15) {
                    console.log("sies")
                    $("#sub_cat_id").replaceWith('<input id="sub_cat_id" name="sub_cat_id" type="text" class="form-control" placeholder="Subcategoría" required>');
                } else {
                    if ($("#sub_cat_id").is("input")) {
                        $("#sub_cat_id").replaceWith('<select id="sub_cat_id" name="sub_cat_id" class="form-control" required><option value="" selected disabled></option></select>');
                    }
                    loadSubCats(cat_id);
                }

                var subSubCategoryDiv = document.getElementById('contenedor_sub');
                if (cat_id == 18) {
                    console.log("nomina seleccioanda")
                    subSubCategoryDiv.style.display = 'block';
                 
                } else {
                    subSubCategoryDiv.style.display = 'none';
                    console.log("no es nominca")
                } */
            });

            function load_sub_SubCats(cat_sub_id) {
                $.ajax({
                    method: "POST",
                    url: "scripts/finance/expenses/load_sub_subcats.php",
                    data: {
                        cat_sub_id: cat_sub_id
                    },
                    dataType: 'json'
                }).done(function(response) {
                    const sub_cats = response.sub_cats;
                    $("#sub_sub_cat_id").empty(); // Clear previous subcategories

                    if (response.success) {
                        sub_cats.forEach(function(value) {
                            const sub_cat_option = `<option value=${value.id}>${value.name}</option>`;
                            $("#sub_sub_cat_id").append(sub_cat_option);
                        });
                    }
                }).fail(function(response) {
                    showSweetAlert();
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            // Obtén el user_id del localStorage
            const user_id = localStorage.getItem("user_id");
            // Asigna el valor de user_id al campo oculto en el formulario
            $("#user_id").val(user_id);

            // Revisa si el user_id es 20
            if (user_id == 20) {
                // Selecciona automáticamente "Transferencia" como método de pago
                $("#payment_method_id").val("3");

                // Deshabilita todas las demás opciones excepto "Transferencia"
                $("#payment_method_id option").each(function() {
                    if ($(this).val() != "3") {
                        $(this).prop("disabled", true); // Deshabilita las otras opciones
                    }
                });
            } else {
                // Si el user_id no es 20, asegura que todas las opciones estén habilitadas
                $("#payment_method_id option").prop("disabled", false);
            }
        });
    </script>

    <link rel="stylesheet" href="./assets/css/tab_gastos.css">