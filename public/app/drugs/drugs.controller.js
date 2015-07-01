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
        .controller('DrugsReviewVoteCtrl', DrugsReviewVoteCtrl)
        .controller('DrugsSignupModalCtrl', DrugsSignupModalCtrl);

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
    function DrugsViewCtrl(DrugsService, $stateParams, $mdDialog, $auth, $state) {
        var that = this;

        activate();

        this.tabs = [
            {title: 'Overview', state: 'drugs.view.overview'},
            {title: 'Reviews', state: 'drugs.view.reviews'},
            {title: 'Alternatives', state: 'drugs.view.alternatives'}
        ];

        switch ($state.current.name) {
            case 'drugs.view.overview':
                this.selectedIndex = 0;
                break;
            case 'drugs.view.reviews':
                this.selectedIndex = 1;
                break;
            case 'drugs.view.alternatives':
                this.selectedIndex = 2;
                break;
            default:
                this.selectedIndex = 0;
        }

        this.updateReviewTabState = function() {
            this.selectedIndex = 1;
        };

        this.openSignupModal = function() {
            $mdDialog.show({
                controller: 'DrugsSignupModalCtrl',
                controllerAs: 'drugsSignupModal',
                templateUrl: 'app/drugs/drugs.signup.modal.html',
                clickOutsideToClose: true,
                hasBackdrop: true
            });
        };

        this.openReviewModal = function(ev) {
            if ($auth.isAuthenticated()) {
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
            } else {
                that.openSignupModal();
            }
        };

        function activate() {
            DrugsService.get({id: $stateParams.id}).$promise.then(function(drug) {
                that.drug = drug;

                var covered = drug.insurance_coverage_percentage * 100;
                var uncovered = (1 - drug.insurance_coverage_percentage) * 100;

                var effectiveness = drug.effectiveness_percentage * 100;
                var uneffectiveness = (1 - drug.effectiveness_percentage) * 100;

                that.insuranceChartData = [covered, uncovered];
                that.effectivenessChartData = [effectiveness, uneffectiveness];
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
        this.effectiveLabels = ['Effective', 'Not Effective'];
        this.effectiveColours = ['#5e35b1', '#d1c4e9'];
        this.effectiveOptions = {
            animationEasing: 'easeOutQuart',
            percentageInnerCutout: 75,
            segmentShowStroke : false
        };

        this.iLabels = ['Covered', 'Not Covered'];
        this.iColours = ['#FF9800', '#FFE0B2'];
        this.iOptions = {
            animationEasing: 'easeOutQuart',
            percentageInnerCutout: 75,
            segmentShowStroke : false
        };
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

        this.effectiveLabels = ['Effective', 'Not Effective'];
        this.effectiveColours = ['#81c784', '#c8e6c9'];
        this.effectiveOptions = {
            animationEasing: 'easeOutQuart',
            percentageInnerCutout: 80,
            segmentShowStroke : false
        };

        this.insuranceLabels = ['Covered', 'Not Covered'];
        this.insuranceColours = ['#0288D1', '#B3E5FC'];
        this.insuranceOptions = {
            animationEasing: 'easeOutQuart',
            percentageInnerCutout: 80,
            segmentShowStroke : false
        };

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
    function DrugsReviewVoteCtrl(DrugsService, $mdDialog, $auth, $mdToast) {
        var that = this;

        this.openSignupModal = function() {
            $mdDialog.show({
                controller: 'DrugsSignupModalCtrl',
                controllerAs: 'drugsSignupModal',
                templateUrl: 'app/drugs/drugs.signup.modal.html',
                clickOutsideToClose: true,
                hasBackdrop: true
            });
        };

        this.vote = function(review, vote) {
            if ($auth.isAuthenticated()) {
                DrugsService.voteOnReview(
                    {
                        'id': review.id,
                        'vote': vote
                    }
                ).$promise.then(
                    function(resp) {
                        if (vote === 1) {
                            review.upvotes++;
                            if (resp.updated_at > resp.created_at) {
                                review.downvotes--;
                            }
                        } else {
                            review.downvotes++;
                            if (resp.updated_at > resp.created_at) {
                                review.upvotes--;
                            }
                        }
                    },
                    function(resp) {
                        if (resp.status === 401) {
                            //on 401 error from server ask user to log in (prob. token expired)
                            that.openSignupModal();
                        } else if (resp.status === 400) {
                            //on 400 error user already voted so show toast
                            $mdToast.show(
                                $mdToast.simple()
                                    .content(resp.data.message)
                                    .position('top right')
                                    .hideDelay(3000)
                            );
                        }
                    }
                );
            } else {
                this.openSignupModal();
            }
        };
    }

    /** @ngInject */
    function DrugsSignupModalCtrl($mdDialog, $state) {
        this.closeDialog = function() {
            $mdDialog.hide();
        };

        this.login = function() {
            $mdDialog.hide();
            $state.go('login');
        };

        this.signup = function() {
            $mdDialog.hide();
            $state.go('signup');
        };
    }
})();
