(function() {
    'use strict';

    angular
        .module('entic')
        .controller('DrugsListCtrl', DrugsListCtrl)
        .controller('DrugsViewCtrl', DrugsViewCtrl)
        .controller('DrugsOverviewCtrl', DrugsOverviewCtrl)
        .controller('DrugsReviewsCtrl', DrugsReviewsCtrl)
        .controller('DrugsAlternativesCtrl', DrugsAlternativesCtrl);

    /** @ngInject */
    function DrugsListCtrl(DrugsService) {
        var that = this;
        activate();

        function activate() {
            DrugsService.query().$promise.then(function(drugs) {
                that.drugs = drugs;
            });
        }
    }

    /** @ngInject */
    function DrugsViewCtrl(DrugsService, $stateParams) {
        var that = this;
        activate();

        this.tabs = [
            {title: 'Overview', state: 'drugs.view.overview'},
            {title: 'Reviews', state: 'drugs.view.reviews'},
            {title: 'Alternative', state: 'drugs.view.alternatives'}
        ];

        function activate() {
            DrugsService.get({id: $stateParams.id}).$promise.then(function(drug) {
                that.drug = drug;
            });
        }
    }

    /** @ngInject */
    function DrugsOverviewCtrl() {
        activate();

        function activate() {
            //TODO: add API service call here
        }
    }

    /** @ngInject */
    function DrugsReviewsCtrl() {
        activate();

        function activate() {
            //TODO: add API service call here
        }
    }

    /** @ngInject */
    function DrugsAlternativesCtrl() {
        activate();

        function activate() {
            //TODO: add API service call here
        }
    }
})();
