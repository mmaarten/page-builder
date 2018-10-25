module.exports = function( grunt ) 
{
	grunt.initConfig(
	{
		sass: 
		{ 
			dist:
			{
				options: 
				{ 
					style: 'expanded'
				},
				
				files:
				{
					'assets/css/editor.css': 'src/editor/scss/main.scss',
				},
			},
		},

		postcss: 
		{
			options: 
			{
				map: true, // Inline sourcemaps

				processors: 
				[
					// Add vendor prefixes
					require( 'autoprefixer' )( { browsers: 'last 2 versions' } ), 
				],
			},
			
			dist:
			{
				src: 'assets/css/*.css',
			},
		},

		cssmin: 
		{
			target: 
			{
				files: [
				{
					expand: true,
					cwd: 'assets/css',
					src: [ '*.css', '!*.min.css' ],
					dest: 'assets/css',
					ext: '.min.css'
				}],
			},
		},

		concat:
		{
			editor: 
			{
				src:
				[
					'src/editor/js/main.js',
					'src/editor/js/widgets.js',
				],
				
				dest: 'assets/js/editor.js',
			}
		},
		
		uglify:
		{
			options: 
			{
				compress: 
				{
					drop_console: true
				},
			},

			dist: 
			{
				files: [{
					expand: true,
					cwd: 'assets/js',
					src: [ '**/*.js', '!**/*.min.js' ],
					dest: 'assets/js',
					rename: function ( dst, src ) 
					{
						return dst + '/' + src.replace('.js', '.min.js');
					},
				}],
			},
		},

		copy: 
		{
			images:
			{
				expand: true,
				cwd: 'src/images/',
				src: '**',
				dest: 'assets/images/',
			},
			
			fonts:
			{
				expand: true,
				cwd: 'src/fonts/',
				src: '**',
				dest: 'assets/fonts/',
			},
			
			vendor: 
			{
				files: 
				[
					{ src: ['vendor/featherlight/src/featherlight.js'], dest: 'assets/js/featherlight.js' },
				],
			},
		},

		watch: 
		{
			styles:
			{
				files: 'src/**/*.scss',
				tasks: [ 'sass', 'postcss', 'cssmin' ],
			},

			scripts:
			{
				files: 'src/**/*.js',
				tasks: [ 'concat', 'uglify' ],
			},
		},
	});

	grunt.loadNpmTasks( 'grunt-contrib-sass' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );

	grunt.registerTask( 'dist', [ 'copy', 'sass', 'postcss', 'cssmin', 'concat', 'uglify' ] );
	grunt.registerTask( 'default', ['watch'] );
};
