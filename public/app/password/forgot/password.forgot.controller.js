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
                    $mdToast.showSimple(response.data.message);
                })
                .catch(function(response) {
                    $mdToast.showSimple(response.data.message);
                });
        };
    }
})();
