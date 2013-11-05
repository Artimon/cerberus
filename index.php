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

?><!DOCTYPE html>
<html ng-app='cerberusApp'>
<head>
	<title>Cerberus Deployment</title>

	<link rel='stylesheet' type='text/css' href='cerberus/src/style.css'>
	<link rel='stylesheet' type='text/css' href='cerberus/src/bootstrap.min.css'>
</head>
<body style='padding-top: 50px;'
	ng-controller='CerberusCtrl'
	ng-init='setup("<?php echo $_SERVER['REQUEST_URI']; ?>", <?php echo $projects; ?>)'
	ng-click="showDropDown = false">
<div class='navbar navbar-default navbar-fixed-top'>
	<div class='container'>
		<div class='navbar-header'>
			<a href='?' class='navbar-brand'>Cerberus by PadSoft</a>
		</div>
		<div class='navbar-collapse collapse' id='navbar-main'>
			<ul class="nav navbar-nav">
				<li class="dropdown" ng-class="{ open: showDropDown }">
					<a href="javascript:;"
						class="dropdown-toggle"
						id="projects"
						ng-click="toggleDropDown($event)">
						Projects
						<span class="caret"></span>
					</a>

					<ul class="dropdown-menu" aria-labelledby="projects">
						<li ng-repeat='project in projects'>
							<a href="javascript:;" tabindex="-1"
								ng-click='loadProject(project.id)'>
								{{project.title}}
							</a>
						</li>
					</ul>
				</li>
			</ul>
			<ul class='nav navbar-nav navbar-right'>
				<li>
					<a href='https://github.com/Artimon/cerberus'>GitHub Repo</a>
				</li>
				<li>
					<a href='http://www.pad-soft.de/'>PadSoft</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="container">
	<div class="page-header" id="banner">
		<div class="row">
			<div class="col-lg-12">
				<h1>Cerberus</h1>
				<p class="lead">
					Simple Deployment System
				</p>
			</div>
		</div>
	</div>

	<div class="row" ng-show='files'>
		<div class="col-lg-12">
			<h2>Project Status</h2>
		</div>
	</div>

	<div class="row" ng-show='files'>
		<div class="col-lg-4">
			<ul class="list-group">
				<li class="list-group-item">
					<span class="badge">{{total}}</span>
					Total Files
				</li>
				<li class="list-group-item">
					<span class="badge">{{changed}}</span>
					Changed
				</li>
			</ul>
		</div>
		<div class="col-lg-4">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h3 class="panel-title">Progress</h3>
				</div>
				<div class="panel-body">
					<div class="progress progress-striped">
						<div class="progress-bar progress-bar-success" ng-style="{ width: progress + '%' }"></div>
					</div>
					<button class="btn btn-primary" ng-click="deploy()">Deploy</button>
					<button class="btn btn-default" ng-click="cancel()">Cancel</button>
				</div>
			</div>
		</div>
		<div class="col-lg-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">File Upload</h3>
				</div>
				<div class="panel-body">
					<div ng-repeat='file in files'>
						<p>
							<span class="label label-primary" ng-hide="file.action == 4">Update</span>
							<span class="label label-danger" ng-show="file.action == 4">Delete</span>
						</p>

						<p ng-hide='file.uploading'>{{file.localPath}}</p>

						<div class="progress progress-striped active" ng-show='file.uploading'>
							<div class="progress-bar" style="width: 100%"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row" ng-show='nothingToDoHere'>
		<div class="col-lg-4">
			<div class="alert alert-dismissable alert-info">
				<strong>Congratulations!</strong>
				Everything is up to date.
			</div>
		</div>
	</div>
</div>

<script type='text/javascript' src='cerberus/src/jquery-1.8.0.min.js'></script>
<script type='text/javascript' src='cerberus/src/angular.min.js'></script>
<script type='text/javascript' src='cerberus/src/page.js'></script>
</body>
</html>