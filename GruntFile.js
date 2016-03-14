module.exports = function (grunt) {
	
	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),
		watch: {
			javascript: {
				files: ['examples/assets/js/app.js'],
				tasks: ['concat', 'uglify']
			},
			sass: {
				files: 'examples/assets/sass/**/*.scss',
				tasks: ['compass', 'concat']
			}
		},
		compass: {
			dist: {
				options: {
					relativeAssets: true,
					sassDir: 'examples/assets/sass',
					cssDir: 'examples/assets/css',
					imagesDir: 'examples/assets/img',
					environment: 'production',
					outputStyle: 'compressed'
				}
			}
		},
		uglify: {
			main: {
				files: {
					'examples/assets/js/app.min.js': ['examples/assets/js/app.min.js']
				}
			}
		},
		concat: {
			options: {
				separator: '\n\n'
			},
			siteJS: {
				src: [
					'bower_components/jquery/dist/jquery.min.js',
					'bower_components/angular/angular.min.js',
					'bower_components/angular-sanitize/angular-sanitize.min.js',
					'bower_components/videogular/videogular.min.js',
					'bower_components/videogular-controls/vg-controls.min.js',
					'examples/assets/js/app.js'
				],
				dest: 'examples/assets/js/app.min.js'
			},
			siteCSS: {
				src: [
					'examples/assets/css/app.css'
				],
				dest: 'examples/assets/css/app.css'
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['compass', 'concat', 'uglify']);
};