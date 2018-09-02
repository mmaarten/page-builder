module.exports = function(grunt) {

  // Project configuration.
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),
    sass: {
      dist: {
        options: {
          style: 'expanded'
        },
        files: {
          // 'destination': 'source'
          'css/editor.css': 'scss/editor/main.scss',
          'css/front.css': 'scss/front/main.scss'
        }
      }
    },
    postcss: {
      options: {
        map: true, // inline sourcemaps

        processors: [
          require('autoprefixer')({browsers: 'last 2 versions'}), // add vendor prefixes
        ]
      },
      dist: {
        src: 'css/*.css'
      }
    },
    concat: {
      editor: {
        src: [
          'js/src/editor/class.js',
          'js/src/editor/event-manager.js',
          'js/src/editor/main.js',
          'js/src/editor/fields.js',
          'js/src/editor/fields/editor.js',
          'js/src/editor/fields/icon.js',
          'js/src/editor/fields/image.js',
          'js/src/editor/fields/post.js',
          'js/src/editor/fields/repeater.js',
          'js/src/editor/fields/tab.js',
          'js/src/editor/fields/term.js',
          'js/src/editor/fields/url.js',
          'js/src/editor/widgets.js',
          'js/src/editor/widgets/row.js',
          'js/src/editor/widgets/column.js',
          'js/src/editor/controls.js',
          'js/src/editor/controls/add.js',
          'js/src/editor/controls/edit.js',
          'js/src/editor/controls/copy.js',
          'js/src/editor/controls/delete.js',
        ],
        dest: 'js/dist/editor.js',
      },
      front: {
        src: [
          'js/src/front/common.js',
          'js/src/front/cover-image.js',
          'js/src/front/widgets/map.js',
          'js/src/front/widgets/post.js',
        ],
        dest: 'js/dist/front.js',
      }
    },
    uglify:
    {
      options: {
        compress: {
          drop_console: true
        }
      },
      editor: {
        files: {
          'js/dist/editor.min.js': 'js/dist/editor.js',
          'js/dist/front.min.js': 'js/dist/front.js'
        }
      }
    },
    cssmin: {
      target: {
        files: [{
          expand: true,
          cwd: 'css',
          src: ['*.css', '!*.min.css'],
          dest: 'css',
          ext: '.min.css'
        }]
      }
    },
    watch: {
      css: {
        files: '**/*.scss',
        tasks: ['sass', 'postcss', 'cssmin'],
        options: {
          livereload: true,
        },
      },
      scripts: {
        files: ['js/src/**/*.js'],
        tasks: ['concat', 'uglify'],
        options: {
          interrupt: true
        }
      }
    }
  });

  // tasks.
  grunt.loadNpmTasks( 'grunt-contrib-sass' );
  grunt.loadNpmTasks( 'grunt-postcss' );
  grunt.loadNpmTasks( 'grunt-contrib-uglify' );
  grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
  grunt.loadNpmTasks( 'grunt-contrib-watch' );
  grunt.loadNpmTasks( 'grunt-contrib-concat' );

  // Default task(s).
  grunt.registerTask( 'default', ['watch'] );

};