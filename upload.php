<?php
// Description: Dumb uploader for flameshot software fork.
// https://github.com/seamus-45/flameshot/tree/custom-upload-hosting
include "./lib/config.php";
// Creates a short link by creating an actual symlink
function get_short_link($sha1, $ext)
{
    $link_size = 6;
    $full = $sha1;
    $short = substr($sha1, 0, $link_size);
    $link = false;
    if (file_exists($short)) {
        $link = readlink($short);
    }

    while ((false != $link) && (basename($link) != $full)) {
        $link_size += 1;
        $short = substr($sha1, 0, $link_size) . '.' . $ext;
        $link = readlink($short);
    }
    if ($link == false) {
        symlink($full, $short);
    }
    return $short;
}

// Upload file
function uploadfile($file, $config)
{
    // Check error value.
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
        default:
            throw new RuntimeException('Unknown error.');
    }

    // Check filesize.
    if ($file['size'] > $config['max_upload_size']) {
        throw new RuntimeException('Exceeded filesize limit.');
    }

    // Check MIME Type.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($file['tmp_name']),
        $config->allowed_exts,
        true
    )) {
        //throw new RuntimeException('Invalid file format.');
    }

    // Obtain safe unique name from its binary data.
    $sha1 = sha1_file($file['tmp_name']);
    $finalname = $sha1;
    print($config["upload_path"] . "/" . $finalname);

    // Move file to final location
    if (!move_uploaded_file(
        $file['tmp_name'],
        $config["upload_path"] . "/" . $finalname
    )) {
        throw new RuntimeException('Failed to move uploaded file.');
    }

    // Make short URL if requested
    if ($config['shorten_url']) {
        $finalname = get_short_link($sha1, $ext);
    }

    return ['name' => $file['name'], 'link' => $config['base_url'] . $finalname, 'hash' => $sha1, 'size' => $file['size']];
}

// Main
header('Content-Type:  application/json; charset=utf-8');
$answer = [];

$upload_path = realpath($config['upload_path']) . DIRECTORY_SEPARATOR;
chdir($upload_path);

try {
    // Make some checks
    if (
        !isset($_FILES["image"]["error"]) ||
        !is_array($_FILES["image"])
    ) {
        throw new RuntimeException('Invalid parameters.');
    }

    $key = $_GET['key'];

    if (!isset($key) || $key === '' || !in_array($key, $config->keys)) {
        throw new RuntimeException('Invalid api key.');
    }
    $result = uploadfile($_FILES['image'], $config);

    $answer['success'] = true;
    $answer['data'] = $result;
} catch (RuntimeException $e) {

    $answer['success'] = false;
    $answer['status'] = 400;
    $answer['description'] = $e->getMessage();
    http_response_code(400);
}

print(preg_replace('/\\\"/', "\"", json_encode($answer)));
