<?php
function copyFolder($source, $destination) {
    // Create destination folder if it does not exist
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }

    // Scan source directory
    $files = scandir($source);

    foreach ($files as $file) {
        if ($file == "." || $file == "..") {
            continue;
        }

        $srcFilePath = $source . DIRECTORY_SEPARATOR . $file;
        $destFilePath = $destination . DIRECTORY_SEPARATOR . $file;

        // If it's a directory, copy recursively
        if (is_dir($srcFilePath)) {
            copyFolder($srcFilePath, $destFilePath);
        } else {
            // Copy file to destination
            copy($srcFilePath, $destFilePath);
        }
    }
}

// Define source and destination paths
$destinationPath = "C:/xampp/htdocs/onlinelibrary/uploads";
$sourcePath = "C:/xampp/htdocs/onlinelibrary/admin/uploads";

// Start copying process
copyFolder($sourcePath, $destinationPath);

echo "Files successfully updated in admin/uploads!";
?>
