var gulp = require('gulp');
var shell = require('gulp-shell');

gulp.task('default', ['composer', 'update_database', 'concat', 'compress', 'compass'], function() {
    // place code for your default task here
});

gulp.task('compress', function() {
    var uglify = require('gulp-uglify');
    gulp.src('public/js/*.js')
            .pipe(uglify())
            .pipe(gulp.dest('public/js/min'));
});

gulp.task('concat', ['compress'], function() {
    // CAUTION: This must be the exact same files in reverse order than in application/layout/layout.phtml
    var concat = require('gulp-concat');
    var uglify = require('gulp-uglify');
    gulp.src('public/js/application/*.js')
            .pipe(concat('application.js'))
            .pipe(uglify())
            .pipe(gulp.dest('public/js/min/'));
});

gulp.task('update_database', ['composer'], shell.task([
    'php scripts/update_database.php'
]));

gulp.task('compass', function() {
    var compass = require('gulp-compass');
    gulp.src('application/sass/*.scss')
            .pipe(compass({
                project: __dirname,
                style: 'compressed',
                css: 'public/css',
                sass: 'application/sass',
                javascript: 'public/js',
                image: 'public/images',
                relative: true
            }))
            .pipe(gulp.dest('public/css'));
});

gulp.task('composer', function() {
    var composer = require('gulp-composer');
    return composer('install', {});
});
