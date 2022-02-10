'use strict';

module.exports = function(grunt) {

	// Project configuration.
	grunt.initConfig({

		pkg: grunt.file.readJSON('package.json'),

		// Configure JSHint
		jshint: {
			test: {
				src: ['assets/js/*.js', '!assets/js/block-booking-form.js', '!assets/js/blocks.build.js']
			}
		},

		// Watch for changes on some files and auto-compile them
		watch: {
			js: {
				files: ['assets/js/*.js'],
				tasks: ['jshint']
			},
		},

		// Create a .pot file
		makepot: {
			target: {
				options: {
					processPot: function( pot, options ) {
						pot.headers['report-msgid-bugs-to'] = 'https://themeofthecrop.com';
						return pot;
					},
					type: 'wp-plugin',
				}
			}
		},

		// Build a package for distribution
		compress: {
			main: {
				options: {
					archive: 'restaurant-reservations-<%= pkg.version %>.zip'
				},
				files: [
					{
						src: [
							'*', '**/*',
							'!restaurant-reservations-<%= pkg.version %>.zip',
							'!.*', '!Gruntfile.js', '!package.json', '!node_modules', '!node_modules/**/*',
							'!**/.*', '!**/Gruntfile.js', '!**/package.json', '!**/node_modules', '!**/node_modules/**/*',
						],
						dest: 'restaurant-reservations/',
					}
				]
			}
		}

	});

	// Load tasks
	grunt.loadNpmTasks('grunt-contrib-compress');
	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-wp-i18n');

	// Default task(s).
	grunt.registerTask('default', ['watch']);
	grunt.registerTask('build', ['jshint']);
	grunt.registerTask('package', ['build', 'makepot', 'compress']);

};
