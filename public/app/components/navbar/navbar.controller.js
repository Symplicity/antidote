'use strict';

angular.module('entic')
    .controller('NavbarCtrl', function($auth) {
        this.isAuthenticated = function() {
            return $auth.isAuthenticated();
        };
    });
