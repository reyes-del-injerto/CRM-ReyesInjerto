<?php
ini_set('error_reporting', -1);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
date_default_timezone_set('America/Mexico_City');
session_start();

require_once "../connection_db.php";

try {
    $lead_id = $_POST['lead_id'];

    $sql = "SELECT total, advance, method, date,
            (SELECT SUM(total) FROM sa_info_payment_px WHERE invoice_type = 'abono' AND lead_id = $lead_id AND status = 1) AS partials, 
            (SELECT SUM(total) FROM sa_info_payment_px WHERE invoice_type = 'producto' AND lead_id = $lead_id AND status = 1) AS products, 
            (SELECT SUM(total) FROM sa_info_payment_px WHERE invoice_type = 'tratamiento' AND lead_id = $lead_id AND status = 1) AS treatments 
            FROM sa_info_payment_px 
            WHERE invoice_type = 'anticipo' AND status = 1 AND lead_id = $lead_id;
    ";
    $query = mysqli_query($conn, $sql);

    if (!$query) {
        throw new Exception("Error: " . mysqli_error($conn));
    }
    if (mysqli_num_rows($query) == 1) {

        $row = mysqli_fetch_assoc($query);

        echo json_encode([
            "success" => true,
            "total" => ($row['total'] != null) ? floatval($row['total']) : 0,
            "advance" => ($row['advance'] != null) ? floatval($row['advance']) : 0,
            "partials" => ($row['partials'] != null) ? floatval($row['partials']) : 0,
            "products" => ($row['products'] != null) ? floatval($row['products']) : 0,
            "treatments" => ($row['treatments'] != null) ? floatval($row['treatments']) : 0,
            "method" => $row['method'],
            "date" => $row['date']
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "total" => 0,
            "advance" => 0,
            "partials" => 0,
            "products" => 0,
            "treatments" => 0
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
    ]);
}


/* else {
										// Llama a SweetAlert para confirmar el cambio de tab
										Swal.fire({
											title: '¿Quieres cambiar de formato?',
											text: "Perderás los cambios realizados",
											icon: 'warning',
											showCancelButton: true,
											confirmButtonColor: '#3085d6',
											cancelButtonColor: '#d33',
											confirmButtonText: 'Sí, cambiar!',
											cancelButtonText: 'Cancelar'
										}).then((result) => {
											if (result.isConfirmed) {
												// Si el usuario confirma, carga el formulario
												setProfileInfo(tab, url_form);
											}
										});
									} */