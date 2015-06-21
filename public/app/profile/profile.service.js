'use strict';

angular.module('entic')
    .factory('ProfileService', function ProfileService($resource) {
        return $resource('/api/users/me',
            {},
            {
                update: {
                    method: 'PUT'
                }
            }
        );
    });
