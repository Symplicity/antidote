(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('DrugsListCtrl', DrugsListCtrl)
        .controller('DrugsViewCtrl', DrugsViewCtrl)
        .controller('DrugsOverviewCtrl', DrugsOverviewCtrl)
        .controller('DrugsReviewsCtrl', DrugsReviewsCtrl)
        .controller('DrugsAlternativesCtrl', DrugsAlternativesCtrl);

    /** @ngInject */
    function DrugsListCtrl(DrugsService, $stateParams) {
        var that = this;
        this.letters=['a','b','c','d','e','f','g','h','i','j','k','l','m','n','p','q','r','s','t','u','v','w','x','y','z'];

        activate();

        function activate() {
            DrugsService.query($stateParams).$promise.then(function(drugs) {
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
    function DrugsReviewsCtrl(DrugsService, $stateParams) {
        this.reviews = {};
        var that = this;

        activate();

        function activate() {
            DrugsService.getReviews({id: $stateParams.id}).$promise.then(function(reviews) {
                that.reviews = reviews.data;
            });
        }
    }

    /** @ngInject */
    function DrugsAlternativesCtrl(DrugsService, $stateParams) {
        this.alternatives = {};
        var that = this;

        activate();

        function activate() {
            DrugsService.getAlternatives({id: $stateParams.id}).$promise.then(function(alternatives) {
                that.alternatives = alternatives;
            });
        }
    }
})();
