<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

session_start();

require_once __DIR__ . "/../../common/bunnynet.php";
require_once __DIR__ . "/../../common/utilities.php";
require_once __DIR__ . "/../../common/connection_db.php";

$api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
$storageZoneName = "rdi-enf-cdmx";
$bunny = new BunnyCDNStorage($storageZoneName, $api_key, "LA");
$result_message = "";
$uploaded_datetime = date('Y-m-d H:i:s');
$num_med = "0";
$telegram_sent = false;
$db_saved = false;

try {
    // Init Empty Data
    $message = "";
    $fileExtension = NULL;
    $img_url = NULL;

    // Log Data
    $uploaded_by = $_POST['user_id'];
    $uploaded_at = date('Y-m-d H:i:s');

    // Form Data
    $lead_id = $_POST['lead_id'];
    $num_med = trim($_POST['num_med']);
    [$px_name, $num_med_record] = getPatientData($_POST['px_data']);
    $touchup = "";
    $process_number = parseProcessNumber($_POST['process']);
    $specialist = $_POST['specialist'];
    $room = $_POST['room'];
    $notif_type = $_POST['notif_type'];
    $hour = (isset($_POST['hour'])) ? $_POST['hour'] : 'N/A';
    $notif_datetime = (isset($_POST['hour'])) ? getNotifDatetime($_POST['hour']) : $uploaded_at;

    $available_process = ["Px firmó documentos", "Inicio de infiltración", "Término de infiltración", "Inicio de extracción", "Término de extracción", "Inicio de infiltración", "Término de infiltración", "Inicio de incisiones", "Término de incisiones", "Inicio de implantación", "Término de implantación y procedimiento"];
    if ($process_number == 0) {
        $specialist = $_POST['specialist'];
        $goal = $_POST['goal'];
        $target_achieved = 0;

        $sql = $conn->prepare("INSERT INTO enf_procedures_targets (lead_id, target_fixed, target_achieved) VALUES (?, ?, ?)");

        $sql->bind_param("iii", $lead_id, $goal, $target_achieved);

        if (!$sql->execute()) {
            throw new Exception("Error al añadir la notificación. Contacta al admin.");
        }
        $telegram_message = "Px firmó documentos a las {$hour}\n\n*Especialista:* {$specialist}\n*Meta:* {$goal}";
    } else if ($process_number == 3.1 || $process_number == 9.1 || $process_number == -1) {
        $photo_type = (isset($_POST['photo_type'])) ? $_POST['photo_type'] : "Incidencia";
        $notif_datetime = $uploaded_at;
        $filename = $_FILES['file']['name'];
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $tmp_file = $_FILES['file']['tmp_name'];

            $parsed_filename = str_replace(" ", "", $filename);
            $local_filename = $_FILES['file']['tmp_name'];

            $image = new Imagick($tmp_file);

            $image->setImageCompressionQuality(70);
            $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);
            $newFileName = $uploaded_datetime . '.' . $fileExtension;

            $targetDir = "../../../temporal_storage/rdi-enf-cdmx/{$num_med}/proced/";

            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            $image->writeImage($targetDir . $newFileName);

            $image->clear();
            $image->destroy();

            $img_url = "https://lrdi.online/CDMX3/temporal_storage/rdi-enf-cdmx/{$num_med}/proced/{$newFileName}";
        }
        $telegram_message = "*{$px_name}* - {$photo_type}\n";
    } else if ($process_number > 0 && $process_number < 10) {
        $telegram_message = "*{$px_name}*. {$available_process[$process_number]} a las *{$hour}*";
    } else if ($process_number == 10) {
        $follicular_units = $_POST['uf'];
        $hair_follicles = $_POST['hair_follicles'];
        $specialist = $_POST['specialist'];

        $telegram_message = "*{$px_name}*. {$available_process[$process_number]} a las *{$hour}*\nUnidades Foliculares: *{$follicular_units}*\nFoliculos: *{$hair_follicles}*\nEspecialista: *{$specialist}*\n";
    }

    if (isset($_POST["comments"]) && !empty(trim($_POST["comments"]))) {
        $notas = $_POST['comments'];
        $telegram_message .= "\nNotas:*{$notas}*";
    }

    $result = // sendTelegramMessage($telegram_message, $room, $img_url);
	 // Send Telegram Message  or fake 
	 ["success"=>true, "message_id"=> 1];
    if (!$result['success']) {
        throw new Exception("Error al enviar la notificación en Telegram. Contacta al administrador");
    }
    $telegram_sent = true;

    $db_message = parseDbMessage($telegram_message);
    $message_id = $result['message_id'];

    $sql = $conn->prepare("INSERT INTO notifications (lead_id, process, datetime, message, uploaded_by, uploaded_datetime, ext_image, telegram_msg_id, procedure_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("idssissii", $lead_id, $process_number, $notif_datetime, $db_message, $uploaded_by, $uploaded_at, $fileExtension, $message_id, $notif_type);

    if (!$sql->execute()) {
        throw new Exception("Error al insertar la notificación en la BD. Contacta al administrador");
    }
    $db_saved = true;

    $result_message = "Notificación enviada";
    if (empty($notif_type)) {
        $result_message .= " (Notificacion guardada pero no se indico a que proced. pertenece)";
    }
} catch (Exception $e) {
    $result_message = $e->getMessage();
}

