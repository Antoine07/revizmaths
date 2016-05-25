/**
 * @author: Simon
 *
 */

var gulp = require('gulp'),
    sass = require('gulp-sass'),
    cleanCSS = require('gulp-clean-css'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    rename = require('gulp-rename');

var path = {

    'resources': {
        'sass': './web/bundles/revizfront/sass',
        'js':  './web/bundles/*/js/'
    },
    'public': {
        'css': './web/assets/css',
        'js': './web/assets/js'
    },
    'sass': './web/bundles/revizfront/sass/**/*.scss',
    'js': './src/Reviz/FrontBundle/Resources/assets/**/*.js'
};

// CSS

gulp.task('appCSS', function() {
    return gulp.src(path.resources.sass + '/app.scss')
        .pipe(sass())
        .pipe(cleanCSS())
        .pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest(path.public.css));
});

// JS

gulp.task('appJS', function() {
   return gulp.src(path.resources.js + '/**/*.js')
       .pipe(uglify())
       .pipe(concat('app.js'))
       .pipe(rename({suffix: '.min'}))
       .pipe(gulp.dest(path.public.js));
});

// Watch

gulp.task('watch', function() {
    gulp.watch(path.sass, ['appCSS']);
});

gulp.task('default', ['watch']);
