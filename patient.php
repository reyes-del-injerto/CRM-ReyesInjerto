<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.
date_default_timezone_set('America/Mexico_City');

require_once __DIR__ . "/scripts/common/connection_db.php";
require_once __DIR__ . "/scripts/common/validate_session.php";

?>
<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
	<title>Paciente | ERP | Los Reyes del Injerto</title>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" type="text/css" href="assets/plugins/bootstrap/bootstrap.min.css">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="assets/plugins/fontawesome/fontawesome.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

	<!-- Datatables CSS -->
	<link href="https://cdn.jsdelivr.net/npm/feather-icon@0.1.0/css/feather.min.css" rel="stylesheet">

	<!-- Main CSS -->
	<link rel="stylesheet" type="text/css" href="assets/css/style.css">

	<!-- Swiper Slider -->
	<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.css'>

	<!-- FancyCards -->
	<link rel="stylesheet" href="assets/plugins/fancycards/style.css">
</head>

<body class="mini-sidebar">
	<div class="main-wrapper">
		<?php
		require 'templates/header.php';
		require 'templates/sidebar.php';
		?>
		<div class="page-wrapper">
			<div class="content">
				<div class="page-header">
					<div class="row">
						<div class="col-sm-12">
							<ul class="breadcrumb">
								<li class="breadcrumb-item"><a href="index.php">Dashboard </a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item "><a href="view_procedures.php">Procedimientos</a></li>
								<li class="breadcrumb-item"><i class="fa fa-arrow-right"></i></li>
								<li class="breadcrumb-item"><a href="procedure_photos.php?id=<?= $_GET['id']; ?>">Fotografías de Revisiones</a></li>
						</div>
					</div>
				</div>
				<div class="row justify-content-center">
					<div class="mx-auto col-12 col-md-4">
						<!-- <a href="comparar_fotos.php?id=" type="button" class="btn btn-warning">Ver evolución del paciente</a> -->
						<div class="card text-white bg-secondary ">
							<div class="card-body text-center">
								<h2 style='color:#e0ac44;'><?= $row->name; ?></h2>
								<p><span style='font-size:20px;' class='badge bg-secondary'>#<?= $row->num_med_record; ?></span></p>
								<p><span style='font-size:20px;' class='badge bg-primary'><?= $row->procedure_type; ?></span><span style='font-size:20px;' class='badge bg-dark'><?= $row->clinic; ?></span></p>
								<p style='font-size:20px;'><strong>Sala: </strong><?= $row->room; ?><br /><strong>Especialista: </strong><?= $row->specialist; ?><br /><strong>Fecha Procedimiento: </strong><?= $row->procedure_date; ?></p>
							</div>
						</div>
					</div>
					<div class="col-12 col-md-8">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="text-center col-md-12 col-xs-12 order-md-1 order-last">
										<input type="hidden" value="<?= $_GET['id']; ?>" id="px_sales_id">
										<input type="hidden" id="px_num_med_record">
										<input type="hidden" id="clinic" value="1">
									</div>
								</div>
								<div class=" slider">
									<div class="swiper people__slide">
										<div class="swiper-wrapper">
											<div class="swiper-slide">
												<div class="people__card">
													<div class="people__image">
														<img src="https://www.losreyesdelinjerto.com/assets/img/leon-footer.webp" style="width:50%;height:auto;">
													</div>
													<div class="people__info">
														<ul class="people__social">
														</ul>
														<h3 class="people__name">Procedimiento</h3>
													</div>
													<div class="people__btn">
														<a class="view_imgs" href="procedure_photos.php?id=<?= $_GET['id']; ?>">Ir</a>
													</div>
												</div>
											</div>
											<div class=" swiper-slide">
												<div class="people__card">
													<div class="people__image">
														<img src="https://www.losreyesdelinjerto.com/assets/img/leon-footer.webp" style="width:50%;height:auto;">
													</div>
													<div class="people__info">
														<ul class="people__social">
														</ul>
														<h3 class="people__name">Tratamiento</h3>
													</div>
													<div class="people__btn">
														<a class="view_imgs" href="view_treatment.php?id=<?= $_GET['id']; ?>&px=yes">Ir</a>
													</div>
												</div>
											</div>
											<div class=" swiper-slide">
												<div class="people__card">
													<div class="people__image">
														<img src="https://www.losreyesdelinjerto.com/assets/img/leon-footer.webp" style="width:50%;height:auto;">
													</div>
													<div class="people__info">
														<ul class="people__social">
														</ul>
														<h3 class="people__name">Retoque</h3>
													</div>
													<div class="people__btn">
														<a class="view_imgs" href="procedure_photos.php?id=<?= $_GET['id']; ?>&touchup=yes">Ir</a>
													</div>
												</div>
											</div>
										</div>
									</div>
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
	<script src="assets/plugins/bootstrap/bootstrap.bundle.min.js"></script>

	<!-- Slimscroll -->
	<script src="assets/js/jquery.slimscroll.js"></script>

	<!-- Swiper Slider -->
	<script src='https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.0.4/swiper-bundle.min.js'></script>

	<!-- Custom JS -->
	<script src="assets/js/app.js"></script>
	<script>
		$(document).ready(function() {
			var folders, folders_name, swiper;
			const px_sales_id = $("#px_sales_id").val();

			swiper = new Swiper(".swiper", {
				loop: false,
				slidesPerView: "auto",
				centeredSlides: true,
				observeParents: true,
				observer: true,
			});

			swiper.slides.forEach(function(slide, index) {
				slide.addEventListener('mouseover', function() {
					$(this).css('cursor', 'pointer');
				});
				slide.addEventListener('click', function() {
					swiper.slideTo(index);
				});
			});

			swiper.on('slideChangeTransitionEnd', function() {
				$(".people__btn").css('display', 'none');
				$(swiper.el).find('.swiper-slide-active .people__btn').css('display', 'block');
			});

		});
	</script>

</body>

</html>