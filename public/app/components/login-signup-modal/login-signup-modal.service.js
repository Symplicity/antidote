(function() {
    'use strict';

    angular
        .module('antidote')
        .factory('LoginSignupModalService', LoginSignupModalService);

    /** @ngInject */
    function LoginSignupModalService($mdDialog) {
        return {
            open: function() {
                $mdDialog.show({
                    controller: 'LoginSignupModalCtrl',
                    controllerAs: 'loginSignupModal',
                    templateUrl: 'app/components/login-signup-modal/login-signup-modal.html',
                    clickOutsideToClose: true,
                    hasBackdrop: true
                });
            },
            close: function() {
                $mdDialog.hide();
            }
        };
    }
})();
