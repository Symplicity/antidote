(function() {
    'use strict';

    angular
        .module('antidote')
        .config(config);

    function config($mdThemingProvider, $authProvider) {
        $mdThemingProvider.theme('default')
            .primaryPalette('indigo', {
                'default':'500',
                'hue-1':'50',
                'hue-2':'900'
            })
            .accentPalette('teal', {
                'default':'300'
            });

        $authProvider.loginUrl = '/api/auth/login';
        $authProvider.signupUrl = '/api/auth/signup';
    }

})();
