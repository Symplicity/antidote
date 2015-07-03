(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('LoginCtrl', LoginCtrl);

    /** @ngInject */
    function LoginCtrl($mdToast, $auth, $state, LoginSignupModalService) {
        this.username = '';
        this.password = '';

        this.login = function() {
            $auth.login({username: this.username, password: this.password})
                .then(loginSuccessHandler)
                .catch(loginErrorHandler);
        };

        function loginSuccessHandler() {
            $mdToast.showSimple('You have successfully logged in');
            if ($state.current.name === 'login') {
                $state.go('home');
            } else {
                //login dialog
                LoginSignupModalService.close();
            }
        }

        function loginErrorHandler(response) {
            $mdToast.showSimple(response.data ? response.data.message : response);
        }
    }
})();
