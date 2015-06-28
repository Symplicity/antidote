(function() {
    'use strict';

    angular
        .module('antidote')
        .directive('drugsReviewRating', drugsReviewRating);

    /** @ngInject */
    function drugsReviewRating() {
        return {
            restrict: 'E',
            scope: {
                rating: '=rating'
            },
            templateUrl: 'app/drugs/drugs.review.rating.html'
        };
    }
})();
