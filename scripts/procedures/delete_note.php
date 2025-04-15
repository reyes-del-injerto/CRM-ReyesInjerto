<?php
// Incluir el archivo de conexión a la base de datos
require_once __DIR__ . "/../common/connection_db.php";

// Comprobar si el ID de la nota se ha enviado a través de POST
if (isset($_POST['id'])) {
    // Sanitizar el ID para evitar inyecciones SQL
    $noteId = intval($_POST['id']);

    // Preparar la consulta para eliminar la nota
    $stmt = $conn->prepare("DELETE FROM medical_notes WHERE id = ?");
    $stmt->bind_param("i", $noteId);

    // Ejecutar la consulta
    if ($stmt->execute()) {
        // Si la eliminación fue exitosa, enviar una respuesta JSON
        echo json_encode(['status' => 'success', 'message' => 'Nota eliminada correctamente.']);
    } else {
        // Si hubo un error, enviar una respuesta JSON con error
        echo json_encode(['status' => 'error', 'message' => 'No se pudo eliminar la nota.']);
    }

    // Cerrar la declaración y la conexión
    $stmt->close();
    $conn->close();
} else {
    // Si no se envió el ID, enviar una respuesta de error
    echo json_encode(['status' => 'error', 'message' => 'ID de la nota no proporcionado.']);
}
?>
