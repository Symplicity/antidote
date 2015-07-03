(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('LogoutCtrl', LogoutCtrl);

    /** @ngInject */
    function LogoutCtrl($mdToast, $auth) {
        if ($auth.isAuthenticated()) {
            $auth.logout()
                .then(function() {
                    $mdToast.showSimple('You have been logged out');
                });
        }
    }
})();
