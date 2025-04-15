<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";


if (isset($_GET['type'])  && isset($_GET['id'])) {
    $procedure_id = $_GET['id'];

    $sql_row = "SELECT CONCAT(sla.first_name, ' ', sla.last_name) AS name, sla.procedure_date, sla.procedure_type, sla.enfermedades, ep.num_med_record, ep.touchup, ep.room, ep.specialist, ep.notes FROM enf_procedures ep INNER JOIN sa_leads_assessment sla ON ep.lead_id = sla.lead_id WHERE /* sla.status = 1 AND */ ep.lead_id = ?  AND sla.status != 0";

    $sql = $conn->prepare($sql_row);
    $sql->bind_param("i", $procedure_id);

    if (!$sql->execute()) {
        throw new Exception("Error al obtener la info. del procedimiento: " . $sql->error);
    }
    $result = $sql->get_result();

    if ($result->num_rows != 1) {
        throw new Exception("Error de duplicidad. Contacta al administrador.");
    }

    $px_info = $result->fetch_object();

    $procedure_date = $px_info->procedure_date;





    $card = '
			<div class="text-center col-md-12 col-xs-12 order-md-1 order-last">
				<div class="card text-white bg-secondary ">
					<div class="card-body text-center">
						<h2 style="color:#e0ac44;" id="px_name">' . $px_info->name . ' </h2>
						<p><span style="font-size:20px;" class="badge bg-secondar">#' . $px_info->num_med_record . '</span></p>
						<p><span style="font-size:20px;" class="badge bg-primary">' . $px_info->procedure_type . '</span>
						</p>
						<p style="font-size:20px;">
							<strong>Especialista: </strong>' . $px_info->specialist . '<br />
							<strong>Fecha Procedimiento: </strong>' . $px_info->procedure_date . '</p>
					</div>
					<input type="hidden" id="num_med_record" name="num_med_record" value="' . $px_info->num_med_record . '">
					<input type="hidden" id="clinic" name="clinic" value="1">
				</div>
				<input type="hidden" value="' . $_GET['id'] . '" id="px_sales_id">
				<input type="hidden" id="num_med_record" value="' . $px_info->num_med_record . '">
				<input type="hidden" id="clinic" value="1">
				<input type="hidden" id="room" value="' . $px_info->room . '">
				<input type="hidden" id="type" value="' . $_GET['type'] . '">
			</div>';
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <title>Fotos del Paciente | ERP | Los Reyes del Injerto</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Select2 CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/select2.min.css">

    <!-- FileInput-->
    <link rel="stylesheet" type="text/css" href="assets/plugins/fileinput/fileinput.css" />

    <!-- Main CSS -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">

    <!-- Swiper Slider -->
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.css'>
    <!-- Fancy Cards -->
    <link rel="stylesheet" href="assets/plugins/fancycards/style.css">



    <link rel="stylesheet" href="./assets/css/uiverse.css">
</head>

<style>
    #imagesContainer {
        position: relative;
        /* Necesario para la animación */
        opacity: 0;
        /* Ocultar inicialmente */
        transition: opacity 0.5s ease;
        /* Transición suave para la opacidad */
    }

    .image-item {
        display: inline-block;
        /* Cambiado para mostrar imágenes en línea */
        width: 200px;
        /* Ajusta el tamaño si es necesario */
        margin: 10px;
        opacity: 0;
        /* Ocultar inicialmente cada imagen */
        transform: translateY(20px);
        /* Desplazar hacia abajo */
        transition: opacity 0.5s ease, transform 0.5s ease;
        /* Transición suave */
    }

    .image-item.show {
        opacity: 1;
        /* Mostrar imagen */
        transform: translateY(0);
        /* Regresar a la posición original */
    }

    .img_comparer {
        display: flex;
        gap: 1rem;
    }

    .conteiner_principal {
        display: flex;
        align-items: center;
        justify-content: space-around;

    }

    .card_info {
        max-width: 200px;
    }

    .contendor_thumb {
        display: flex;
        flex-direction: column;
    }

    .modal_content_images {
        display: flex;
        flex-direction: row;
        gap: 1rem;
        align-items: center;
        justify-content: center;
    }


    @media screen and (max-width: 600px) {
        .modal_content_images {
            flex-direction: column;
            border: 1px solid red;
        }
    }




    .contendor_thumb button {
        border-radius: 1rem;
        /* Bordes redondeados para los botones */
        background-color: #007bff;
        /* Color de fondo del botón (puedes cambiarlo) */
        color: white;
        /* Color del texto del botón */
        border: none;
        /* Sin borde */
        padding: 0.1rem;
        /* Espaciado interno del botón */
        cursor: pointer;
        /* Cambia el cursor al pasar sobre el botón */
        transition: background-color 0.3s;
        /* Transición suave para el color de fondo */
        width: 50%;
        margin: auto;
    }

    .contendor_thumb button:hover {
        background-color: #0056b3;
        /* Color de fondo al pasar el cursor (puedes cambiarlo) */
    }

    .contendor_thumb button:focus {
        outline: none;
        /* Quita el contorno en el enfoque (opcional) */
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.5);
        /* Sombra de enfoque (opcional) */
    }
