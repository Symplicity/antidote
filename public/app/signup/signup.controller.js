(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('SignupCtrl', SignupCtrl);

    /** @ngInject */
    function SignupCtrl($mdToast, $auth) {

        this.user = {};

        this.authenticate = function(provider) {
            $auth.authenticate(provider)
                .then(loginSuccessHandler)
                .catch(loginErrorHandler);
        };

        this.signup = function() {
            $auth.signup({
                username: this.user.username,
                email: this.user.email,
                password: this.user.password,
                gender: this.user.gender,
                age: this.user.age
            }).catch(loginErrorHandler);
        };

        function loginSuccessHandler() {
            $mdToast.showSimple('You have successfully logged in');
        }

        function loginErrorHandler(response) {
            if (typeof response.data.message === 'object') {
                angular.forEach(response.data.message, function(message) {
                    $mdToast.showSimple(message[0]);
                });
            } else {
                $mdToast.showSimple(response.data ? response.data.message : response);
            }
        }
    }
})();
