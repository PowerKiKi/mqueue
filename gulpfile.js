var gulp = require('gulp');
var uglify = require('gulp-uglify');
var concat = require('gulp-concat');
var shell = require('gulp-shell');
var compass = require('gulp-compass');


gulp.task('default', ['update_database', 'concat', 'compress', 'compass'], function() {
    // place code for your default task here
});

gulp.task('compress', function() {
    gulp.src('public/js/*.js')
            .pipe(uglify())
            .pipe(gulp.dest('public/js/min'));
});

gulp.task('concat', ['compress'], function() {
    // CAUTION: This must be the exact same files in reverse order than in application/layout/layout.phtml
    gulp.src('public/js/application/*.js')
            .pipe(concat('application.js'))
            .pipe(uglify())
            .pipe(gulp.dest('public/js/min/'));
});

gulp.task('update_database', shell.task([
    'php scripts/update_database.php'
]));

gulp.task('compass', function() {
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
