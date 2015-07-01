(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('SignupModalCtrl', SignupModalCtrl);

    /** @ngInject */
    function SignupModalCtrl(SignupModalService, $state) {
        this.closeDialog = function() {
            SignupModalService.close();
        };

        this.login = function() {
            SignupModalService.close();
            $state.go('login');
        };

        this.signup = function() {
            SignupModalService.close();
            $state.go('signup');
        };
    }
})();
