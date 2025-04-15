<?php
function sanitizeFileName($filename) {
    $unwanted_array = [
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
        'ñ'=>'n', 'Ñ'=>'N',
        'ä'=>'a', 'ë'=>'e', 'ï'=>'i', 'ö'=>'o', 'ü'=>'u',
        'Ä'=>'A', 'Ë'=>'E', 'Ï'=>'I', 'Ö'=>'O', 'Ü'=>'U'
    ];

    // Replace accented characters and ñ
    $filename = strtr($filename, $unwanted_array);

    // Remove special characters except _ and space
    $filename = preg_replace('/[^A-Za-z0-9_\s\.\-]/', '', $filename);

    return $filename;
}

function renameFilesAndDirectories($dir) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        $originalPath = $file->getPathname();
        $originalName = $file->getFilename();
        $sanitizedName = sanitizeFileName($originalName);
        $newPath = $file->getPath() . DIRECTORY_SEPARATOR . $sanitizedName;

        echo "Processing: $originalPath\n";

        if ($originalName !== $sanitizedName) {
            if (!file_exists($newPath)) {
                if (rename($originalPath, $newPath)) {
                    echo "Renamed: $originalPath -> $newPath\n";
                } else {
                    echo "Failed to rename: $originalPath to $newPath\n";
                    echo "Error details: ";
                    print_r(error_get_last());
                    echo "\n";
                }
            } else {
                echo "Skipped (already exists): $originalPath\n";
            }
        }
    }
}

// Change this line to the path of your 'buscar' folder
$directoryToScan = __DIR__ . './docs';
echo "vamoo\n";
echo "Scanning directory: $directoryToScan\n";
renameFilesAndDirectories($directoryToScan);
?>
