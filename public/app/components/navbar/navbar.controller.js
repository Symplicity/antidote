(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('NavbarCtrl', NavbarCtrl);

    /** @ngInject */
    function NavbarCtrl($auth) {
        this.search = {};
        this.search.keywords = '';

        this.isAuthenticated = function() {
            return $auth.isAuthenticated();
        };
    }
})();
