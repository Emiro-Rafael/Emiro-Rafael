// GULP PACKAGES
// Most packages are lazy loaded
var gulp  = require('gulp'),
    gutil = require('gulp-util'),
    filter = require('gulp-filter'),
	sourcemaps = require('gulp-sourcemaps'),
    touch = require('gulp-touch-cmd'),
    plugin = require('gulp-load-plugins')(),
	rename = require('gulp-rename');


// GULP VARIABLES
// Modify these variables to match your project needs

// Select Foundation components, remove components project will not use
const SOURCE = {
    // Place custom JS here, files will be concatenated, minified if ran with --production
    scripts: ['assets/js/**/*.js', '!assets/js/**/main.js'],

	// Scss files will be concatenated, minified if ran with --production
	styles: 'assets/scss/**/*.scss',

	// Images placed here will be optimized
	images: 'assets/images/**/*',

	php: '**/*.php'
};

const ASSETS = {
	styles: 'assets/',
	scripts: 'assets/js/',
	images: 'assets/images/',
	all: 'assets/'
};

const JSHINT_CONFIG = {
	"node": true,
	"globals": {
		"document": true,
		"window": true,
		"jQuery": true,
		"$": true,
		"Foundation": true
	}
};

// GULP FUNCTIONS
// JSHint, concat, and minify JavaScript
gulp.task('scripts', function() {

	// Use a custom filter so we only lint custom JS
	const CUSTOMFILTER = filter(ASSETS.scripts + '**/*.js', {restore: true});

	return gulp.src(SOURCE.scripts)
		.pipe(plugin.plumber(function(error) {
            gutil.log(gutil.colors.red(error.message));
            this.emit('end');
        }))
		.pipe(plugin.sourcemaps.init())
		.pipe(plugin.babel({
			presets: ['es2015'],
			compact: true,
		}))
		.pipe(CUSTOMFILTER)
			.pipe(plugin.jshint(JSHINT_CONFIG))
			.pipe(plugin.jshint.reporter('jshint-stylish'))
			.pipe(CUSTOMFILTER.restore)
		.pipe(plugin.concat('main.js'))
		.pipe(plugin.uglify())
		.pipe(plugin.sourcemaps.write('.')) // Creates sourcemap for minified JS
		.pipe(gulp.dest(ASSETS.scripts))
		.pipe(touch());
});

// Compile Sass, Autoprefix and minify
gulp.task('styles', function() {
	return gulp.src(SOURCE.styles)
		.pipe(plugin.plumber(function(error) {
            gutil.log(gutil.colors.red(error.message));
            this.emit('end');
        }))
		.pipe(plugin.sourcemaps.init())
		.pipe(plugin.sass())
		.pipe(plugin.autoprefixer({
		    browsers: [
		    	'last 2 versions',
		    	'ie >= 9',
				'ios >= 7'
		    ],
		    cascade: false
		}))
		.pipe(plugin.cssnano({safe: true, minifyFontValues: {removeQuotes: false}}))
		.pipe(plugin.sourcemaps.write())
		.pipe(rename('style.css'))
		.pipe(gulp.dest(ASSETS.styles))
		.pipe(touch());
});

// Optimize images, move into assets directory
gulp.task('images', function() {
	return gulp.src(SOURCE.images)
		.pipe(plugin.imagemin())
		.pipe(gulp.dest(ASSETS.images))
		.pipe(touch());
});

//  gulp.task( 'translate', function () {
//      return gulp.src( SOURCE.php )
//          .pipe(plugin.wpPot( {
//              domain: 'jointswp',
//              package: 'Example project'
//          } ))
//         .pipe(gulp.dest('file.pot'));
//  });

// Browser-Sync watch files and inject changes
// gulp.task('browsersync', function() {

//     // Watch these files
//     var files = [
//     	SOURCE.php,
//     ];

//     browserSync.init(files, {
// 	    proxy: LOCAL_URL,
//     });

//     gulp.watch(SOURCE.styles, gulp.parallel('styles')).on('change', browserSync.reload);
//     gulp.watch(SOURCE.scripts, gulp.parallel('scripts')).on('change', browserSync.reload);
//     gulp.watch(SOURCE.images, gulp.parallel('images')).on('change', browserSync.reload);

// });

// Watch files for changes (without Browser-Sync)
gulp.task('watch', function() {

	// Watch .scss files
	gulp.watch(SOURCE.styles, gulp.parallel('styles'));

	// Watch scripts files
	gulp.watch(SOURCE.scripts, gulp.parallel('scripts'));

	// Watch images files
	gulp.watch(SOURCE.images, gulp.parallel('images'));

});

// Run styles, js, images
gulp.task('default', gulp.parallel('styles', 'scripts', 'images'));