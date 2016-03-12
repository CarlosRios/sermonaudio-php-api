module.exports = function (grunt) {
	
	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),
		watch: {
			javascript: {
				files: ['public/js/app.js'],
				tasks: ['concat', 'uglify']
			},
			sass: {
				files: 'public/sass/**/*.scss',
				tasks: ['compass', 'concat']
			}
		},
		compass: {
			dist: {
				options: {
					relativeAssets: true,
					sassDir: 'public/sass',
					cssDir: 'public/css',
					imagesDir: 'public/img',
					environment: 'production',
					outputStyle: 'compressed'
				}
			}
		},
		uglify: {
			main: {
				files: {
					'public/js/app.min.js': ['public/js/app.min.js']
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
					'public/js/app.js'
				],
				dest: 'public/js/app.min.js'
			},
			siteCSS: {
				src: [
					'public/css/app.css'
				],
				dest: 'public/css/app.css'
			}
		}
	});

	grunt.loadNpmTasks('grunt-contrib-compass');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-watch');

	grunt.registerTask('default', ['compass', 'concat', 'uglify']);
};