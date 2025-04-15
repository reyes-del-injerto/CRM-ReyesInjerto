<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.

require_once "scripts/common/connection_db.php";

if (isset($_COOKIE['recordar_token'])) {
    $token = $_COOKIE['recordar_token'];

    $sql = "SELECT user_id,user_name,user_department FROM u_tokens WHERE token = '$token';";

    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) == 1) {
        $row = mysqli_fetch_assoc($query);
        $userId = $row['user_id'];
        $userName = $row['user_name'];
        $userDepartment = $row['user_department'];

        session_start();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
        $_SESSION['user_department'] = $userDepartment;

        $sql = "SELECT permission_id FROM u_permission_assignment WHERE user_id = $userId;";

        $result = $conn->query($sql);

        $user_permissions = array();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $user_permissions[] = $row['permission_id'];
            }
        }
        $_SESSION['user_permissions'] = $user_permissions;
    }
} else {
    header("Location: login.php?redirect=view_log.php");
    exit();
}
if ($_SESSION['user_id'] != 1 && $_SESSION['user_id'] != 20  && $_SESSION['user_id'] != 7 && $_SESSION['user_id'] != 41 && $_SESSION['user_id'] != 18) {
    header('Location: login.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>Inicio | Los Reyes del Injerto</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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

                <!-- Page Header -->
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
                                <li class="breadcrumb-item active"><a href="view_log.php">Ver Log</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Page Header -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card">
                            <div class="card-header">
                            </div>
                            <div class="card-body">

                                <!-- Table Header -->
                                <div class="page-table-header mb-2">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="doctor-table-blk">
                                                <div class="doctor-search-blk">
                                                    <div class="top-nav-search table-search-blk">

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /Table Header -->

                                <div class="table-responsive">
                                    <table class="table border-0 custom-table comman-table datatable mb-0 table-striped" id="usersTable">
                                        <thead>
                                            <tr>
                                                <th>Px</th>
                                                <th>Fecha</th>
                                                <th>Total Registros</th>
                                                <th>Creado Por</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>


                            </div>
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
    <script src="assets/plugins/bootstrap/bootstrapx.bundle.min.js"></script>

    <!-- Feather Js -->
    <script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js"></script>

    <!-- Slimscroll -->
    <script src="assets/js/jquery.slimscroll.js"></script>

    <!-- Datatables JS -->
    <script src="assets/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="assets/plugins/datatables/js/datatables.min.js"></script>

    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
    <script>
        $(document).ready(function() {

            let jquery_datatable = $("#usersTable").DataTable({
                ajax: 'scripts/load/log.php', // Ruta al archivo PHP con la nueva consulta
                autoWidth: false,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                },
                scrollX: true,
                order: [
                    [1, 'desc']
                ], // Ordenar por la fecha
                columns: [{
                        title: "Px",
                        data: "belongs_to"
                    }, // Mapeo de `belongs_to`
                    {
                        title: "Fecha",
                        data: "fecha"
                    }, // Mapeo de `fecha`
                    {
                        title: "Elementos cargados",
                        data: "total_registros"
                    }, // Mapeo de `total_registros`
                    {
                        title: "Creado Por",
                        data: "created_by"
                    } // Mapeo de `created_by`
                ],
                drawCallback: function(settings) {
                    // Añadir clase al último <td> en cada fila después de que se dibuje la tabla
                    $('#usersTable tbody tr td:last-child').addClass('text-end');
                }
            });

            // Cambiar el color de la paginación
            const setTableColor = () => {
                document.querySelectorAll('.dataTables_paginate .pagination').forEach(dt => {
                    dt.classList.add('pagination-primary');
                });
            };

            setTableColor();
            jquery_datatable.on('draw', setTableColor);
        });
    </script>



</body>

</html>