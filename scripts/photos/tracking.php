<?php
header('Content-Type: application/json');
require_once __DIR__ . "/../common/connection_db.php";
require '../../vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Obtener los parámetros de la URL
$date = isset($_GET['date']) ? $_GET['date'] : null;
$clinic = isset($_GET['clinic']) ? $_GET['clinic'] : null;


// Verificar los parámetros recibidos
if ($date && $clinic) {
    // Preparar la consulta SQL
    $sql = "SELECT id, event_type, attendance_type, title, start, end, description, clinic, qualy, status, review_time, uploaded_by 
            FROM sa_events 
            WHERE (event_type = 'revision' OR event_type = 'tratamiento') 
            AND DATE(start) = ? 
            AND clinic = ? 
            ORDER BY start ASC"; // Ordenar por hora de inicio

    // Preparar la declaración
    if ($stmt = $conn->prepare($sql)) {
        // Enlazar los parámetros
        $stmt->bind_param("ss", $date, $clinic); // 's' indica que ambos son strings
        $stmt->execute();
        $result = $stmt->get_result();

        // Verificar si hay filas devueltas
        if ($result->num_rows > 0) {
            // Recuperar los datos en un array
            $events = $result->fetch_all(MYSQLI_ASSOC);

            // API de BunnyCDN
            $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
            $storageZoneName = 'rdi-enf-cdmx';
            $client = new Client([
                'base_uri' => 'https://la.storage.bunnycdn.com/',
                'headers' => [
                    'AccessKey' => $api_key,
                    'Accept' => 'application/json',
                ],
            ]);

            // Expresión regular para extraer el número de expediente
            $pattern = '/-\s*(\d+)$/';

            // Iterar sobre los eventos y extraer el número de expediente, luego buscar archivos
            foreach ($events as &$event) {
                $expedienteNumber = null;
                if (preg_match($pattern, $event['title'], $matches)) {
                    // Capturar el número de expediente
                    $expedienteNumber = $matches[1];
                }

                 // Añadir el número de expediente al evento
                 $event['expedienteNumber'] = $expedienteNumber ? $expedienteNumber : 0; // 0 si no se encuentra número de expediente

                // Si se encontró un número de expediente, buscar archivos en BunnyCDN
                if ($expedienteNumber) {
                    try {
                        // Hacer la solicitud GET para obtener la lista de archivos
                        $response = $client->request('GET', "$storageZoneName/$expedienteNumber/");
                        $body = $response->getBody();
                        $files = json_decode($body, true);

                        // Agregar los archivos encontrados al evento
                        if (is_array($files)) {
                            $event['files'] = [];

                            foreach ($files as $file) {
                                if (isset($file['ObjectName'])) {
                                    $event['files'][] = $file['ObjectName'];
                                }
                            }
                        }
                    } catch (RequestException $e) {
                        // Manejar errores de la solicitud y agregar mensaje de error
                        $event['files'] = [
                            "error" => "No se pudo acceder a la carpeta: " . $e->getMessage()
                        ];
                    }
                } else {
                    // Si no hay número de expediente, asignar un array vacío para archivos
                    $event['files'] = [];
                }
            }

            // Respuesta JSON que incluye los eventos con sus respectivos archivos
            echo json_encode([
                "success" => "true",
                "data" => $events, // Eventos con la lista de archivos añadidos
            ]);
        } else {
            echo json_encode([
                "success" => "true",
                "data" => [],
                "message" => "No se encontraron eventos para la fecha y clínica especificadas.",
            ]);
        }

        // Cerrar la declaración
        $stmt->close();
    } else {
        echo json_encode([
            "message" => "Error en la consulta SQL.",
            "success" => "false",
        ]);
    }
} else {
    echo json_encode([
        "message" => "Faltan parámetros: clinic o date.",
        "success" => "false",
    ]);
}

// Cerrar la conexión
$conn->close();
