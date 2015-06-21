'use strict';

module.exports = function(config) {

    var configuration = {
        autoWatch : false,

        frameworks: ['jasmine'],

        ngHtml2JsPreprocessor: {
            stripPrefix: 'src/',
            moduleName: 'gulpAngular'
        },

        browsers : ['PhantomJS'],

        plugins : [
          'karma-phantomjs-launcher',
          'karma-jasmine',
          'karma-ng-html2js-preprocessor'
        ],

        preprocessors: {
            'src/**/*.html': ['ng-html2js']
        }
    };

    config.set(configuration);
};
