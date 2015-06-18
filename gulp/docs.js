'use strict';

var gulp = require('gulp');
var aglio = require('gulp-aglio');

module.exports = function (options) {
	gulp.task('docs', function () {
		gulp.src('api-documentation/*.md')
			.pipe(aglio({template: 'default'}))
			.pipe(gulp.dest('public/api-documentation'));
	});
};
