<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
header('Content-Type: application/json');
date_default_timezone_set('America/Mexico_City');

session_start();
require_once __DIR__ . '/../common/connection_db.php';
require_once __DIR__ . "/../common/bunnynet.php";
require_once __DIR__ . '/../../vendor/autoload.php';

try {
  $api_key = '630c8570-c058-4763-bff29c55f4e8-b1ca-47ae';
  $storageZoneName = 'rdi-cdmx-leads';

  $bunnyCDNStorage = new BunnyCDNStorage($storageZoneName, $api_key, "LA");

  $success = false;
  $filePath = null;
  //$created_by = $_SESSION['user_id'];
  $created_by = $_POST['user_id'];

  // Cambia el archivo base según la clínica
  $clinic = $_POST['clinic'];
  if ($clinic === 'Queretaro') {
      $file = '../../files/cdmx/diseno-valoracion_qro.pdf'; // Asegúrate de que esta ruta sea correcta
  } else {
      $file = '../../files/cdmx/diseno-valoracion.pdf';
  }

  $lead_id = $_POST['lead_id'];

  $raw_assessment_date = $_POST['assessment_date'];
  $assessment_date = parse_date($raw_assessment_date);

  $firstname = $_POST['client_firstname'];
  $lastname = $_POST['client_lastname'];
  $name = $firstname . " " . $lastname;

  if (isset($_POST['open_date']) && $_POST['open_date'] == 1) {
    $raw_procedure_date = "2030-01-01";
    $procedure_date = "Por definir";
  } else {
    $raw_procedure_date = $_POST['e_procedure_date'];
    $procedure_date = parse_date($raw_procedure_date);
  }

  $procedure_type = $_POST['procedure_type'];
  $Notas2 = nl2br($_POST['description']);
  $closer = $_POST['assessment_employee'];

  $assessment_type = $_POST['assessment_type'];
  $created_at = date("Y-m-d H:i:s");
  $timestamp = time();
  $first_meet_type = (isset($_POST['first_meet_type'])) ? $_POST['first_meet_type'] : 'Desconocido';
  $status = 1;

  // Check if a photo was uploaded, otherwise use a default photo
  if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
    $uploaded_photo_src = upload_photo($_FILES['photo'], $lead_id, $timestamp);

    if (!$uploaded_photo_src) {
      throw new Exception("Error al subir la fotografía");
    }
  } else {
    // Use default photo
    $default_photo = '../../assets/img/Fotografía_pendiente.jpg'; // Replace with your default photo path
    $uploaded_photo_src = $default_photo;
  }

  // Disable all the previous assessments (not delete them for further information)
  $sql_row = "UPDATE sa_leads_assessment SET status = 0 WHERE lead_id = ?;";
  $sql = $conn->prepare($sql_row);
  $sql->bind_param("i", $lead_id);

  if (!$sql->execute()) {
    throw new Exception("Error al borrar las valoraciones pasadas: " . $sql->error);
  }

  $sql_row = "INSERT INTO sa_leads_assessment (lead_id, date, type, first_name, last_name, procedure_date, procedure_type, closer, first_meet_type, clinic, notes, created_at, created_by, timestamp, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
  $sql = $conn->prepare($sql_row);

  $sql->bind_param("isssssssssssiii", $lead_id, $raw_assessment_date, $assessment_type, $firstname, $lastname, $raw_procedure_date, $procedure_type, $closer, $first_meet_type, $clinic, $_POST['description'], $created_at, $created_by, $timestamp, $status);

  if (!$sql->execute()) {
    throw new Exception("Error al añadir la valoración a la BD" . $sql->error);
  }

  $mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'orientation' => 'P',
    'format' => 'Letter'
  ]);

  $mpdf->SetTitle("Valoración_$name");

  $pagecount = $mpdf->SetSourceFile($file);
  $tplId = $mpdf->ImportPage($pagecount);
  $mpdf->UseTemplate($tplId);
  $mpdf->SetFont('leagues', 'B', 12);

  write_text(48, 60, $name);
  write_text(168.9, 47.4, $assessment_date);
  write_text(76, 69.6, $procedure_date);
  write_text(157, 69.6, $procedure_type);
  write_notes($Notas2);
  write_text(136, 248, $closer);

  // Img dimensions
  list($width, $height) = getimagesize($uploaded_photo_src);

  // Put img based on orientation
  if ($width > $height) {
    //!Horizontal
    $mpdf->Image($uploaded_photo_src, 45, 82, 133, 100, 'jpg', '', true, false);
  } else {
    //!Vertical
    $mpdf->Image($uploaded_photo_src, 75, 82, 75, 100, 'jpg', '', true, false);
  }

  // Asegúrate de que el directorio existe antes de guardar el archivo PDF
  $fileDirectory = "../../storage/leads/{$lead_id}/assessment/";
  if (!file_exists($fileDirectory)) {
    mkdir($fileDirectory, 0775, true);
  }

  $filePath = $fileDirectory . "valoracion_{$timestamp}.pdf";

  if ($mpdf->Output($filePath, 'F') === false) {
    throw new Exception("Error al generar la hoja de valoración");
  }

  if (!$bunnyCDNStorage->uploadFile($filePath, "{$storageZoneName}/{$lead_id}/assessment/valoracion_{$timestamp}.pdf")) {
    throw new Exception('Error al subir el archivo a BunnyCDN.');
  }

  $success = true;
  $message = "Valoración añadida correctamente";
} catch (Exception $e) {
  $success = false;
  $message = "Error: " . $e->getMessage();
}

echo json_encode(["success" => $success, "message" => $message, "path" => $filePath]);

function parse_date($date)
{
  $exploded_date = explode('-', $date);
  $formatted_date = $exploded_date[2] . "/" . $exploded_date[1] . "/" . $exploded_date[0];
  return $formatted_date;
}

function write_text($x, $y, $text)
{
  global $mpdf;
  $mpdf->WriteText($x, $y, $text);
}

function write_notes($Notas2)
{
  $Notas = explode("<br />", $Notas2);
  $Nota1 = $Notas[0];
  $Nota2 = isset($Notas[1]) ? ltrim(rtrim($Notas[1])) : '';
  $Nota3 = isset($Notas[2]) ? ltrim(rtrim($Notas[2])) : '';

  global $mpdf;
  $mpdf->WriteText(30, 203.50, $Nota1);
  $mpdf->WriteText(30, 208.50, $Nota2);
  $mpdf->WriteText(30, 213.50, $Nota3);
  return true;
}

function upload_photo($photo, $lead_id, $timestamp)
{
  $uploadDirectory = "../../storage/leads/{$lead_id}/assessment/";
  if (!file_exists($uploadDirectory)) {
    mkdir($uploadDirectory, 0775, true);
  }

  $file_name = "photo_{$timestamp}.jpg";
  $file_source = $uploadDirectory . $file_name;

  if (move_uploaded_file($photo['tmp_name'], $file_source)) {
    return $file_source;
  } else {
    return false;
  }
}
?>
