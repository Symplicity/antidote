'use strict';

var path = require('path');
var gulp = require('gulp');

var karma = require('karma');

function runTests (singleRun, done) {
    karma.server.start({
        configFile: path.join(__dirname, '/../karma.conf.js'),
        singleRun: singleRun,
        autoWatch: !singleRun
    }, function(exitStatus) {
        // Karma's return status is not compatible with gulp's streams
        done(exitStatus ? 'There are failing unit tests' : undefined);
    });
}

gulp.task('test', ['scripts'], function(done) {
    runTests(true, done);
});

gulp.task('test:auto', ['watch'], function(done) {
    runTests(false, done);
});
