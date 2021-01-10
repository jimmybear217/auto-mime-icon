<?php

    /**
     * auto-mime-icon/www/mime-icon.php
     * 
     * This is the main API file that will receice the requests and return
     * the image of the requested mime, or a default image.
     * The mimes are all based off linux mime definitions.
     * 
     * Credit for the icons goes to (PapirusDevelopmentTeam)[https://github.com/PapirusDevelopmentTeam/papirus-icon-theme/]
     * 
     * 
     * @author JimmyBear217
     * @since 20200109
     * 
     */

$mime='unknown';
if (!empty($_GET["mime"])) {
    $mime = $_GET["mime"];
}

/*$wantedType = "";
$valid_imageType = array("svg", "png");
if (!empty($_GET["type"])) {
    $wantedType = (in_array($_GET["type"], $valid_imageType)) ? $_GET["type"] : "";
}*/

// 1. check if file for this mime_type exists
//if ($wantedType == "" || $wantedType == "svg") {
    $mimeIcon = __DIR__ . "/icons/" . str_replace("/", "-", $mime) . ".svg";
    if (file_exists($mimeIcon)){
        header("Content-Type: " . mime_content_type($mimeIcon), true, 200);
        die(file_get_contents($mimeIcon));
    }    
//}
/*if ($wantedType == "" || $wantedType == "png") {
    $mimeIcon = __DIR__ . "/icons/" . str_replace("/", "-", $mime) . ".png";
    if (file_exists($mimeIcon)){
        header("Content-Type: " . mime_content_type($mimeIcon), true, 200);
        die(file_get_contents($mimeIcon));
    }    
}*/