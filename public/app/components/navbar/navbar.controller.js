(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('NavbarCtrl', NavbarCtrl);

    /** @ngInject */
    function NavbarCtrl($auth, $scope, $mdSidenav) {
        this.search = {};
        this.search.keywords = '';

        this.isAuthenticated = function() {
            return $auth.isAuthenticated();
        };

        this.openMenu = function() {
            $mdSidenav('md-mobile-menu').toggle();
        };

        $scope.$on('$stateChangeSuccess', function() {
            $mdSidenav('md-mobile-menu').close();
        });
    }
})();
