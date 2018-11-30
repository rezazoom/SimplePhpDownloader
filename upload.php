<?php

// This example will help you download Wordpress 4.9.8
// to your server with out uploading from your computer.

$url = "https://fa.wordpress.org/wordpress-4.9.8-fa_IR.zip";
$filename = "wordpress-fa.zip";

$file = file_put_contents($filename, fopen($url, 'r'));

if($file != false){
echo "<h2>Download complete!</h2><p>" . $file . " bytes saved as <a href='" . $filename . "'>" . $filename . "</a>";
} else {
echo "<h2>Download was unsuccessful.<h2><code>Download Failed.</code>";
}
?>