'use strict';

angular.module('entic')
    .controller('ResetPasswordCtrl', function($auth, $mdToast, Password, $stateParams) {
        this.reset = function() {
            Password.resetPassword($stateParams.token, { 'password': this.password })
                .then(function(token) {
                    showDefaultToast('Password has been updated');
                    $auth.setToken(token, true);
                })
                .catch(function(response) {
                    showDefaultToast(response.data.message);
                });
        };

        function showDefaultToast(message) {
            $mdToast.show(
                $mdToast.simple()
                    .content(message)
                    .position('bottom right')
                    .hideDelay(3000)
            );
        }
    });
