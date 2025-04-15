<?php
if (extension_loaded('imagick')) {
    $imagick = new Imagick();
    echo 'Imagick version: ' . $imagick->getVersion()['versionString'];
} else {
    echo 'Imagick extension is not loaded.';
}
?>
