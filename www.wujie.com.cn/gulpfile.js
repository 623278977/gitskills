//var elixir = require('laravel-elixir');
var gulp = require('gulp'),
    minifycss = require('gulp-minify-css'),
    rename = require('gulp-rename'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    del = require('del'),
    concat = require('gulp-concat');
    //notify = require('gulp-notify');
//新版本脚本压缩
gulp.task('v0240scripts', function() {
    return gulp.src('public/js/v0240/src/**/*.js')
        //.pipe(jshint('.jshintrc'))
        //.pipe(jshint.reporter('default'))
        //.pipe(concat('main.js'))
        //.pipe(gulp.dest('dist/scripts'))
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/v0240/dist'));
        //.pipe(notify({ message: 'Scripts task complete' }));
});
//老版本的JS压缩
gulp.task('oldscripts', function() {
    return gulp.src('public/js/*.js')
        .pipe(rename({ suffix: '.min' }))
        .pipe(uglify())
        .pipe(gulp.dest('public/js/dist'));
});
gulp.task('minifycss',function(){
    return gulp.src('public/css/*.css')
        //.pipe(rename({ suffix: '.min' }))
        .pipe(minifycss())
        .pipe(gulp.dest('public/css/dist'));
});
gulp.task('clean',function(cb){
    del(['public/css/dist','public/js/dist','public/js/v0240/dist'],cb);
});
gulp.task('default',function(){
    gulp.start('minifycss','oldscripts','v0240scripts');
});