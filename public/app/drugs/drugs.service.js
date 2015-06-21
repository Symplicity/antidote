'use strict';

angular.module('entic')
    .factory('DrugsService', function DrugsService($resource) {
        return $resource('/api/drugs/:id',
            {
                id: '@id'
            },
            {
                update: {
                    method: 'PUT'
                }
            }
        );
    });
