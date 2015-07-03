(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('SignupCtrl', SignupCtrl);

    /** @ngInject */
    function SignupCtrl($mdToast, $auth, $state, LoginSignupModalService) {

        this.user = {};

        this.signup = function() {
            $auth.signup({
                username: this.user.username,
                email: this.user.email,
                password: this.user.password,
                gender: this.user.gender,
                age: this.user.age
            })
                .then(signupSuccessHandler)
                .catch(signupErrorHandler);
        };

        function signupSuccessHandler() {
            $mdToast.showSimple('You have successfully signed up');
            if ($state.current.name === 'signup') {
                $state.go('home');
            } else {
                //login dialog
                LoginSignupModalService.close();
            }
        }

        function signupErrorHandler(response) {
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
