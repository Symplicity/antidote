(function() {
    'use strict';

    angular
        .module('entic')
        .controller('NavbarCtrl', NavbarCtrl);

    /** @ngInject */
    function NavbarCtrl($auth) {
        this.isAuthenticated = function() {
            return $auth.isAuthenticated();
        };
    }
})();
