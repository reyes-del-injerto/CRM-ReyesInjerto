<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
date_default_timezone_set('America/Mexico_City');
header('Content-Type: application/json');

session_start();
require_once __DIR__ . "/../common/connection_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    if ($input && isset($input['contact_id'])) {
        $contact_id = (isset($input['contact_id'])) ? $input['contact_id'] : exit();
        $created_at = date("Y-m-d H:i:s");
        $first_name = (isset($input['first_name'])) ? $input['first_name'] : '';
        $last_name = (isset($input['last_name'])) ? $input['last_name'] :  '';
        $lead_type = (isset($input['lead_type'])) ? $input['lead_type'] : '';
        $phone = (isset($input['phone'])) ? $input['phone'] : '';
        $clinic = (isset($input['clinic'])) ? $input['clinic'] : '';
        $platform = (isset($input['platform'])) ? $input['platform'] : '';

        $seller_id = isset($input['seller']) ? $input['seller'] : null;

        switch ($seller_id) {
            case "282849":
                $seller = "Marisol Olmos";
                break;
            case "314895":
                $seller = "Janeth Ruiz";
                break;
            case "427821":
                $seller = "Adriana Silva";
                break;
            default:
                $seller = "Desconocido"; // O cualquier valor por defecto que prefieras
                break;
        }

        $interested_in = "";
        $notes = "";
        $stage = "Nuevo Lead";
        $quali = "En conversación";
        $date = date("Y-m-d H:i:s");
        $link = "https://app.respond.io/space/166799/message/" . $contact_id;

        try {
            // Verificar si el link ya existe
            $check_sql = $conn->prepare("SELECT id FROM sa_leads WHERE link = ?");
            $check_sql->bind_param("s", $link);
            $check_sql->execute();
            $check_sql->store_result();

            if ($check_sql->num_rows > 0) {
                throw new Exception("El link ya existe");
            }

            $check_sql->close();

            // Insertar el nuevo registro si no hay duplicados
            $sql = $conn->prepare("INSERT INTO sa_leads (created_at, first_name, last_name, clinic, origin, phone, interested_in, stage, quali, link, notes, seller, last_activity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $sql->bind_param("sssssssssssss", $created_at, $first_name, $last_name, $clinic, $platform, $phone, $interested_in, $stage, $quali, $link, $notes, $seller, $date);

            if (!$sql->execute()) {
                throw new Exception("Error al insertar registro");
            }

            $sql->close();

            $response = "Todo bien";
            echo json_encode(array('message' => $response));
        } catch (mysqli_sql_exception $e) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(array('error' => 'Chale, ocurrió un error: ' . $e->getMessage()));
            exit;
        }
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(array('error' => 'El JSON recibido no es válido.'));
    }
} else {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(array('error' => 'Método no permitido. Se esperaba un POST.'));
}
