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
        var self = this;
        this.drugs = [];

        this.letters = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g',
            'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
            'q', 'r', 's',
            't', 'u', 'v',
            'w', 'x', 'y',
            'z'
        ];

        function activate() {
            self.loadData();
        }

        this.loadData = function() {
            DrugsService.query(
                {
                    term: $stateParams.term,
                    page: self.page,
                    limit: self.perPage
                }
            ).$promise.then(function(drugs) {
                    self.drugs = self.drugs.concat(drugs.data);
                    self.more = self.page < drugs.last_page;
                });
        };

        this.getAlphabetFilterClass = function(alphabet) {
            if ($stateParams.term === alphabet) {
                return 'active';
            } else {
                return '';
            }
        };

        /** PAGINATION **/
        this.perPage = 50;
        this.page = 1;
        this.more = true;

        this.showMore = function() {
            self.page++;
            self.loadData();
        };

        this.hasMore = function() {
            return self.more;
        };

        activate();
    }

    /** @ngInject */
    function DrugsViewCtrl(DrugsService, $stateParams, SignupModalService, $auth, $mdDialog, $state) {
        var self = this;

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
            SignupModalService.open();
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
                        drug_side_effects: self.drug.side_effects
                    }
                });
            } else {
                self.openSignupModal();
            }
        };

        function activate() {
            DrugsService.get({id: $stateParams.id}).$promise.then(function(drug) {
                self.drug = drug;

                var covered = drug.insurance_coverage_percentage * 100;
                var uncovered = (1 - drug.insurance_coverage_percentage) * 100;

                var effectiveness = drug.effectiveness_percentage * 100;
                var uneffectiveness = (1 - drug.effectiveness_percentage) * 100;

                self.insuranceChartData = [covered, uncovered];
                self.effectivenessChartData = [effectiveness, uneffectiveness];
            });

            DrugsService.getReviews({id: $stateParams.id, limit: 2}).$promise.then(function(reviews) {
                self.topReviews = reviews.data;
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
        var self = this;
        this.review = {
            side_effects: []
        };
        this.reviewSubmitted = false;

        this.submitReview = function() {
            DrugsService.postReview({id: $stateParams.id}, this.review).$promise.then(function() {
                self.reviewSubmitted = true;
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
        this.reviews = [];
        var self = this;

        function activate() {
            self.loadData();
        }

        this.loadData = function() {
            DrugsService.getReviews(
                {
                    id: $stateParams.id,
                    page: self.page,
                    limit: self.perPage
                }
            ).$promise.then(function(reviews) {
                    self.reviews = self.reviews.concat(reviews.data);
                    self.more = self.page < reviews.last_page;
                });
        };

        /** PAGINATION **/
        this.perPage = 10;
        this.page = 1;
        this.more = true;

        this.showMore = function() {
            self.page++;
            self.loadData();
        };

        this.hasMore = function() {
            return self.more;
        };

        activate();
    }

    /** @ngInject */
    function DrugsAlternativesCtrl(DrugsService, $stateParams) {
        this.alternatives = {};
        var self = this;

        this.effectiveLabels = ['Effective', 'Not Effective'];
        this.effectiveColours = ['#5e35b1', '#d1c4e9'];
        this.effectiveOptions = {
            animationEasing: 'easeOutQuart',
            percentageInnerCutout: 80,
            segmentShowStroke : false
        };

        this.insuranceLabels = ['Covered', 'Not Covered'];
        this.insuranceColours = ['#FF9800', '#FFE0B2'];
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

                self.alternatives = alternatives;
            });
        }
    }

    /** @ngInject */
    function DrugsReviewVoteCtrl(DrugsService, $auth, $mdToast, SignupModalService) {
        var self = this;

        this.openSignupModal = function() {
            SignupModalService.open();
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
                            self.openSignupModal();
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
                self.openSignupModal();
            }
        };
    }
})();
