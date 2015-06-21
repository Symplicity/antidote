(function() {
    'use strict';

    angular
        .module('entic')
        .factory('ProfileService', ProfileService);

    /** @ngInject */
    function ProfileService($resource) {
        return $resource('/api/users/me',
            {},
            {
                update: {
                    method: 'PUT'
                }
            }
        );
    }
})();