</style>

<body class="mini-sidebar">
    <div class="main-wrapper">
        <?php
        require 'templates/header.php';
        require 'templates/sidebar.php';
        ?>
        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a class="nav-link active" href="index.php">Dashboard </a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
                                <li class="breadcrumb-item "><a class="nav-link active" href="view_procedures.php">Procedimientos</a></li>
                                <li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
                                <li class="breadcrumb-item active"><a class="nav-link active" href="#">Comparativa de fotografias de Revisiones</a></li>
                        </div>
                    </div>
                </div>
                <div class="conteiner_principal">

                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="mx-auto col-12 col-md-4">
                                    <div class="card text-white bg-secondary ">
                                        <?= $card; ?>
                                        <input type="hidden" id="clinic" name="clinic" value="1">
                                    </div>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="card">
                                        <h4 class="text-black">Ver fotos de:</h4>

                                        <div id="tabsContainer"></div>
                                        <div id="imagesContainer" class="d-flex flex-wrap"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>




                </div>







            </div>
        </div>

    </div>

    <!-- Modal para mostrar las imágenes seleccionadas -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Comparativa de Fotografías</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal_content_images">
                        <img id="modalFirstImage" class="img-fluid" alt="">
                        <img id="modalSecondImage" class="img-fluid" alt="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <div class=" sidebar-overlay" data-reff=""></div>

    <!-- jQuery -->
    <script src="assets/js/jquery.min.js"></script>

    <script src="assets/js/buffer.js" type="text/javascript"></script>
    <script src="assets/js/filetype.js" type="text/javascript"></script>

    <!-- Bootstrap Core JS -->
    <script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>

    <script src="assets/plugins/fileinput/fileinput.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/kartik-v/bootstrap-fileinput@5.5.3/js/locales/es.js" type="text/javascript"></script>

    <!-- Feather Js -->
    <script src="https://preclinic.dreamstechnologies.com/html/template/assets/js/feather.min.js"></script>

    <!-- Slimscroll -->
    <script src="assets/js/jquery.slimscroll.js"></script>

    <script src='https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>

    <script>
        const px_sales_id = $("#px_sales_id").val();
        const num_med_record = $("#num_med_record").val();
        const room = $("#room").val();
        const type = $("#type").val();



        $(document).ready(function() {

            console.log(num_med_record);

            getFoldersAvailable(num_med_record);

            function fetchFolderContent(numMedico, folderName) {
                // Crear la URL para hacer la petición al backend
                const backendUrl = `./scripts/photos/aux_fetch_folder.php?numMedico=${numMedico}&folderName=${folderName}`;

                console.log("Haciendo petición al backend para obtener el contenido de la carpeta: ", backendUrl);

                fetch(backendUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al obtener el contenido de la carpeta desde el backend.');
                        }
                        return response.json(); // Parsear la respuesta JSON
                    })
                    .then(data => {
                        const images = data.filter(item => !item.IsDirectory && item.ObjectName.match(/\.(jpg|jpeg|png|gif)$/i));

                        const imagesContainer = document.getElementById('imagesContainer');
                        imagesContainer.innerHTML = ''; // Limpiar contenido anterior

                        if (images.length === 0) {
                            imagesContainer.innerHTML = '<p>No hay imágenes disponibles.</p>';
                        } else {
                            let isFirstImageLoaded = false;
                            let isSecondImageLoaded = false;

                            images.forEach((image) => {
                                const imageUrl = `https://rdi-enf-cdmx.b-cdn.net${image.Path.replace('/rdi-enf-cdmx', '')}thumb/${image.ObjectName}`;
                                const imgElement = document.createElement('img');
                                imgElement.src = imageUrl;
                                imgElement.alt = image.ObjectName;
                                imgElement.className = 'image-item';
                                imgElement.onload = () => {
                                    imgElement.classList.add('show');
                                };

                                const addButton = document.createElement('button');
                                addButton.textContent = 'Agregar';
                                addButton.onclick = () => {
                                    const modalFirstImage = document.getElementById('modalFirstImage');
                                    const modalSecondImage = document.getElementById('modalSecondImage');

                                    const selectedImageUrl = imgElement.src.replace('/thumb', '');
                                    console.log(`Imagen seleccionada: ${selectedImageUrl}`); // Log de la imagen seleccionada

                                    if (modalFirstImage.src) {
                                        modalSecondImage.src = selectedImageUrl; // Asigna la URL al modal
                                        isSecondImageLoaded = true; // Marca la segunda imagen como cargada
                                        console.log('Segunda imagen cargada.'); // Log de carga de la segunda imagen
                                    } else {
                                        modalFirstImage.src = selectedImageUrl; // Asigna la URL al modal
                                        isFirstImageLoaded = true; // Marca la primera imagen como cargada
                                        console.log('Primera imagen cargada.'); // Log de carga de la primera imagen
                                    }

                                    // Abre el modal si al menos una imagen está cargada
                                    if (isFirstImageLoaded || isSecondImageLoaded) {
                                        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                                        imageModal.show();
                                    }
                                };

                                const imageContainer = document.createElement('div');
                                imageContainer.classList.add('contendor_thumb');
                                imageContainer.appendChild(imgElement);
                                imageContainer.appendChild(addButton);

                                imagesContainer.appendChild(imageContainer);
                            });
                        }

                        setTimeout(() => {
                            imagesContainer.style.opacity = 1; // Hacer visible el contenedor
                        }, 0);
                    })
                    .catch(error => {
                        console.error('Error al obtener el contenido de la carpeta desde el backend:', error);
                    });
            }




            function getFoldersAvailable(numMedico) {
                // Obtener el tipo de la URL
                const urlParams = new URLSearchParams(window.location.search);
                const type_param = urlParams.get('type'); // Obtener el parámetro 'type' de la URL
                console.log("Tipo obtenido de la URL:", type_param); // Log del tipo obtenido

                // Crear la URL para hacer la petición al backend
                let backendUrl = `./scripts/photos/aux_compare.php?numMedico=${numMedico}&type=${type_param}`;

                console.log("Haciendo petición al backend en ", backendUrl);
                fetch(backendUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error al obtener los datos desde el backend.');
                        }
                        return response.json(); // Parsear la respuesta JSON
                    })
                    .then(data => {
                        const tabsContainer = document.getElementById('tabsContainer');
                        let tabsHTML = '<ul class="nav nav-tabs" id="folderTabs">';

                        data.forEach((folder, index) => {
                            const isActive = index === 0 ? 'active' : '';
                            tabsHTML += `
                <li class="nav-item">
                    <a class="nav-link ${isActive}" href="#" data-folder="${folder.ObjectName}">${folder.ObjectName}</a>
                </li>
            `;
                        });

                        tabsHTML += '</ul>';
                        tabsContainer.innerHTML = tabsHTML;

                        const tabs = document.querySelectorAll('#folderTabs .nav-link');
                        tabs.forEach(tab => {
                            tab.addEventListener('click', function(event) {
                                event.preventDefault();
                                tabs.forEach(t => t.classList.remove('active'));
                                this.classList.add('active');

                                const folderName = this.getAttribute('data-folder');
                                console.log(`Carpeta seleccionada: ${folderName}`); // Log de la carpeta seleccionada
                                fetchFolderContent(numMedico, folderName);
                            });
                        });
                    })
                    .catch(error => {
                        console.error('Error en la petición al backend:', error);
                    });
            }

        });
    </script>


</body>


</html>