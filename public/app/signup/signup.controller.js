(function() {
    'use strict';

    angular
        .module('entic')
        .controller('SignupCtrl', SignupCtrl);

    /** @ngInject */
    function SignupCtrl($mdToast, $auth) {

        this.authenticate = function(provider) {
            $auth.authenticate(provider)
                .then(loginSuccessHandler)
                .catch(loginErrorHandler);
        };

        this.signup = function() {
            $auth.signup({
                name: this.name,
                email: this.email,
                password: this.password
            }).catch(loginErrorHandler);
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
            if (typeof response.data.message === 'object') {
                angular.forEach(response.data.message, function(message) {
                    $mdToast.show(
                        $mdToast.simple()
                            .content(message[0])
                            .position('bottom right')
                            .hideDelay(3000)
                    );
                });
            } else {
                $mdToast.show(
                    $mdToast.simple()
                        .content(response.data ? response.data.message : response)
                        .position('bottom right')
                        .hideDelay(3000)
                );
            }
        }
    }
})();
