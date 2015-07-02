(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('DrugsSearchCtrl', DrugsSearchCtrl);

    /** @ngInject */
    function DrugsSearchCtrl(DrugsService, $state) {
        this.search = {};

        this.getMatches = function(searchText) {
            return DrugsService.queryAutocomplete({
                'term': searchText
            }).$promise.then(function(resp) {
                    return resp;
                });
        };

        this.textChange = function() {

        };

        this.itemChange = function(item) {
            $state.go('drugs.view.overview', {'id': item.id});
        };
    }
})();
