var gulp = require('gulp');
var sass = require('gulp-sass');

//var sourcemaps = require('gulp-sourcemaps');
//var minifyCss = require('gulp-minify-css');
var watch = require('gulp-watch');

var rename = require('gulp-rename');

var concat = require('gulp-concat');
var uglify = require('gulp-uglify');


var livereload = require('gulp-livereload');

var gutil = require('gulp-util');

var path = {
  app: 'app/Resources',
  bower_components: './bower_components'
};




/**
* gulp-ruby-sass
* @see https://www.npmjs.com/package/gulp-ruby-sass
*
* Compile Sass to CSS using Compass.
*/
/*gulp.task('sass', function() {

  return sass(path.app + '/scss', { compass: true, style: 'compressed', sourcemap: false })
    .on('error', function (err) {
      console.error('Error!', err.message);
    })
    .pipe(minifyCss({keepSpecialComments:0}))
    //.pipe(sourcemaps.write())
    .pipe(rename({suffix: '.min'}))
    .pipe(gulp.dest('web/css/'));
});*/

gulp.task('sass', function() {
    return gulp.src(path.app + '/scss/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('web/css'));
});

gulp.task('js', function() {
    gulp.src([
            './bower_components/jquery/dist/jquery.js'
        ])
        .pipe(concat('jquery.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./web/js'));


    gulp.src([
            './bower_components/bootstrap-sass-official/assets/javascripts/bootstrap.js'
        ])
        .pipe(concat('bootstrap.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./web/js'));

    gulp.src([
            './bower_components/requirejs/require.js'
        ])
        .pipe(concat('require.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./web/js'));



    gulp.src([
            path.app + '/js/*.js'
        ])
        .pipe(concat('PRWspotify.js'))
        .pipe(uglify())
        .pipe(gulp.dest('./web/js'));

});



gulp.task('watch', function () {
    var onChange = function (event) {
        console.log('File '+event.path+' has been '+event.type);
        // Tell LiveReload to reload the window
        livereload.changed("");
    };
    // Starts the server
    livereload.listen();
    gulp.watch(path.app + '/scss/*.scss', ['sass'])
        .on('change', onChange);
    gulp.watch(path.app + '/js/*.js', ['js'])
        .on('change', onChange);
});




gulp.task('default', [







'sass'
]);
