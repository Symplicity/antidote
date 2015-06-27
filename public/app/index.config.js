(function() {
    'use strict';

    angular
        .module('antidote')
        .config(config);

    function config($locationProvider, $mdThemingProvider, $authProvider) {

        $locationProvider.html5Mode(true);

        $mdThemingProvider.theme('default')
            .primaryPalette('light-blue', {
                'default':'800',
                'hue-1':'50',
                'hue-2':'900'
            })
            .accentPalette('amber', {
                'default':'500'
            });

        $authProvider.loginUrl = '/api/auth/login';
        $authProvider.signupUrl = '/api/auth/signup';
        $authProvider.logoutRedirect = '/home';
    }

})();
