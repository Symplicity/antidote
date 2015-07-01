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
        .controller('DrugsReviewModalCtrl', DrugsReviewModalCtrl)
        .controller('DrugsReviewVoteCtrl', DrugsReviewVoteCtrl);

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
            DrugsService.queryAutocomplete($stateParams).$promise.then(function(drugs) {
                that.drugs = drugs;
            });
        }

        this.getAlphabetFilterClass = function(alphabet) {
            if ($stateParams.term === alphabet) {
                return 'active';
            } else {
                return '';
            }
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
                bindToController: true,
                templateUrl: 'app/drugs/drugs.review.modal.html',
                targetEvent: ev,
                clickOutsideToClose: true,
                hasBackdrop: true,
                locals: {
                    drug_side_effects: that.drug.side_effects
                }
            });
        };

        function activate() {
            DrugsService.get({id: $stateParams.id}).$promise.then(function(drug) {
                that.drug = drug;

                var covered = drug.insurance_coverage_percentage * 100;
                var uncovered = (1 - drug.insurance_coverage_percentage) * 100;

                var effectiveness = drug.effectiveness_percentage * 100;
                var uneffectiveness = (1 - drug.effectiveness_percentage) * 100;

                var sideEffects = ['60', '30', '10'];

                that.insuranceChartData = [covered, uncovered];
                that.effectivenessChartData = [effectiveness, uneffectiveness];
                that.sideEffectsData = sideEffects;
            });

            DrugsService.getReviews({id: $stateParams.id, limit: 2}).$promise.then(function(reviews) {
                that.topReviews = reviews.data;
            });
        }
    }

    /** @ngInject */
    function DrugsReviewModalCtrl($mdDialog) {
        this.closeDialog = function() {
            $mdDialog.hide();
        };

        this.toggle = function(item, list) {
            var idx = list.indexOf(item);
            if (idx > -1) {
                list.splice(idx, 1);
            } else {
                list.push(item);
            }
        };

        this.exists = function(item, list) {
            return list.indexOf(item) > -1;
        };
    }

    /** controller for review form **/
    /** @ngInject */
    function DrugsReviewCtrl(DrugsService, $stateParams) {
        var that = this;
        this.review = {
            side_effects: []
        };
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

        this.effectiveLabels = ['Effective', 'Not Effective'];
        this.effectiveColours = ['#673AB7', '#D1C4E9'];

        this.seLabels = ['Spleen Explosion', 'Headache', 'Nausea'];
        this.seColours = ['#4CAF50', '#81C784', '#E8F5E9'];

        this.iLabels = ['Covered', 'Not Covered'];
        this.iColours = ['#FF5722', '#FFCCBC'];

        function activate() {
            //TODO: add API service call here
        }
    }

    /** @ngInject */
    function DrugsReviewsCtrl(DrugsService, $stateParams, $mdDialog) {
        this.reviews = {};
        var that = this;

        this.openSignupModal = function(ev) {
            $mdDialog.show({
                controller: 'DrugsReviewModalCtrl',
                controllerAs: 'drugsReviewModal',
                templateUrl: 'app/drugs/drugs.signup.modal.html',
                targetEvent: ev,
                clickOutsideToClose: true,
                hasBackdrop: true
            });
        };

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

        this.effectiveLabels = ['Effective', 'Not Effective'];
        this.effectiveColours = ['#673AB7', '#D1C4E9'];

        this.insuranceLabels = ['Covered', 'Not Covered'];
        this.insuranceColours = ['#FF5722', '#FFCCBC'];

        activate();

        function activate() {
            DrugsService.getAlternatives({id: $stateParams.id}).$promise.then(function(alternatives) {
                angular.forEach(alternatives.data, function(alternative) {

                    var covered = alternative.insurance_coverage_percentage * 100;
                    var uncovered = (1 - alternative.insurance_coverage_percentage) * 100;

                    var effectiveness = alternative.effectiveness_percentage * 100;
                    var uneffectiveness = (1 - alternative.effectiveness_percentage) * 100;

                    alternative.chartData = {
                        'insuranceChartData' : [covered, uncovered],
                        'effectivenessChartData' : [effectiveness, uneffectiveness]
                    };
                });

                that.alternatives = alternatives;
            });
        }
    }

    /** @ngInject */
    function DrugsReviewVoteCtrl(DrugsService) {
        this.vote = function(review, vote) {
            DrugsService.voteOnReview(
                {
                    'id': review.id,
                    'vote': vote
                }
            ).$promise.then(function() {
                if (vote === 1) {
                    review.upvotes++;
                } else {
                    review.downvotes++;
                }
            });
        };
    }
})();
