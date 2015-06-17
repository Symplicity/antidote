'use strict';

var gulp = require('gulp');
var browserSync = require('browser-sync');

var $ = require('gulp-load-plugins')();

var wiredep = require('wiredep').stream;

module.exports = function(options) {
    gulp.task('styles', function() {
        var sassOptions = {
            style: 'expanded'
        };

        var injectFiles = gulp.src([
            options.src + '/styles/**/*.scss',
            '!' + options.src + '/styles/18f.scss',
            '!' + options.src + '/styles/vendor.scss'
        ], {
            read: false
        });

        var injectOptions = {
            transform: function(filePath) {
                filePath = filePath.replace(options.src + '/styles/', '');
                return '@import \'' + filePath + '\';';
            },
            starttag: '// injector',
            endtag: '// endinjector',
            addRootSlash: false
        };

        var indexFilter = $.filter('index.scss');
        var vendorFilter = $.filter('vendor.scss');
        var cssFilter = $.filter('**/*.css');

        return gulp.src([
                options.src + '/styles/18f.scss',
                options.src + '/styles/vendor.scss'
            ])
            .pipe(indexFilter)
            .pipe($.inject(injectFiles, injectOptions))
            .pipe(indexFilter.restore())
            .pipe(vendorFilter)
            .pipe(wiredep(options.wiredep))
            .pipe(vendorFilter.restore())
            .pipe($.rubySass(sassOptions)).on('error', options.errorHandler('RubySass'))
            .pipe(cssFilter)
            .pipe($.sourcemaps.init({
                loadMaps: true
            }))
            .pipe($.autoprefixer()).on('error', options.errorHandler('Autoprefixer'))
            .pipe($.sourcemaps.write())
            .pipe(cssFilter.restore())
            .pipe(gulp.dest(options.tmp + '/serve/styles/'))
            .pipe(browserSync.reload({
                stream: true 
            }));
    });
};
