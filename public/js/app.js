(function($) {

	"use-strict";
	
	var app = angular.module( "saApp", [
		"ngSanitize", "com.2fdevs.videogular", "com.2fdevs.videogular.plugins.controls"
	]);

	app.controller( 'SermonController', ['$scope', '$http', '$sce', function( $scope, $http, $sce ) {

		// Handle videogular
		$scope.API = null;

		$scope.config = {
			autoPlay: true,
		};

		$scope.onPlayerReady = function(API) {
			$scope.API = API;
		};

		$scope.formData = {};
		$scope.speaker_list = {};
		$scope.hasSermons = false;
		$scope.hasAudio = false;
		$scope.audioSrc = '';
		$scope.audioTitle = '';
		$scope.pages = {};

		/**
		 * Prepopulate the speakers list
		 */
		$http({
			url: 'server.php?' + $.param({ type: 'get-speakers', }),
			method: 'GET',
		}).then( function ( response ) {
			$scope.speaker_list = response.data;
			$scope.formData['speaker'] = $scope.speaker_list[1];
			$scope.processForm();
		});

		/**
		 * Gets the sermons
		 */
		$scope.processForm = function( args ) {

			$scope.formData.type = 'get-sermons';

			$http({
				url: 'server.php?' + $.param( $scope.formData ),
				method: 'GET',
				cache: true,
			}).then( function ( response ) {

				if( response.data ) {
					$scope.hasSermons = true;
					$scope.sermons = response.data;
				}

			});

		};

		$scope.changePlayerAudio = function( url, title ) {
			$scope.API.stop();
			$scope.audioSrc = [
				{ src: $sce.trustAsResourceUrl( url ), type: "audio/mp3" }
			];
			$scope.audioTitle = title;
			$scope.hasAudio = true;
		};

	}]);

})(jQuery);