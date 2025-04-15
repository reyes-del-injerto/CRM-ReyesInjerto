<?php

header('Content-Type: application/json');
require '../../vendor/autoload.php'; // Asegúrate de que la ruta sea correcta

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

$api_key = '67708a26-bc3d-4637-bce324a44a8d-9766-4ecb';
$storageZoneName = 'rdi-enf-cdmx';


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

// Directorio que deseas verificar

$folders = ["pre", "diseno", "post", "24horas", "10dias", "1mes", "3meses", "6meses", "9meses", "12meses", "15meses", "18meses"];
$dir_checker = array();

$client = new Client();

for ($i = 0; $i < count($folders); $i++) {

    $directorio = "../../temporal_storage/rdi-enf-cdmx/997/{$folders[$i]}/thumb";

    if (existeArchivoEnDirectorio($directorio)) {
        $dir_checker[$i] = true;
    } else {
        $dir_checker[$i] = false;
    }



    $response = $client->request('GET', "https://la.storage.bunnycdn.com/rdi-enf-cdmx/997/{$folders[$i]}/thumb/", [
        'headers' => [
            'AccessKey' => $api_key,
            'accept' => '*/*',
        ]
    ]);

    $body = $response->getBody();
    $files = json_decode($body, true);

    $contador = count($files);
    if ($contador > 0) {
        $dir_checker[$i] = ($dir_checker[$i] == false) ? true : false;
    }
}


print_r($dir_checker);
