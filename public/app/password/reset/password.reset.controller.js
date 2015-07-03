(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('ResetPasswordCtrl', ResetPasswordCtrl);

    /** @ngInject */
    function ResetPasswordCtrl($auth, $mdToast, Password, $stateParams) {
        this.reset = function() {
            Password.resetPassword($stateParams.token, {'password': this.password})
                .then(function(token) {
                    $mdToast.showSimple('Password has been updated');
                    $auth.setToken(token, true);
                })
                .catch(function(response) {
                    $mdToast.showSimple(response.data.message);
                });
        };
    }
})();
