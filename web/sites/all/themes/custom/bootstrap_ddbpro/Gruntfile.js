module.exports = function (grunt) {
    grunt.initConfig({
        watch: {
            src: {
                files: ['**/*.scss', '**/*.php'],
                tasks: ['compass:dev']
            },
           options: {
                livereload: true,
            },
        },
        compass: {
            dev: {
                options: {
                    sassDir: 'sass',
                    cssDir: 'css',
                    imagesPath: 'img',
                    noLineComments: true,
                    outputStyle: 'compressed'
                }
            }
        }
    });
    grunt.registerTask('default', ['watch']);
    
    grunt.loadNpmTasks('grunt-contrib-compass');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-watch');
};