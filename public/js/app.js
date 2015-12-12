(function($) {

	var app = angular.module('saApp', []);

	app.controller('SermonController', ['$scope', '$http', '$sce', function( $scope, $http, $sce ) {

		$scope.formData = {};

		$scope.list = {};

		$scope.currentSpeaker = '';

		$scope.hasSermons = false;

		$scope.hasAudio = false;

		$scope.audioSrc = '';

		$http({
			url: 'server.php?' + $.param({ type: 'get-speakers', }),
			method: 'GET',
		}).then( function ( response ) {

			$scope.list =  response.data;

		});

		$scope.processForm = function() {

			$scope.formData.type = 'get-sermons';

			$http({
				url: 'server.php?' + $.param( $scope.formData ),
				method: 'GET',
				cache: true,
			}).then( function ( response ) {

				if( response.data ) {

					$scope.hasSermons = true;

					$scope.currentSpeaker = $scope.formData.speaker;

					$scope.sermons = chunk( response.data, 4 );

				}

			});

		};

		$scope.changePlayerAudio = function( url ) {

			$scope.audioSrc = url;
			
			if( $scope.hasAudio == false ) {
				$scope.hasAudio = true;
			}
		
		};

		function chunk( a, b ) {
			for (var c = [], d = 0; d < a.length; d += b) c.push(a.slice(d, d + b));
			return c;
		}

	}]);
	
	app.filter( 'trustUrl', [ '$sce', function($sce) {
		return function( url ) {
			return $sce.trustAsResourceUrl( url );
		}
	}]);

})(jQuery);