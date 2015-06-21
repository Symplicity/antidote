(function() {
    'use strict';

    angular
        .module('entic')
        .config(config);

    function config($mdThemingProvider, $authProvider) {
        $mdThemingProvider.theme('default')
            .primaryPalette('blue')
            .accentPalette('grey');

        $authProvider.loginUrl = '/api/auth/login';
        $authProvider.signupUrl = '/api/auth/signup';
    }

})();
