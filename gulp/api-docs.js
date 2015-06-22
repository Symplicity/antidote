'use strict';

var gulp = require('gulp');
var aglio = require('gulp-aglio');
var path = require('path');

gulp.task('api-docs', function() {
    gulp.src(path.join(__dirname, '/../docs/api/*.apib'))
        .pipe(aglio({template: 'default'}))
        .pipe(gulp.dest(path.join(__dirname, '/../public/api-docs')));
});
