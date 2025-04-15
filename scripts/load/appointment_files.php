<div class="row mt-4 files">
	<div class="col-12 col-md-3 col-lg-3">
		<strong>Historial Clínico</strong><br>
		<?php
		$appointment_id = $_GET['px_id'];
		$general_dir = "temporal_storage/docs/{$appointment_id}";  // Replace this with the path to your directory
		$hc_dir = $general_dir . "/hc/";
		// Get the list of files in the directory
		$files = scandir($hc_dir);

		// Iterate through each file
		foreach ($files as $file) {
			// Exclude current directory (.) and parent directory (..)
			if ($file != "." && $file != "..") {

				$fileInfo = pathinfo($hc_dir . '/' . $file);
				$fileSize = filesize($hc_dir . '/' . $file); ?>

				<div class="card">
					<div class="card-body card-file">
						<div class="media">
							<img src="https://cdn3.iconfinder.com/data/icons/ui-files-documents/263/extension-format-document-file_139-512.png" class="img-fluid mr-3" width="50" />
							<div class="media-body">
								<h5 class="mt-0"><?= $fileInfo['basename'] ?></h5>
							</div>
						</div>
						<div class="btn-group btnGroupFile mx-auto" role="group">

							<a type="button" class="btn btn-light btn-download" href="scripts/download/appointment_file.php?px_id=<?= $_GET['px_id']; ?>&folder=hc&filename=<?= $fileInfo['basename'] ?>">
								<i class="fa fa-download"></i>
							</a>
							<?php if (in_array(8, $_SESSION['user_permissions'])) { ?>
								<a type="button" class="btn btn-light btn-delete" data-px=<?= $_GET['px_id']; ?> data-folder="hc" data-filename="<?= $fileInfo['basename'] ?>">
									<i class="fa fa-trash"></i>
								</a>
							<?php } ?>
						</div>
					</div>
				</div>
		<?php }
		}
		?>

		<strong>Subir archivo a /H.C./</strong>
		<input class="form-control" type="file" accept="*/*" name="hc[]" id="hc" multiple>
	</div>
	<div class="col-12 col-md-3 col-lg-3">
		<strong>INE</strong><br>
		<?php
		$appointment_id = $_GET['px_id'];
		$ine_dir = $general_dir . "/ine/";
		// Get the list of files in the directory
		$files = scandir($ine_dir);

		// Iterate through each file
		foreach ($files as $file) {
			// Exclude current directory (.) and parent directory (..)
			if ($file != "." && $file != "..") {

				$fileInfo = pathinfo($ine_dir . '/' . $file);
				$fileSize = filesize($ine_dir . '/' . $file); ?>

				<div class="card">
					<div class="card-body card-file">
						<div class="media">
							<img src="https://cdn3.iconfinder.com/data/icons/ui-files-documents/263/extension-format-document-file_139-512.png" class="img-fluid mr-3" width="50" />
							<div class="media-body">
								<h5 class="mt-0"><?= $fileInfo['basename'] ?></h5>
							</div>
						</div>
						<div class="btn-group btnGroupFile mx-auto" role="group">

							<a type="button" class="btn btn-light btn-download" href="scripts/download/appointment_file.php?px_id=<?= $_GET['px_id']; ?>&folder=ine&filename=<?= $fileInfo['basename'] ?>">
								<i class="fa fa-download"></i>
							</a>
							<?php if (in_array(8, $_SESSION['user_permissions'])) { ?>

								<a type="button" class="btn btn-light btn-delete" data-px=<?= $_GET['px_id']; ?> data-folder="ine" data-filename="<?= $fileInfo['basename'] ?>">
									<i class="fa fa-trash"></i>
								</a>
							<?php } ?>
						</div>
					</div>
				</div>
		<?php }
		}
		?>

		<strong>Subir archivo a /ine/</strong>
		<input class="form-control" type="file" accept="*/*" name="ine[]" id="ine" multiple>
	</div>
	<div class="col-12 col-md-3 col-lg-3">
		<strong>Laboratorios</strong><br>
		<?php
		$appointment_id = $_GET['px_id'];
		$labs_dir = $general_dir . "/labs/";
		// Get the list of files in the directory
		$files = scandir($labs_dir);

		// Iterate through each file
		foreach ($files as $file) {
			// Exclude current directory (.) and parent directory (..)
			if ($file != "." && $file != "..") {

				$fileInfo = pathinfo($labs_dir . '/' . $file);
				$fileSize = filesize($labs_dir . '/' . $file); ?>

				<div class="card">
					<div class="card-body card-file">
						<div class="media">
							<img src="https://cdn3.iconfinder.com/data/icons/ui-files-documents/263/extension-format-document-file_139-512.png" class="img-fluid mr-3" width="50" />
							<div class="media-body">
								<h5 class="mt-0"><?= $fileInfo['basename'] ?></h5>
							</div>
						</div>
						<div class="btn-group btnGroupFile mx-auto" role="group">
							<a type="button" class="btn btn-light btn-download" href="scripts/download/appointment_file.php?px_id=<?= $_GET['px_id']; ?>&folder=labs&filename=<?= $fileInfo['basename'] ?>">
								<i class="fa fa-download"></i>
							</a>
							<?php if (in_array(8, $_SESSION['user_permissions'])) { ?>

								<a type="button" class="btn btn-light btn-delete" data-px=<?= $_GET['px_id']; ?> data-folder="labs" data-filename="<?= $fileInfo['basename'] ?>">
									<i class="fa fa-trash"></i>
								</a>
							<?php } ?>
						</div>
					</div>
				</div>
		<?php }
		}
		?>

		<strong>Subir archivo a /laboratorios/</strong>
		<input class="form-control" type="file" accept="*/*" name="labs[]" id="labs" multiple>
	</div>
	<div class="col-12 col-md-3 col-lg-3">
		<strong>Recibos</strong><br>
		<?php
		$appointment_id = $_GET['px_id'];
		$general_dir = "temporal_storage/docs/{$appointment_id}";  // Replace this with the path to your directory
		$hc_dir = $general_dir . "/invoices/";
		// Get the list of files in the directory
		$files = scandir($hc_dir);

		// Iterate through each file
		foreach ($files as $file) {
			// Exclude current directory (.) and parent directory (..)
			if ($file != "." && $file != "..") {

				$fileInfo = pathinfo($hc_dir . '/' . $file);
				$fileSize = filesize($hc_dir . '/' . $file); ?>

				<div class="card">
					<div class="card-body card-file">
						<div class="media">
							<img src="https://cdn3.iconfinder.com/data/icons/ui-files-documents/263/extension-format-document-file_139-512.png" class="img-fluid mr-3" width="50" />
							<div class="media-body">
								<h5 class="mt-0"><?= $fileInfo['basename'] ?></h5>
							</div>
						</div>
						<div class="btn-group btnGroupFile mx-auto" role="group">

							<a type="button" class="btn btn-light btn-download" href="scripts/download/appointment_file.php?px_id=<?= $_GET['px_id']; ?>&folder=invoices&filename=<?= $fileInfo['basename'] ?>" data-px=<?= $_GET['px_id']; ?> data-folder="invoices" data-filename="<?= $fileInfo['basename'] ?>">
								<i class="fa fa-download"></i>
							</a>
							<?php if (in_array(8, $_SESSION['user_permissions'])) { ?>

								<a type="button" class="btn btn-light btn-delete" data-px=<?= $_GET['px_id']; ?> data-folder="invoices" data-filename="<?= $fileInfo['basename'] ?>">
									<i class="fa fa-trash"></i>
								</a>
							<?php } ?>
						</div>
					</div>
				</div>
		<?php }
		}
		?>

		<strong>Subir archivo a /recibos/</strong>
		<input class="form-control" type="file" accept="*/*" name="invoices[]" id="invoices" multiple>
	</div>
</div>