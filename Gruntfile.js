

module.exports = function(grunt) {

    grunt.initConfig({

        pkg: grunt.file.readJSON('package.json'),

        react: {
            combined_file_output: {
                files: {
                    'src/js/app.js': [
                        'src/jsx/App.jsx'
                    ]
                }
            }
        },

        uglify: {
            my_target: {
                files: {
                    'dist/js/app.min.js': 'src/js/*.js'
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
                files: ['src/jsx/*.jsx'],
                tasks: ['react', 'uglify']
            }
        }

    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-react');

    grunt.registerTask('default', ['watch']);

};