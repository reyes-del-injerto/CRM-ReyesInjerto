<?php
// Configura la respuesta por defecto
$response = [
    'success' => false, 
    'message' => '', 
    'file' => '', 
    'file_path' => '', 
    'realpath' => ''
];

// Verifica si se ha recibido una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica que el parámetro 'file' esté presente en la solicitud
    if (isset($_POST['file'])) {
        $file = $_POST['file'];
        $response['file'] = basename($file); // Nombre del archivo
        $response['file_path'] = $file; // Ruta del archivo

        // Construye la ruta desde la raíz del proyecto ERP
        $filePath = __DIR__ . '/../../' . $file; // Cambia esto según tu estructura de carpetas
        $response['realpath'] = $filePath; // Agrega el valor real de la ruta para depuración

        // Verifica si el archivo existe
        if (file_exists($filePath)) {
            // Intenta eliminar el archivo
            if (unlink($filePath)) {
                $response['success'] = true;
                $response['message'] = 'Archivo borrado correctamente.';
            } else {
                $response['message'] = 'No se pudo eliminar el archivo.';
            }
        } else {
            $response['message'] = 'El archivo no existe.';
            $response['realpath_check'] = realpath($filePath); // Verifica la ruta real
        }
    } else {
        $response['message'] = 'Parámetro de archivo no proporcionado.';
    }
} else {
    $response['message'] = 'Método no permitido.';
}

// Enviar la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
