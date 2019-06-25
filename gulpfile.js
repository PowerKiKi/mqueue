const {parallel, series, src, dest} = require('gulp');

function execWithOutput(command) {
    const cp = require('child_process');
    return cp.exec(command, (err, stdout, stderr) => {
        console.log(stdout);
        console.warn(stderr);
    });
}

function compress() {
    const uglify = require('gulp-uglify');

    return src('public/js/*.js')
        .pipe(uglify())
        .pipe(dest('public/js/min'));
}

function concatenate() {
    // CAUTION: This must be the exact same files in reverse order than in application/layout/layout.phtml
    const concat = require('gulp-concat');
    const uglify = require('gulp-uglify');

    return src('public/js/application/*.js')
        .pipe(concat('application.js'))
        .pipe(uglify())
        .pipe(dest('public/js/min/'));
}

function update_database() {
    return execWithOutput('php bin/update_database.php');
}

function sass() {
    const sass = require('gulp-sass');

    return src('application/sass/**/*.scss')
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
        .pipe(dest('public/css'));
}

function composer() {
    return execWithOutput('composer install --ansi --classmap-authoritative')
}

const server = series(composer, update_database);
const client = series(compress, concatenate, sass);

/**
 * Main tasks
 */
exports.default = parallel(server, client);
