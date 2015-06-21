(function() {
    'use strict';

    angular
        .module('entic')
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
                        .position('bottom right')
                        .hideDelay(3000)
                );
            });
    }
})();
