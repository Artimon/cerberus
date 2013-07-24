<?php

error_reporting(E_ALL);

require_once "cerberus/ftp.php";
require_once "cerberus/deployfile.php";
require_once "cerberus/deployment.php";

$configs = require_once "config.php";
$config = $configs[$_GET['project']];

chdir($config['projectDir']);

$deployment = new Deployment();
$deployment->setRoot($config['root']);

foreach ($config['folders'] as $folderName => $filesOnly) {
	$deployment->addFolder($folderName, $filesOnly);
}

if (array_key_exists('configs', $config)) {
	foreach ($config['configs'] as $generator => $target) {
		$deployment->addConfig($generator, $target);
	}
	$deployment->generateConfigs();
}

if (array_key_exists('clean', $config)) {
	foreach ($config['clean'] as $folderName) {
		$deployment->cleanDirectory($folderName);
	}
}

$deployFiles = $deployment->deployFiles($config['login']);

$json = array(
	'total' => 0,
	'changed' => 0,
	'files' => array()
);

foreach ($deployFiles as $deployFile) {
	$json['total']++;

	if ($deployFile->hasIgnoreAction()) {
		continue;
	}

	$json['changed']++;
	$json['files'][] = array(
		'remotePath'	=> $deployFile->remotePath(),
		'localPath'		=> $deployFile->localPath(),
		'action'		=> $deployFile->action(),
		'delete'		=> $deployFile->hasDeleteAction()
	);
}

echo json_encode($json);