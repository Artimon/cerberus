<?php

require_once "cerberus/ftp.php";
require_once "cerberus/uploadfile.php";
require_once "cerberus/deployfile.php";

$configs = require_once "config.php";
$config = $configs[$_POST['project']];

chdir($config['projectDir']);

UploadFile::getInstance()->commit($config['login'], true);