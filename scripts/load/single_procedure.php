<?php
header('Content-Type: application/json');

require_once "../connection_db.php";
//require_once "../../vendor/autoload.php"; // Asegúrate de que la ruta sea correcta

//use GuzzleHttp\Client;
//use GuzzleHttp\Exception\RequestException;

$px_sales_id = $_POST['px_sales_id'];

$sql = "SELECT ep.px_sales_id, ep.num_med_record, ep.room, ep.specialist, ep.notes, CONCAT(sig.first_name, ' ',sig.last_name) name, sig.clinic , DATE_FORMAT(sig.procedure_date,'%d.%m.%Y') AS procedure_date, sig.procedure_type FROM enf_procedures ep INNER JOIN sa_info_general_px sig ON ep.px_sales_id = sig.id WHERE ep.px_sales_id = $px_sales_id;";

$query = mysqli_query($conn, $sql);

$row = mysqli_fetch_assoc($query);

$px_num_med_record = $row['num_med_record'];
$clinic = 1;

switch ($clinic) {
    case 1: // CDMX
        $api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
        $storageZoneName = 'rdi-enf-cdmx';
        break;
    case 2: // Culiacán
        $api_key = '90086039-bce6-43d4-bc3dc22d891c-ee35-4e6b';
        $storageZoneName = 'rdi-enf-cul';
        break;
    case 3: // Mazatlán
        $api_key = 'bfae151f-118b-4428-acc65e702314-1987-4471';
        $storageZoneName = 'rdi-enf-mzt';
        break;
    case 4: // Tijuana
        $api_key = 'bc1fee1f-25c4-43cc-9662f7fd5588-a964-497b';
        $storageZoneName = 'rdi-enf-tij';
        break;
    default:
        echo 0;
}

/*

$folders = ["pre", "diseno", "post", "24horas", "10dias", "1mes", "3meses", "6meses", "9meses", "12meses", "15meses", "18meses"];
$dir_checker = array();

$client = new Client();

for ($i = 0; $i < count($folders); $i++) {

    $directorio = "../../temporal_storage/rdi-enf-cdmx/{$px_num_med_record}/{$folders[$i]}/thumb";
    if (is_dir($directorio)) {

        if (existeArchivoEnDirectorio($directorio)) {
            $dir_checker[$i] = true;
        } else {
            $dir_checker[$i] = false;
        }
    } else {
        // Si el directorio no existe, establecemos la bandera a false
        $dir_checker[$i] = false;
    }


    $response = $client->request('GET', "https://la.storage.bunnycdn.com/rdi-enf-cdmx/{$px_num_med_record}/{$folders[$i]}/thumb/", [
        'headers' => [
            'AccessKey' => $api_key,
            'accept' => '* / *',
        ]
    ]);

    $body = $response->getBody();
    $files = json_decode($body, true);

    $contador = count($files);
    if ($contador > 0) {
        $dir_checker[$i] = ($dir_checker[$i] == false) ? true : $dir_checker[$i];
    }
}


function existeArchivoEnDirectorio($directorio)
{
    // Obtener lista de archivos y directorios en el directorio
    $archivos = scandir($directorio);

    // Iterar sobre la lista de archivos y verificar si hay algún archivo no deseado
    foreach ($archivos as $archivo) {
        // Excluir "." y ".."
        if ($archivo != '.' && $archivo != '..') {
            // Excluir archivos ocultos (que empiezan con un punto)
            if ($archivo[0] != '.') {
                // Se encontró al menos un archivo no deseado
                return true;
            }
        }
    }

    // No se encontraron archivos no deseados
    return false;
}
*/
// Directorio que deseas verificar

//$data = array("patient" => $row, "dir_checker" => $dir_checker);
$data = array("patient" => $row);
echo json_encode($data);
