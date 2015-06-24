(function() {
    'use strict';

    angular
        .module('antidote')
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
