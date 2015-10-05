

module.exports = function(grunt) {

    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        uglify: {
            my_target: {
                files: {
                    'dist/js/app.js': 'src/js/*.js'
                }
            }
        },

        sass: {
            dist: {
                files: {
                    'dist/css/main.css': 'src/sass/main.sass'
                }
            }
        },

        cssmin: {
            target: {
                files: [{
                    expand: true,
                    cwd: 'dist/css',
                    src: ['*.css', '!*.min.css'],
                    dest: 'dist/css',
                    ext: '.min.css'
                }]
            }
        },

        watch: {
            sass: {
                files: ['src/sass/**/*.sass', 'src/sass/main.sass'],
                tasks: ['sass', 'cssmin']
            },

            scripts: {
                files: ['src/js/*.js'],
                tasks: ['uglify']
            }
        }

    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('default', ['watch']);

};