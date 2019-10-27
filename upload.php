<?php
// This example will help you download latest version of Wordpress
// to your server with out uploading from your computer.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>h2, p {font-family: "Trebuchet MS", Helvetica, sans-serif;}</style>
    <title>SimplePHPUploader</title>
</head>
<body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"], ENT_QUOTES, "utf-8"); ?>" method="post">
        Direct File URL:<br>
        <input type="text" name="url" value="https://wordpress.org/latest.zip">
        <br>
        Save as:<br>
        <input type="text" name="filename" value="wordpress-latest.zip">
        <br><br>
        <input name="button" type="submit" value="Fetch">
    </form>
</body>
</html>

<?php
if (isset($_POST["button"])) {

    $filename = $_POST['filename'];
    $url = $_POST['url'];
    $file = file_put_contents($filename, fopen($url, 'r'));

    if($file != false){
        echo "<h2>Download complete!</h2><p>" . $file . " bytes saved as <a href='" . $filename . "'>" . $filename . "</a>";
    } else {
        echo "<h2>Download was unsuccessful.<h2><code>Download Failed.</code>";
    }

}
?>
