(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('LoginCtrl', LoginCtrl);

    /** @ngInject */
    function LoginCtrl($mdToast, $auth) {
        this.login = function() {
            $auth.login({username: this.username, password: this.password})
                .then(loginSuccessHandler)
                .catch(loginErrorHandler);
        };
        this.authenticate = function(provider) {
            $auth.authenticate(provider)
                .then(loginSuccessHandler)
                .catch(loginErrorHandler);
        };

        function loginSuccessHandler() {
            $mdToast.show(
                $mdToast.simple()
                    .content('You have successfully logged in')
                    .position('bottom right')
                    .hideDelay(3000)
            );
        }

        function loginErrorHandler(response) {
            $mdToast.show(
                $mdToast.simple()
                    .content(response.data ? response.data.message : response)
                    .position('bottom right')
                    .hideDelay(3000)
            );
        }
    }
})();
