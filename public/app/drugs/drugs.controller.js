(function() {
    'use strict';

    angular
        .module('entic')
        .controller('DrugsViewCtrl', DrugsViewCtrl);

    /** @ngInject */
    function DrugsViewCtrl(DrugsService, $stateParams) {
        var that = this;
        activate();

        function activate() {
            DrugsService.get({id: $stateParams.id}).$promise.then(function(drug) {
                that.drug = drug;
            });
        }
    }
})();
