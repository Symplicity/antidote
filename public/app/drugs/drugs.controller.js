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
            'z', '#'
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

        this.currentLetter = $stateParams.term;

        /** PAGINATION **/
        this.perPage = 50;
        this.page = 1;
        this.more = false;

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
    function DrugsViewCtrl(DrugsService, $stateParams, LoginSignupModalService, $auth, $mdDialog, $state) {
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
                LoginSignupModalService.open();
            }
        };

        this.numLimit = 200;
        this.readBtn = 'read more';
        this.readMore = function() {
            if (this.numLimit === 200) {
                this.numLimit = 10000;
                this.readBtn = 'read less';
            }else {
                this.numLimit = 200;
                this.readBtn = 'read more';
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
    function DrugsReviewCtrl(DrugsService, $stateParams, ServerErrorHandlerService) {
        var self = this;
        this.review = {
            side_effects: []
        };
        this.reviewSubmitted = false;

        this.submitReview = function() {
            DrugsService.postReview({id: $stateParams.id}, this.review).$promise.then(function() {
                self.reviewSubmitted = true;
            }, ServerErrorHandlerService.handle);
        };
    }

    /** @ngInject */
    function DrugsOverviewCtrl(DrugsService, $stateParams) {
        var self = this;

        this.topReviews = [];

        this.effectiveLabels = ['Effective', 'Not Effective'];
        this.effectiveColours = ['#5e35b1', '#d1c4e9'];
        this.effectiveOptions = {
            animationEasing: 'easeOutQuart',
            percentageInnerCutout: 85,
            segmentShowStroke : false,
            responsive: false
        };

        this.iLabels = ['Covered', 'Not Covered'];
        this.iColours = ['#FF9800', '#FFE0B2'];
        this.iOptions = {
            animationEasing: 'easeOutQuart',
            percentageInnerCutout: 85,
            segmentShowStroke : false,
            responsive: false
        };

        function activate() {
            DrugsService.getReviews({id: $stateParams.id, limit: 2}).$promise.then(function(reviews) {
                self.topReviews = reviews.data;
            });
        }

        activate();
    }

    /** @ngInject */
    function DrugsReviewsCtrl(DrugsService, $stateParams, $auth, ProfileService) {
        this.reviews = [];
        var self = this;

        function activate() {
            if (!$auth.isAuthenticated()) {
                //load with default filters
                self.applyFilters();
            } else {
                ProfileService.get().$promise.then(function(user) {
                    if (user.age || user.gender) {
                        if (user.age) {
                            for (var i = 0; i < self.ageRanges.length; i++) {
                                var ageRange = self.ageRanges[i];
                                if (user.age > ageRange.min_value && user.age < ageRange.max_value) {
                                    self.selectedAgeRangeTabIndex = i;
                                    break;
                                }
                            }
                        }

                        if (user.gender) {
                            for (var j = 0; j < self.genders.length; j++) {
                                var gender = self.genders[j];
                                if (user.gender === gender.value) {
                                    self.selectedGenderTabIndex = j;
                                    break;
                                }
                            }
                        }
                    } else {
                        //load with default filters
                        self.applyFilters();
                    }
                });
            }
        }

        this.loadData = function(append) {
            append = (typeof append !== 'undefined') ? append : true;

            DrugsService.getReviews(
                {
                    id: $stateParams.id,
                    page: self.page,
                    limit: self.perPage,
                    min_age: self.filters.ageRange.min_value,
                    max_age: self.filters.ageRange.max_value,
                    gender: self.filters.gender.value
                }
            ).$promise.then(function(reviews) {
                    if (append) {
                        self.reviews = self.reviews.concat(reviews.data);
                    } else {
                        self.reviews = reviews.data;
                    }
                    self.more = self.page < reviews.last_page;
                });
        };

        /** FILTERS **/
        this.ageRanges = [
            {
                'min_value': null,
                'max_value': null,
                'label': 'All Ages'
            },
            {
                'min_value': 18,
                'max_value': 34,
                'label': '18-34'
            },
            {
                'min_value': 35,
                'max_value': 50,
                'label': '35-50'
            },
            {
                'min_value': 51,
                'max_value': 125,
                'label': '51+'
            }
        ];

        this.genders = [
            {
                'value': null,
                'label': 'All Genders'
            },
            {
                'value': 'm',
                'label': 'Male'
            },
            {
                'value': 'f',
                'label': 'Female'
            }
        ];

        this.filters = {
            ageRange: self.ageRanges[0],
            gender: self.genders[0]
        };

        /* TODO: remove this workaround for this bug:  https://github.com/angular/material/issues/3243 */
        var onAgeRangeFilterSelectedEventFiredCount = 0;
        var onGenderFilterSelectedEventFiredCount = 0;

        this.onAgeRangeFilterSelected = function(ageRange) {
            onAgeRangeFilterSelectedEventFiredCount++;
            //ignore first event
            if (onAgeRangeFilterSelectedEventFiredCount > 1) {
                this.filters.ageRange = ageRange;
                this.applyFilters();
            }
        };

        this.onGenderFilterSelected = function(gender) {
            onGenderFilterSelectedEventFiredCount++;
            //ignore first event
            if (onGenderFilterSelectedEventFiredCount > 1) {
                this.filters.gender = gender;
                this.applyFilters();
            }
        };

        this.applyFilters = function() {
            this.resetPagination();
            this.loadData(false);
        };

        /** PAGINATION **/
        this.resetPagination = function() {
            this.perPage = 10;
            this.page = 1;
            this.more = false;
        };

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
            percentageInnerCutout: 85,
            segmentShowStroke : false,
            responsive: false
        };

        this.insuranceLabels = ['Covered', 'Not Covered'];
        this.insuranceColours = ['#FF9800', '#FFE0B2'];
        this.insuranceOptions = {
            animationEasing: 'easeOutQuart',
            percentageInnerCutout: 85,
            segmentShowStroke : false,
            responsive: false
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
    function DrugsReviewVoteCtrl(DrugsService, $auth, $mdToast, LoginSignupModalService, ServerErrorHandlerService) {
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
                    ServerErrorHandlerService.handle
                );
            } else {
                LoginSignupModalService.open();
            }
        };
    }
})();
