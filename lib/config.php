<?php
require_once __DIR__ . '/../vendor/autoload.php';

use \Dallgoot\Yaml;

// Settings
$config = Yaml::parseFile(__DIR__ . '/../config.yaml', 0, $debug);

$config["base_url"] = "https://" . $_SERVER['SERVER_NAME'] . "/" . $config->base_path . "/";
$config['max_upload_size'] = 16 * 1024 * 1024;
$config['upload_path'] = __DIR__ . "/../" . $config->base_path;
