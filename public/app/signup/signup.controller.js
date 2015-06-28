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
            $mdToast.show(
                $mdToast.simple()
                    .content('You have successfully logged in')
                    .position('top left')
                    .hideDelay(3000)
            );
        }

        function loginErrorHandler(response) {
            if (typeof response.data.message === 'object') {
                angular.forEach(response.data.message, function(message) {
                    $mdToast.show(
                        $mdToast.simple()
                            .content(message[0])
                            .position('top left')
                            .hideDelay(3000)
                    );
                });
            } else {
                $mdToast.show(
                    $mdToast.simple()
                        .content(response.data ? response.data.message : response)
                        .position('top left')
                        .hideDelay(3000)
                );
            }
        }

        this.range = function(min, max, step) {
            step = step || 1;
            var input = [];
            for (var i = min; i <= max; i += step) {
                input.push(i);
            }
            return input;
        };
    }
})();
