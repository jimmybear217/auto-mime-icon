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
    $mime = trim($_GET["mime"]);
}

$filename = '';
if (!empty($_GET["filename"])) {
    $filename = trim($_GET["filename"]);
}

// aliases
$mimeRedirections = array(
    "application-x-javascript" => "text-x-javascript",
    "application/x-javascript" => "text-x-javascript",
    "application/javascript" => "text-x-javascript",
    "application-x-php" => "text-x-php",
    "application/x-php" => "text-x-php",
    "application/php" => "text-x-php",
    "text-xml" => "application-xml",
    "text/xml" => "application-xml"
);

$textRedirections = array(
    "js" => "text-x-javascript",
    "cpp" => "text-x-c++",
    "c" => "text-x-c"
);

// get file extentions
$fileExtention = explode('.', $filename);
$fileExtention = $fileExtention[count($fileExtention)-1];
$fileExtention = trim($fileExtention);

switch (explode('/', $mime)[0]) {
    case 'directory':
        $mimeIcon = __DIR__ . "/icons/folder-cyan.svg";
        header("Content-Type: image/svg+xml", true, 200);
        echo file_get_contents($mimeIcon);
        break;

    case 'image':
        $mimeIcon = __DIR__ . "/icons/image-x-generic.svg";
        header("Content-Type: image/svg+xml", true, 200);
        echo file_get_contents($mimeIcon);
        error_log("Image Mime: $mime for $filename\n", 3, "mimes_not_found.log");
        break;

    case 'text':

        // default icon
        $mimeIcon = __DIR__ . "/icons/text-x-generic.svg";

        // text/cpp => text-cpp
        $mimeIconAttempt = __DIR__ . "/icons/" . str_replace("/", "-", $mime) . ".svg";
        if (file_exists($mimeIconAttempt)) {
            $mimeIcon = $mimeIconAttempt;
        }

        // text/cpp => text-x-cpp
        $mimeIconAttempt = __DIR__ . "/icons/" . str_replace("/", "-x-", $mime) . ".svg";
        if (file_exists($mimeIconAttempt)) {
            $mimeIcon = $mimeIconAttempt;
        }
            
        // redirections of mimes to mimes
        if (in_array($mimeRedirections, array_keys($mimeRedirections))) {
            $mimeIcon = __DIR__ . "/icons/" . $mimeRedirections[$mime]. ".svg";
        }

        // redirections of extentions to mimes
        if (in_array($fileExtention, $textRedirections)) {
            $mimeIcon = __DIR__ . "/icons/" . $textRedirections[$fileExtention]. ".svg";
            error_log("Debug: extention $fileExtention: $mimeIcon\n", 3, "mimes_not_found.log");
        }
            
        header("Content-Type: image/svg+xml", true, 200);
        echo file_get_contents($mimeIcon);
        error_log("Text Mime: $mime for $filename with extention $fileExtention: $mimeIcon\n", 3, "mimes_not_found.log");
        break;
    
    default:
        // check for existing icons in icons folder
        $mimeIconOriginal = __DIR__ . "/icons/" . str_replace("/", "-", $mime) . ".svg";
        if (file_exists($mimeIconOriginal)){
            header("Content-Type: image/svg+xml", true, 200);
            echo file_get_contents($mimeIconOriginal);
        } elseif (in_array($mimeRedirections, array_keys($mimeRedirections))) {
            $mimeIcon = __DIR__ . "/icons/" . $mimeRedirections[$mime]. ".svg";
            header("Content-Type: image/svg+xml", true, 200);
            echo file_get_contents($mimeIcon);
        } else {
            // check for matching icons is papirus list
            $mimeIcon = __DIR__ . "/icons/papirus-svg/" . str_replace("/", "-", $mime) . ".svg";
            if (file_exists($mimeIcon)){
                header("Content-Type: image/svg+xml", true, 200);
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