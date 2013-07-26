<?php

$configs = require_once "config.php";

$projects = array();
foreach ($configs as $key => $config) {
	$projects[] = array(
		'id' => $key,
		'title' => $config['title']
	);
}
$projects = json_encode($projects);

echo "
<!DOCTYPE html>
<html ng-app='cerberusApp'>
<head>
	<title>Cerberus Deployment</title>

	<link rel='stylesheet' type='text/css' href='cerberus/src/style.css'>
</head>
<body ng-controller='CerberusCtrl' ng-init='setup(\"{$_SERVER["REQUEST_URI"]}\", {$projects})'>
	<div class='content'>
		<h1>Cerberus</h1>
		<h2>Projects</h2>

		<ul>
			<li ng-repeat='project in projects'>
				<a ng-click='loadProject(project.id)'>Load {{project.title}}</a>
			</li>
		</ul>

	<div ng-show='files'>
		<h2>Deployment</h2>

		<p>
			{{changed}} / {{total}} files changed.
		</p>

		<p>
			<button ng-click='deploy()'>Deploy</button>
			<button ng-click='cancel()'>Cancel</button>
		</p>

		<div ng-repeat='file in files'>
			<div class='action' ng-class='{ 4: \"delete\" }[file.action]'></div>
			<span class='path' ng-hide='file.uploading'>{{file.localPath}}</span>
			<div class='loaderBar' ng-show='file.uploading'></div>
		</div>
	</div>

	<div ng-show='nothingToDoHere'>
		<h2>Deployment</h2>

		<p>Gratulations! Everything is up to date.</p>
	</div>

	<script type='text/javascript' src='cerberus/src/jquery-1.8.0.min.js'></script>
	<script type='text/javascript' src='cerberus/src/angular.min.js'></script>
	<script type='text/javascript' src='cerberus/src/page.js'></script>
</body>
</html>";