// Lógica adicional para los estados
$response_status = "";
if ($telegram_sent && $db_saved) {
    $response_status = "Guardado en BD y enviado a Telegram.";
} elseif (!$telegram_sent && $db_saved) {
    $response_status = "Guardado en BD pero no enviado a Telegram.";
} elseif ($telegram_sent && !$db_saved) {
    $response_status = "Enviado a Telegram pero no guardado en BD.";
} else {
    $response_status = "No se guardó ni en BD ni se envió a Telegram.";
}

$fullmessage = $result_message . " " . $response_status;
echo json_encode([
    "success" => $db_saved,
    "message" => $fullmessage,
    "status" => $response_status,
    "exp" => $num_med,
    "notif_type" => $notif_type
]);

$conn->close();

// funciones adicionales

function parseProcessNumber($process_number)
{ // If process number has a dot, its an int number, if not, its an float number.
    $process_number = (strpos($process_number, '.') === false) ? intval($process_number) : floatval($process_number);
    return $process_number;
}

function getPatientData($px_data)
{ // Explode px name and px num med record from form Data
    $px_data = explode("-", $px_data);
    $px_name = ltrim(rtrim($px_data[0]));
    $num_med_record = ltrim(rtrim($px_data[1]));

    return [$px_name, $num_med_record];
}

function getNotifDatetime($hour)
{ // Modify H:m (form data val) to Y-m-d H:m 
    $today_date = date('Y-m-d');
    $datetime_string = $today_date . ' ' . $hour;
    $datetime = new DateTime($datetime_string);
    $notif_datetime = $datetime->format('Y-m-d H:i:s');

    return $notif_datetime;
}

function parseDbMessage($telegram_message)
{
    $db_message = str_replace("\n", "<br>", $telegram_message);
    $db_message = convertBold($db_message);
    return $db_message;
}

function convertBold($msg)
{ // ChatGPT Function xd
    $reemplazar = true;
    return preg_replace_callback('/\*/', function () use (&$reemplazar) {
        $tag = $reemplazar ? '<b>' : '</b>';
        $reemplazar = !$reemplazar;
        return $tag;
    }, $msg);
}

function sendTelegramMessage($telegram_message, $room, $img_url)
{
    [$apiToken, $chat_id] = chooseTgBot($room);

    $data = [
        'chat_id' => $chat_id,
        'text' => $telegram_message,
        'parse_mode' => 'markdown'
    ];

    if ($img_url != NULL) {
        $data['photo'] = $img_url;
        $data['caption'] = $telegram_message;
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendPhoto?" . http_build_query($data));
    } else {
        $data['text'] = $telegram_message;
        $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data));
    }

    $result = json_decode($response, true);
    return [
        'success' => $result['ok'],
        'message_id' => $result['result']['message_id'] ?? null
    ];
}

function chooseTgBot($room)
{
    switch ($room) {
        case 1:
            $apiToken = "6579613970:AAHl-G5We20gpxYkred0bKJnA_U_iu2oPpo";
            $chat_id = '-1002138129798';
            break;
        case 2:
            $apiToken = "6657742338:AAFZZEFnJ4Phm-fqGLNCx2NWUCkLMrbIkaA";
            $chat_id = '-1002016137935';
            break;
        case 3:
            $apiToken = "6446216208:AAGmWFmBgYnjCF8ScZ4ng21k54oWkbkK6f4";
            $chat_id = '-1002135608367';
            break;

        case 4:
            $apiToken = "7418565569:AAF81a3JiOjlUz9jSD_LiFkEy-2eKO5o4Jk";
            $chat_id = '-1002414738423';
            break;
    }

    return [$apiToken, $chat_id];
}
