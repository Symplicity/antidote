(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('LogoutCtrl', LogoutCtrl);

    /** @ngInject */
    function LogoutCtrl($mdToast, $auth, $state) {
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

                $state.go('home');
            });
    }
})();
