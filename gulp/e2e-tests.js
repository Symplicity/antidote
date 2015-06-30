'use strict';

var path = require('path');
var gulp = require('gulp');
var conf = require('./conf');

var $ = require('gulp-load-plugins')();

function runProtractor (done) {
    var params = process.argv;
    var args = params.length > 3 ? [params[3], params[4]] : [];
    if (process.env.CI) {
        args.push('--baseUrl=http://localhost:8000/index.html');
    }

    gulp.src(path.join(conf.paths.e2e, '/**/*.js'))
    .pipe($.protractor.protractor({
        configFile: 'protractor.conf.js',
        args: args
    }))
    .on('error', function(err) {
        // Make sure failed tests cause gulp to exit non-zero
        throw err;
    })
    .on('end', function() {
        done();
    });
}

gulp.task('protractor', runProtractor);
