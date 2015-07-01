(function() {
    'use strict';

    angular
        .module('antidote')
        .factory('SignupModalService', SignupModalService);

    /** @ngInject */
    function SignupModalService($mdDialog) {
        return {
            open: function() {
                $mdDialog.show({
                    controller: 'SignupModalCtrl',
                    controllerAs: 'signupModal',
                    templateUrl: 'app/components/signup-modal/signup-modal.html',
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
