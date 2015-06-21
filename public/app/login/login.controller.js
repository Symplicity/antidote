'use strict';

angular.module('entic')
    .controller('LoginCtrl', function($mdToast, $auth) {
        this.login = function() {
            $auth.login({email: this.email, password: this.password})
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
    });
