(function() {
    'use strict';

    angular
        .module('antidote')
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
                },
                getReviews: {
                    method: 'GET',
                    url: '/api/drugs/:id/reviews'
                },
                postReview: {
                    method: 'POST',
                    url: '/api/drugs/:id/reviews'
                },
                query: {
                    method: 'GET'
                }
            }
        );
    }
})();
