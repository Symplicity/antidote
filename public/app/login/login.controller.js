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
            $mdToast.showSimple('You have successfully logged in');
        }

        function loginErrorHandler(response) {
            $mdToast.showSimple(response.data ? response.data.message : response);
        }
    }
})();
