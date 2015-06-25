(function() {
    'use strict';

    angular
        .module('antidote')
        .factory('Password', Password);

    /** @ngInject */
    function Password($http) {
        return {
            forgotPassword: function(usernameData) {
                return $http.post('/api/auth/forgot', usernameData);
            },
            resetPassword: function(token, passwordData) {
                return $http.post('/api/auth/reset/' + token, passwordData);
            }
        };
    }
})();
