(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('DrugsListCtrl', DrugsListCtrl)
        .controller('DrugsViewCtrl', DrugsViewCtrl)
        .controller('DrugsOverviewCtrl', DrugsOverviewCtrl)
        .controller('DrugsReviewsCtrl', DrugsReviewsCtrl)
        .controller('DrugsAlternativesCtrl', DrugsAlternativesCtrl)
        .controller('DrugsReviewCtrl', DrugsReviewCtrl)
        .controller('DrugsReviewModalCtrl', DrugsReviewModalCtrl);

    /** @ngInject */
    function DrugsListCtrl(DrugsService, $stateParams) {
        var that = this;
        this.letters = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
            'q', 'r', 's',
            't', 'u', 'v',
            'w', 'x', 'y',
            'z'
        ];

        activate();

        function activate() {
            DrugsService.query($stateParams).$promise.then(function(drugs) {
                that.drugs = drugs;
            });
        }

        this.getAlphabetFilterClass = function(alphabet) {
            if ($stateParams.alpha === alphabet) {
                return 'active';
            } else {
                return '';
            }
        };
        this.getAlphabetLetter = function() {
            return $stateParams.alpha;
        };
    }

    /** @ngInject */
    function DrugsViewCtrl(DrugsService, $stateParams, $mdDialog) {
        var that = this;
        activate();

        this.tabs = [
            {title: 'Overview', state: 'drugs.view.overview'},
            {title: 'Reviews', state: 'drugs.view.reviews'},
            {title: 'Alternatives', state: 'drugs.view.alternatives'}
        ];

        this.openReviewModal = function(ev) {
            $mdDialog.show({
                controller: 'DrugsReviewModalCtrl',
                controllerAs: 'drugsReviewModal',
                templateUrl: '/app/drugs/drugs.review.modal.html',
                targetEvent: ev,
                clickOutsideToClose: true,
                hasBackdrop: true
            });
        };

        function activate() {
            DrugsService.get({id: $stateParams.id}).$promise.then(function(drug) {
                that.drug = drug;
            });
        }
    }

    /** @ngInject */
    function DrugsReviewModalCtrl($mdDialog) {
        this.closeDialog = function() {
            $mdDialog.hide();
        };
    }

    /** controller for review form **/
    /** @ngInject */
    function DrugsReviewCtrl(DrugsService, $stateParams) {
        var that = this;
        this.review = {};
        this.reviewSubmitted = false;

        this.submitReview = function() {
            DrugsService.postReview({id: $stateParams.id}, this.review).$promise.then(function() {
                that.reviewSubmitted = true;
            });
            //TODO: add server error handling to display messages to user
        };
    }

    /** @ngInject */
    function DrugsOverviewCtrl() {
        activate();

        this.labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July'];
        this.series = ['Series A', 'Series B'];
        this.data = [
            [65, 59, 80, 81, 56, 55, 40],
            [28, 48, 40, 19, 86, 27, 90]
        ];

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
