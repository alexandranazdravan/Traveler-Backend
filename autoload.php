<?php

/**
 * Recursively loads all php files in all subdirectories of the given path
 * @param $directory
 */
function autoload($directory) {

    // get a listing of the current directory
    $scanned_dir = scandir($directory);


    // Remove the ignored items
    $scanned_dir = array_diff($scanned_dir, ['.', '..']);

    foreach ($scanned_dir as $item) {

        $filename  = $directory . '/' . $item;
        $real_path = realpath($filename);
        $filetype = filetype( $real_path );

        if ($real_path === false || empty($filetype)) {
            continue;
        }

        // if it's a directory then recursively load it; if it's a file, load it
        if ($filetype === 'dir') {

            autoload($real_path);
        } else if ($filetype === 'file') {

            // do not allow files that have been uploaded & only load files that really exist
            if (is_readable($real_path) !== true ||
                is_uploaded_file($real_path ||
                    file_exists($real_path) !== true)) {
                continue;
            }

            $pathinfo = pathinfo($real_path);

            // do not load an empty filename
            if ( empty($pathinfo['filename'] ) ) {
                continue;
            }

            // extension required and it must be php
            if (empty($pathinfo['extension']) || 'php' !== $pathinfo['extension']) {
                continue;
            }

            require_once($real_path);
        }
    }
}
