(function() {
    'use strict';

    angular
        .module('antidote')
        .directive('drugsReviewView', drugsReviewView);

    /** @ngInject */
    function drugsReviewView() {
        return {
            restrict: 'E',
            scope: {
                review: '=review'
            },
            templateUrl: 'app/drugs/drugs.review.view.html'
        };
    }
})();
