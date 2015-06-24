(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('ForgotPasswordCtrl', ForgotPasswordCtrl);

    /** @ngInject */
    function ForgotPasswordCtrl($mdToast, Password) {
        this.forgot = function() {
            Password.forgotPassword({'email': this.email})
                .then(function() {
                    showDefaultToast('Email has been sent');
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

    }
})();
