<?php
//require_once __DIR__ . "/scripts/common/validate_session.php";



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>Preclinic - Medical & Hospital - Bootstrap 5 Admin Template</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="assets/plugins/datatables/css/datatables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css" rel="stylesheet">
    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

</head>

<body>
    <div class="main-wrapper">
        <?php
        require 'templates/header.php';
        require 'templates/sidebar.php';
        ?>
        <div class="page-wrapper">
            <div class="content">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="container mt-4">
                                    <button id="btnAgregarFila" class="btn btn-primary mb-2">Agregar Fila</button>
                                    <button id="btnEliminarFila" class="btn btn-danger mb-2">Eliminar Fila</button>
                                    <button id="btnExportarTxt" class="btn btn-success mb-2">Guardar y generar Layout</button>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-primary dropdown-toggle mb-2" id="clinicButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            Clinica
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item item_clinic" href="#" data-clinic="Santa Fe">Santa Fe</a></li>
                                            <li><a class="dropdown-item item_clinic" href="#" data-clinic="Queretaro">Queretaro</a></li>
                                        </ul>
                                    </div>

                                    <table id="miTabla" class="table">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th> <!-- Checkbox para seleccionar todos -->
                                                <th>ID</th>
                                                <th>Cuenta</th>
                                                <th>Importe</th>
                                                <th>Nombre</th>
                                                <th>Clinica</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div> <!-- Bootstrap JS y Popper.js (necesarios para algunos componentes de Bootstrap) -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar-overlay" data-reff=""></div>

        <!-- jQuery -->
        <script src="assets/js/jquery.min.js"></script>

        <!-- Bootstrap Core JS -->
        <script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>

        <!-- Feather Js -->
        <script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js"></script>

        <!-- Slimscroll -->
        <script src="assets/js/jquery.slimscroll.js"></script>

        <!-- Select2 Js -->
        <script src="assets/js/select2.min.js"></script>

        <!-- Datatables JS -->
        <script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
        <script src="assets/plugins/datatables/js/datatables.min.js"></script>

        <!-- counterup JS -->
        <script src="assets/js/jquery.waypoints.js"></script>
        <script src="assets/js/jquery.counterup.min.js"></script>

        <!-- SweetAlert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Apexchart JS -->
        <script src="assets/plugins/apexchart/apexcharts.min.js"></script>
        <script src="assets/plugins/apexchart/chart-data.js"></script>

        <!-- Custom JS -->
        <script src="assets/js/app.js"></script>
        <script>
            $(document).ready(function() {
                var clinic = localStorage.getItem('clinica');
                if (clinic) {
                    // Actualizar el texto del botón con la clínica guardada
                    $('#clinicButton').text(clinic);

                    // Marcar como activo el item correspondiente en el dropdown
                    $('.item_clinic').each(function() {
                        if ($(this).data('clinic') === clinic) {
                            $(this).addClass('active'); // Agregar clase active al elemento correspondiente
                        } else {
                            $(this).removeClass('active'); // Quitar clase active de otros elementos
                        }
                    });
                }
                var table = $("#miTabla").DataTable({
                    ajax: {
                        url: 'scripts/finance/nom/all_nomina.php',
                        data: {
                            clinic: clinic // Enviar la clínica como parámetro GET
                        },
                        dataSrc: function(response) {
                            console.log('Respuesta inicial del servidor:', response); // Imprimir respuesta en consola
                            return response.data; // Asegurar que DataTables busque en la clave "data"
                        }
                    },
                    autoWidth: false,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                    },
                    scrollX: true,
                    paging: false,
                    columnDefs: [{
                        targets: [0],
                        orderable: false // Columna de checkbox no ordenable
                    }, {
                        targets: [1],
                        type: "num" // Especificar que la segunda columna es numérica
                    }],
                    columns: [{
                            data: null,
                            render: function(data, type, row) {
                                return `<input type="checkbox" class="select-row" data-id="${row.id}">`; // Agregar id como atributo data-id
                            }
                        },
                        {
                            data: 'num_progresivo'
                        },
                        {
                            data: 'cuenta'
                        },
                        {
                            data: 'importe'
                        },
                        {
                            data: 'nombre'
                        },
                        {
                            data: 'clinic'
                        } // Nueva columna para la clínica
                    ]
                });


                // Manejar selección de clínica
                $('.item_clinic').on('click', function() {
                    var selectedClinic = $(this).data('clinic');
                    $('#clinicButton').text(selectedClinic); // Cambiar el texto del botón al seleccionado
                    $('#clinicButton').val(selectedClinic); // Almacenar el valor de la clínica seleccionada

                    // Cargar registros de la clínica seleccionada
                    loadRecordsByClinic(selectedClinic);
                });

                function loadRecordsByClinic(clinic) {
                    $.ajax({
                        url: 'scripts/finance/nom/all_nomina.php',
                        method: 'GET',
                        data: {
                            clinic: clinic
                        },
                        success: function(response) {
                            console.log("response de load", response)
                            var data = response.data;
                            table.clear().rows.add(data).draw();
                        },
                        error: function() {
                            Swal.fire('Error', 'No se pudieron cargar los registros.', 'error');
                        }
                    });
                }

                // Función para agregar una fila
                $('#btnAgregarFila').on('click', function() {
                    var clinicSelected = $('#clinicButton').val(); // Verifica la clínica seleccionada
                    if (clinicSelected === 'Querétaro') {
                        Swal.fire('Advertencia', 'No se pueden agregar filas en la sede de Querétaro.', 'warning');
                        return;
                    }

                    var rowCount = table.rows().count() + 1;
                    var newRowData = {
                        num_progresivo: rowCount,
                        cuenta: '',
                        importe: '',
                        nombre: '',
                        clinic: clinicSelected // Asignar la clínica seleccionada
                    };

                    table.row.add(newRowData).draw();
                });

                // Eliminar fila al hacer clic en el botón
                $('#btnEliminarFila').on('click', function() {
                    // Obtener todos los checkboxes seleccionados
                    var selectedCheckboxes = $('input.select-row:checked');

                    // Verificar si hay al menos un checkbox seleccionado
                    if (selectedCheckboxes.length > 0) {
                        // Crear un array con los valores data-id de los checkboxes seleccionados
                        var ids = selectedCheckboxes.map(function() {
                            return $(this).data('id');
                        }).get();

                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: "No podrás revertir esta acción",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Sí, borrar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: 'scripts/finance/nom/delete_nomina.php',
                                    method: 'POST',
                                    data: {
                                        ids: ids
                                    },
                                    success: function(response) {
                                        let respuesta = JSON.parse(response);
                                        if (respuesta.success) {
                                            // Eliminar las filas de la tabla y actualizar
                                            selectedCheckboxes.each(function() {
                                                table.row($(this).closest('tr')).remove();
                                            });
                                            table.draw();
                                            Swal.fire('¡Eliminado!', respuesta.message, 'success');
                                        } else {
                                            Swal.fire('Error', 'No se pudo eliminar los registros.', 'error');
                                        }
                                    },
                                    error: function() {
                                        Swal.fire('Error', 'Hubo un problema con la petición.', 'error');
                                    }
                                });
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Selecciona al menos una fila para eliminar.', 'warning');
                    }
                });


                // Exportar a TXT
                $('#btnExportarTxt').on('click', function() {
                    var data = table.data().toArray();

                    var txtContent = '';
                    data.forEach(function(row) {
                        txtContent += `${row.num_progresivo}/${row.cuenta}/${row.importe}/${row.nombre}/${row.clinic}\n`; // Construir la fila como string
                    });
                    console.log(clinic)

                    $.post('scripts/add/layout.php', {
                        content: txtContent,
                        clinic: clinic
                    }, function(response) {
                        Swal.fire({
                            title: "Listo!",
                            text: response,
                            icon: "success",
                            background: "white",
                            showConfirmButton: true,
                            confirmButtonText: "Descargar",
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.open('scripts/download/layout.php', '_blank');
                            }
                        });
                    });
                });


                // Habilitar edición en línea
                var editedCell = null;
                $('#miTabla').on('click', 'td', function() {
                    if (editedCell === null) {
                        editedCell = table.cell(this);
                        var currentValue = editedCell.data();
                        editedCell.data(`<input style="width:100%;" type="text" value="${currentValue}">`).draw();

                        var editedInput = editedCell.node().querySelector('input');
                        editedInput.focus();
                    } else if (editedCell.node() !== this) {
                        var newValue = editedCell.node().querySelector('input').value;
                        editedCell.data(newValue).draw();
                        editedCell = null;
                    }
                });

                // Guardar cambios al presionar Enter
                $('#miTabla').on('keyup', 'input', function(e) {
                    if (e.key === "Enter") {
                        var cell = table.cell(this.parentElement);
                        cell.data(this.value).draw();
                        editedCell = null;
                    }
                });

                // Resaltar fila seleccionada
                $('#miTabla tbody').on('click', 'tr', function() {
                    $(this).toggleClass('selected');
                });
            });
        </script>

</body>

</html>