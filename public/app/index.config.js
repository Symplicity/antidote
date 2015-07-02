(function() {
    'use strict';

    angular
        .module('antidote')
        .config(config);

    function config($locationProvider, $mdThemingProvider, $authProvider, $compileProvider) {

        $locationProvider.html5Mode(true);
        $compileProvider.debugInfoEnabled(false);

        $mdThemingProvider.theme('default')
            .primaryPalette('light-blue', {
                'default':'700',
                'hue-1':'50',
                'hue-2':'800'
            })
            .accentPalette('amber', {
                'default':'500'
            });

        $authProvider.baseUrl = '/api';
        $authProvider.logoutRedirect = '/home';
    }

})();
