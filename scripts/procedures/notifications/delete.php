<?php
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_ES.UTF-8');

session_start();

require_once __DIR__ . "/../../common/bunnynet.php";
require_once __DIR__ . "/../../common/utilities.php";
require_once __DIR__ . "/../../common/connection_db.php";

$room = $_POST['room'];

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
}

$lead_id = $_POST['lead_id'];
$message_id = isset($_POST['message_id']) ? $_POST['message_id'] : null;

$responseData = null;
$deletedFromTelegram = false;
$deletedFromDB = false;

try {
    if ($message_id !== null) {
        $url = "https://api.telegram.org/bot$apiToken/deleteMessage?" . http_build_query([
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ]);

        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
            ],
        ]);

        $response = file_get_contents($url, false, $context);
        $responseData = json_decode($response, true);

        if ($responseData && isset($responseData['ok']) && $responseData['ok']) {
            $deletedFromTelegram = true;
        }
    }

    $sql = $conn->prepare("DELETE FROM notifications WHERE lead_id = ? AND (telegram_msg_id = ? OR telegram_msg_id IS NULL);");
    $sql->bind_param("ii", $lead_id, $message_id);
    if ($sql->execute()) {
        $deletedFromDB = true;
    } else {
        throw new Exception("Hubo un error al borrar el mensaje de la Base de datos. Contacta al administrador");
    }

    $success = true;
    if ($deletedFromTelegram) {
        $message = "Notificación eliminada de la base de datos y de Telegram.";
    } else {
        $message = "Notificación eliminada solo de la base de datos.";
    }
} catch (Exception $e) {
    $success = false;
    $message = "Error: " . $e->getMessage();
}

// Depuración
echo json_encode([
    "success" => $success,
    "message" => $message,
    "deletedFromTelegram" => $deletedFromTelegram,
    "deletedFromDB" => $deletedFromDB,
    "responseData" => $responseData,
    "request" => [
        'chat_id' => $chat_id,
        'message_id' => $message_id
    ]
]);
?>
