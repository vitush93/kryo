module.exports = function (grunt) {

    // Execution time of grunt tasks
    require('time-grunt')(grunt);

    // Load all tasks
    require('load-grunt-tasks')(grunt);

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        watch: {
            less: {
                files: ['assets/less/**/*.less'],
                tasks: ['less:dev']
            },
            js: {
                files: ['assets/js/**/*.js'],
                tasks: ['browserify']
            }
        },

        // minify application bundle
        uglify: {
            build: {
                src: 'public/front/js/bundle.js',
                dest: 'public/front/js/bundle.js'
            }
        },

        // compiles and minifies the less files
        less: {
            dev: {
                options: {
                    paths: [
                        'node_modules/bootstrap/less',
                        'node_modules/font-awesome/less'
                    ]
                },
                files: {
                    'www/css/style.css': 'assets/less/main.less'
                }
            },
            prod: {
                options: {
                    plugins: [
                        new (require('less-plugin-autoprefix'))({browsers: ["last 2 versions"]})
                    ],
                    paths: [
                        'bower_components/bootstrap/less',
                        'bower_components/font-awesome/less'
                    ],
                    compress: true,
                    yuicompress: true,
                    optimization: 2
                },
                files: {
                    'www/css/style.css': 'assets/less/main.less'
                }
            }
        },

        browserify: {
            dist: {
                files: {
                    'www/js/bundle.js': ['assets/js/main.js']
                }
            }
        }

    });

    // Default task(s).
    grunt.registerTask('default', ['less:dev', 'browserify', 'watch']);

    grunt.registerTask('build', [
        'less:prod',
        'browserify',
        'uglify'
    ]);
};