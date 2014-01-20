module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
	  react: {
	    files: {
	      expand: true,
	      cwd: 'components/',
	      src: ['**/*.jsx'],
	      dest: 'js/views/',
	      ext: '.js'
	    }
	  }
	});

  // Load the plugin that provides the "uglify" task.
  grunt.loadNpmTasks('grunt-react');
  // Default task(s).
  grunt.registerTask('default', ['react']);

};