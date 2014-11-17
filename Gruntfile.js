/* Gruntfile */
module.exports = function (grunt) {
    var path = require('path'),
        SOURCE_DIR = 'src/',
        BUILD_DIR = 'build/',
        DIST_DIR = 'dist/';

    require('matchdep').filterDev('grunt-contrib-*', './package.json').forEach(grunt.loadNpmTasks);


    // Project configuration.
    grunt.initConfig({
            pkg: grunt.file.readJSON('package.json'),
            copy: {
                build: {
                    files: [
                        // includes files within path and its sub-directories
                        {
                            cwd: SOURCE_DIR,
                            expand: true,
                            src: ['**'],
                            dest: BUILD_DIR
                        }
                    ]
                }
            },
            compress: {
                dist: {
                    options: {
                        archive: DIST_DIR + '<%= pkg.name %>.zip'
                    },
                    expand: true,
                    cwd: BUILD_DIR,
                    src: ['**']
                }
            },
            uglify: {
                build: {
                    expand: true,
                    cwd: SOURCE_DIR,
                    dest: BUILD_DIR,
                    ext: '.js',
                    src: ['*.js', '**/*.js']
                }
            },
            cssmin: {
                build: {
                    expand: true,
                    cwd: SOURCE_DIR,
                    src: ['*.css', '**/*.css'],
                    dest: BUILD_DIR,
                    ext: '.css'
                }
            }
        }
    );
// Default task(s).
    grunt.registerTask('build', ['copy:build', 'uglify:build', 'cssmin:build']);
    grunt.registerTask('dist', ['compress:dist']);

};

