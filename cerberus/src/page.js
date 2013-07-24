var cerberusApp = angular.module('cerberusApp', []);

cerberusApp.controller('CerberusCtrl', ['$scope', '$http', function ($scope, $http) {
	$scope.url = '';
	$scope.projects = [];
	$scope.projectId = 0;
	$scope.files = [];
	$scope.total = 0;
	$scope.changed = 0;

	/**
	 * @param {string} url
	 * @param {[]} projects
	 */
	$scope.setup = function (url, projects) {
		$scope.url = url;
		$scope.projects = projects;
	};

	$scope.deploy = function () {
		var key = 0,
			url = $scope.url + 'deploy.php';

		if ($scope.files.length === 0) {
			return;
		}

		function callDeploy() {
			$scope.files[key].uploading = true;
			$scope.files[key].project = $scope.projectId;

			$.post(
				url,
				$scope.files[key],
				function (json) {
					$scope.files[key].uploading = false;
					$scope.files[key].localPath = json.success ? 'Done' : 'Failed...';

					++key;
					callDeploy();
				});
		}

		callDeploy();
	};

	$scope.cancel = function () {
		$scope.files = [];
	};

	$scope.loadProject = function (id) {
		$scope.projectId = id;

		var url = $scope.url + 'files.php?project=' + id;

		$('body').showLoader();

		$http.get(url).success(function (json) {
			$scope.total = json.total;
			$scope.changed = json.changed;
			$scope.files = json.files;

			$.fn.removeLoader();
		});
	};
}]);


(function ($) {
	var selector = '#loader',
		$loader;

	$.fn.showLoader = function () {
		var $this = this,
			left,
			top;

		$loader = $(selector);

		if ($loader.length === 0) {
			$('body').append('<div id="loader"/>');
			$loader = $(selector);
		}

		left = $this.offset().left;
		left += $this.width() / 2;
		left -= 16;

		top = $this.offset().top;
		top += $this.height() / 2;
		top -= 16;

		$loader.css({
			left: Math.round(left) + 'px',
			top: Math.round(top) + 'px'
		});

		$loader.stop().fadeIn('fast');
	};

	$.fn.removeLoader = function () {
		$loader.stop().fadeOut(
			'fast',
			function () {
				$loader.remove();
			}
		);
	}
}(jQuery));