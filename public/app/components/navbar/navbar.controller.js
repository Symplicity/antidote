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

        $scope.openMenu = function(){
            $mdSidenav('md-mobile-menu').toggle();
        };
    }
})();
