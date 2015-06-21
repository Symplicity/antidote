(function() {
    'use strict';

    angular
        .module('entic')
        .factory('DrugsService', DrugsService);

    /** @ngInject */
    function DrugsService($resource) {
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
    }
})();
