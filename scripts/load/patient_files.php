<?php

ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1); // I use this because I use xdebug.


require_once "../bunnycdn_storage.php";
$lead_id = $_POST['lead_id'];
$type = $_POST['type'];
// Get the list of files in the directory
$ruta_temporal = "../../storage/leads/{$lead_id}/{$type}";

$preview = $config = [];

if (is_dir($ruta_temporal)) {
	$elementos = scanDir($ruta_temporal);

	// Filtrar solo los archivos, excluyendo "." y ".."
	$files = array_filter($elementos, function ($elemento) use ($ruta_temporal) {
		return is_file($ruta_temporal . '/' . $elemento);
	});


	function getFileType($type_ext)
	{
		switch ($type_ext) {
			case 'jpg':
			case 'png':
			case 'jpeg':
				return 'image';
			case 'pdf':
				return 'pdf';
			case 'docx':
			case 'xlsx':
				return 'office';
			default:
				return 'other'; // Tipo de archivo por defecto para otras extensiones
		}
	}


	// Iterate through each file
	foreach ($files as $file) {
		// Exclude current directory (.) and parent directory (..)
		if ($file != "." && $file != "..") {
			$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
			$type_ext = getFileType($ext);

			$fileName  = basename($file);
			$fileInfo = pathinfo($ruta_temporal . '/' . $file);
			$fileSize = filesize($ruta_temporal . '/' . $file);

			$preview[] = str_replace("../../", "", $ruta_temporal) . '/' . $file;
			$config[] = [
				'caption' => $fileName,
				'type' => $type_ext,
				'key' => rand("100", "500"), // Asigna una clave única
				'url' => "scripts/delete/patient_file.php?filename={$fileName}&lead_id={$lead_id}&type={$type}",
				'downloadUrl' => "scripts/download/patient_file.php?filename={$fileName}&lead_id={$lead_id}&type={$type}"
			];
		}
	}
}

$out = ['initialPreview' => $preview, 'initialPreviewConfig' => $config, 'initialPreviewAsData' => true];
echo json_encode($out); // return json data
