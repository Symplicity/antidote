(function() {
    'use strict';

    angular
        .module('entic')
        .factory('Password', Password);

    /** @ngInject */
    function Password($http) {
        return {
            forgotPassword: function(emailData) {
                return $http.post('/api/auth/forgot', emailData);
            },
            resetPassword: function(token, passwordData) {
                return $http.post('/api/auth/reset/' + token, passwordData);
            }
        };
    }
})();
