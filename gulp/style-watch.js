'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');

gulp.task('style-watch', function() {

    gulp.watch([
        path.join(conf.paths.src, '/app/**/*.scss')
    ], function() {
        gulp.start('css');
    });

});

gulp.task('css', ['styles'], function() {
    return gulp.src([
        conf.paths.tmp + '/serve/**/*',
        '!' + conf.paths.tmp + '/serve/index.html'
    ]).pipe(gulp.dest(conf.paths.src + '/'));
});
