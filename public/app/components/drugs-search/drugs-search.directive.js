(function() {
    'use strict';

    angular
        .module('antidote')
        .directive('drugsSearch', drugsSearch);

    /** @ngInject */
    function drugsSearch() {
        return {
            restrict: 'E',
            templateUrl: '/app/components/drugs-search/drugs-search.html',
            controller: 'DrugsSearchCtrl as drugsSearch',
            link: function() {

            }
        };
    }
})();
