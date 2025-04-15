<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);

session_start();
require_once '../../common/connection_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = $_POST['ids'];

    // Si hay IDs para eliminar
    if (!empty($ids)) {
        // Prepara una consulta para eliminar varias filas
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "DELETE FROM ad_nomina WHERE id IN ($placeholders)";
        $stmt = $conn->prepare($sql);

        // Vincula los parámetros
        $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);

        // Ejecuta la consulta
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Filas eliminadas correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar las filas.']);
        }

        // Cerrar la declaración
        $stmt->close();
    }
}

// Cerrar la conexión
$conn->close();
?>
