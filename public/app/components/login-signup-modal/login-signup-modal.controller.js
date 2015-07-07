(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('LoginSignupModalCtrl', LoginSignupModalCtrl);

    /** @ngInject */
    function LoginSignupModalCtrl(LoginSignupModalService) {
        this.showPasswordForgotForm = false;
        this.signup = false;

        this.closeDialog = function() {
            LoginSignupModalService.close();
        };
    }
})();
