(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('LogoutCtrl', LogoutCtrl);

    /** @ngInject */
    function LogoutCtrl($mdToast, $auth) {
        if (!$auth.isAuthenticated()) {
            return;
        }
        $auth.logout()
            .then(function() {
                $mdToast.show(
                    $mdToast.simple()
                        .content('You have been logged out')
                        .position('top left')
                        .hideDelay(3000)
                );
            });
    }
})();
