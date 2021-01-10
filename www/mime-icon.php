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

$filename = '';
if (!empty($_GET["filename"])) {
    $filename = $_GET["filename"];
}

switch ($mime) {
    case 'directory':
        $mimeIcon = __DIR__ . "/icons/directory.png";
        header("Content-Type: image/png", true);
        echo file_get_contents($mimeIcon);
        break;
    
    default:
        // check for existing icons in icons folder
        $mimeIconOriginal = __DIR__ . "/icons/" . str_replace("/", "-", $mime) . ".svg";
        if (file_exists($mimeIconOriginal)){
            header("Content-Type: image/svg+xml", true);
            echo file_get_contents($mimeIconOriginal);
        } else {
            // check for matching icons is papirus list
            $mimeIcon = __DIR__ . "/icons/papirus-svg/" . str_replace("/", "-", $mime) . ".svg";
            if (file_exists($mimeIcon)){
                header("Content-Type: image/svg+xml", true);
                echo file_get_contents($mimeIcon);
                $result = copy($mimeIcon, $mimeIconOriginal);
                chmod($mimeIconOriginal, 0666);
                error_log("Found: $mime for $filename | copy result: $result\n", 3, "mimes_not_found.log");
            } else {
                // write the mime in plain text and in logs
                header("Content-Type: text/plain", true);
                error_log("Mime not found: $mime for $filename\n", 3, "mimes_not_found.log");
                echo $mime;
            }
        }
        break;
}