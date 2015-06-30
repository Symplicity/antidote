(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('ForgotPasswordCtrl', ForgotPasswordCtrl);

    /** @ngInject */
    function ForgotPasswordCtrl($mdToast, Password) {
        this.forgot = function() {
            Password.forgotPassword({'username': this.username})
                .then(function(response) {
                    showDefaultToast(response.data.message);
                })
                .catch(function(response) {
                    showDefaultToast(response.data.message);
                });
        };

        function showDefaultToast(message) {
            $mdToast.show(
                $mdToast.simple()
                    .content(message)
                    .position('top right')
                    .hideDelay(3000)
            );
        }

    }
})();
