<?php
require_once '../../vendor/autoload.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $phpWord = new PhpWord();

        // Configuración global de estilos
        $phpWord->setDefaultParagraphStyle([
            'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
            'spaceBefore' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
            'spaceAfter' => \PhpOffice\PhpWord\Shared\Converter::pointToTwip(0),
        ]);

        // Configuración de márgenes para la sección
        $sectionStyle = [
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1),
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1),
        ];

        // Crear la primera sección
        $section = $phpWord->addSection($sectionStyle);

        // Obtener el nombre del paciente desde POST
        $patientName = $_POST['selectedPatient'] ?? 'Paciente Desconocido';
        $patientName = preg_replace('/[^a-zA-Z0-9_-]/', '', $patientName); // Elimina caracteres no válidos

        // Añadir el título principal
        $section->addText(
            "$patientName - Área 1",
            ['bold' => true, 'size' => 16, 'alignment' => 'center']
        );

        // Definir áreas
        $areas = [
            'Área 1' => 'area1File',
            'Área 2' => 'area2File',
        ];

        // Procesar las áreas
        foreach ($areas as $areaName => $fileKeyPrefix) {
            try {
                // Crear una nueva sección para cada área
                $currentSection = $phpWord->addSection($sectionStyle);

                $currentSection->addText("$patientName - $areaName", ['bold' => true, 'size' => 14]);

                // Recopilar los archivos subidos para esta área
                $files = [];
                foreach ($_FILES as $key => $file) {
                    try {
                        if (strpos($key, $fileKeyPrefix) === 0 && is_uploaded_file($file['tmp_name'])) {
                            $filePath = $file['tmp_name'];

                            // Detectar si la imagen está en formato HEIC
                            $mimeType = mime_content_type($filePath);
                            if ($mimeType === 'image/heic' || $mimeType === 'image/heif') {
                                // Convertir HEIC a JPEG usando Imagick
                                try {
                                    $imagick = new Imagick();
                                    $imagick->readImage($filePath);
                                    $imagick->setImageFormat('jpeg');
                                    $convertedFilePath = tempnam(sys_get_temp_dir(), 'jpeg') . '.jpg';
                                    $imagick->writeImage($convertedFilePath);
                                    $imagick->clear();
                                    $imagick->destroy();

                                    // Reemplazar el archivo original por el convertido
                                    $filePath = $convertedFilePath;
                                } catch (Exception $e) {
                                    // Manejar errores de conversión
                                    error_log("Error al convertir archivo HEIC: " . $e->getMessage());
                                    throw new Exception("Error al convertir archivo HEIC.");
                                }
                            }

                            // Añadir el archivo (convertido o no) al listado
                            $files[] = $filePath;
                        }
                    } catch (Exception $e) {
                        error_log("Error al procesar el archivo: " . $e->getMessage());
                        throw new Exception("Error al procesar el archivo: " . $e->getMessage());
                    }
                }

                if (empty($files)) {
                    $currentSection->addText("No se enviaron fotos para $areaName.");
                } else {
                    // Organizar imágenes en filas de acuerdo a la cantidad
                    $table = $currentSection->addTable();
                    $rowImages = [];

                    foreach ($files as $index => $filePath) {
                        $rowImages[] = $filePath;

                        // Si hay 2 imágenes en la fila o es la última imagen, crear la fila
                        if (count($rowImages) === 2 || $index === count($files) - 1) {
                            $table->addRow();

                            if (count($rowImages) === 1 && $index === count($files) - 1) {
                                $cell = $table->addCell(9000, ['gridSpan' => 2]); // Combina dos celdas
                                $cell->addImage($rowImages[0], [
                                    'width' => 200,
                                    'height' => 150,
                                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
                                ]);
                            } else {
                                foreach ($rowImages as $img) {
                                    $table->addCell(4500)->addImage($img, [
                                        'width' => 200,
                                        'height' => 150,
                                        'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
                                    ]);
                                }

                                if (count($rowImages) === 1) {
                                    $table->addCell(4500); // Celda vacía
                                }
                            }
                            $rowImages = [];
                        }
                    }
                }

                // Si es el Área 2, añadir texto adicional
                if ($areaName == 'Área 2') {
                    $currentSection->addText(
                        "Se realiza evaluación dermatoscopica de las zonas donadora y receptora del paciente, para obtener valor de cobertura de la zona a implantar. De acuerdo a los cálculos realizados basados en la fórmula internacional de valor de cobertura del Dr. Erdogan, se estima una extracción de ____________ Unidades Foliculares.\n\n"
                            . "Me es explicado y estoy de acuerdo con el diseño previamente definido y sugerido por mí, donde requiero que solo sea presentado en las imágenes anteriores. Autorizo llevar a cabo el procedimiento de microtrasplante.\n\n"
                            . "MANIFIESTO QUE TODA LA INFORMACIÓN ES VERÍDICA Y COMPLETA, Y QUE NO OMITÍ DATOS EN MI BENEFICIO.\n\n",
                        ['size' => 12, 'alignment' => 'left']
                    );

                    // Añadir 5 saltos de línea
                    for ($i = 0; $i < 5; $i++) {
                        $currentSection->addTextBreak();
                    }
                    // Añadir la línea de firma centrada
                    $currentSection->addText(
                        "____________________________________________",
                        ['size' => 12],
                        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
                    );

                    // Añadir 2 saltos de línea después de la línea de firma
                    $currentSection->addTextBreak(2);

                    // Añadir la palabra "FIRMA" centrada
                    $currentSection->addText(
                        "FIRMA",
                        ['size' => 12, 'bold' => true],
                        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
                    );
                }
            } catch (Exception $e) {
                error_log("Error al procesar el área $areaName: " . $e->getMessage());
                $section->addText("Error al procesar el área $areaName: " . $e->getMessage());
            }
        }

        // Guardar el archivo en la memoria y enviarlo como respuesta
        $fileName = 'Fotos_Areas_' . $patientName . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Length: ' . filesize($tempFile));
        readfile($tempFile);

        unlink($tempFile);
        exit;
    } catch (Exception $e) {
        error_log("Error general en el proceso: " . $e->getMessage());
        echo "Ocurrió un error durante el proceso. Detalles: " . $e->getMessage();
        exit;
    }
} else {
    http_response_code(405); // Método no permitido
    echo "Método no permitido";
}
