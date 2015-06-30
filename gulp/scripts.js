'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');
var jshint = require('gulp-jshint');

gulp.task('scripts', function() {
    return gulp.src(path.join(conf.paths.src, '/app/**/*.js'))
      .pipe(jshint())
      .pipe(jshint.reporter('jshint-stylish'));
});
