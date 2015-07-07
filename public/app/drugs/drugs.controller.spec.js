describe('Drugs Controller', function() {
    'use strict';

    var scope;
    var controller;

    beforeEach(function() {
        module('antidote');
        inject(function($controller, $rootScope) {
            controller = $controller;
            scope = $rootScope.$new();
        });
    });

    describe('DrugsListCtrl', function() {
        it('should call drugs service to get the list', inject(function(DrugsService) {
            var returnData = {data: [{id: 1}], next_page_url: 'http://foo.com/?page=2'};

            spyOn(DrugsService, 'query').and.returnValue({$promise: {
                then: function(callback) {
                    callback(returnData);
                }
            }});

            var vm = controller('DrugsListCtrl', {
                DrugsService: DrugsService,
                $stateParams: {'term':'foo'}
            });

            expect(DrugsService.query).toHaveBeenCalledWith(
                {
                    term: 'foo',
                    page: 1,
                    limit: 50
                }
            );

            expect(vm.drugs[0].id).toEqual(1);
        }));

        it('should call drugs service to get more drugs and append them to the list', inject(function(DrugsService) {
            var returnData = {data: [{id: 1}], next_page_url: 'http://foo.com/?page=2'};

            spyOn(DrugsService, 'query').and.returnValue({
                $promise: {
                    then: function(callback) {
                        callback(returnData);
                    }
                }
            });

            var vm = controller('DrugsListCtrl', {
                DrugsService: DrugsService,
                $stateParams: {'term': 'foo'}
            });

            expect(vm.more).toEqual(true);

            vm.showMore();

            expect(vm.page).toEqual(2);
            expect(DrugsService.query).toHaveBeenCalledWith(
                {
                    term: 'foo',
                    page: 2,
                    limit: 50
                }
            );

            expect(vm.drugs.length).toEqual(2);
        }));
    });

    describe('DrugsViewCtrl', function() {
        it('should call drugs service to get the record', inject(function(DrugsService) {
            var drug = {id: 2};

            spyOn(DrugsService, 'get').and.returnValue({
                $promise: {
                    then: function(callback) {
                        callback(drug);
                    }
                }
            });

            var vm = controller('DrugsViewCtrl', {
                $stateParams: {id: 2},
                $scope: scope
            });

            expect(vm.drug.id).toEqual(2);
        }));
    });

    describe('DrugsOverviewCtrl', function() {
        it('should call drugs service to get top 2 reviews', inject(function(DrugsService, $auth) {
            var reviews = {data: [{id: 1}, {id: 2}]};

            spyOn(DrugsService, 'getReviews').and.returnValue({
                $promise: {
                    then: function(callback) {
                        callback(reviews);
                    }
                }
            });

            var vm = controller('DrugsOverviewCtrl', {
                $stateParams: {id: 2},
                $scope: scope,
                $auth: $auth
            });

            expect(DrugsService.getReviews).toHaveBeenCalledWith(
                {
                    id: 2,
                    limit: 2,
                    user: null
                }
            );
            expect(vm.topReviews[0].id).toEqual(1);
        }));
    });

    describe('DrugsReviewsCtrl', function() {
        it('should call drugs service to get the list of reviews', inject(function(DrugsService) {
            var returnData = {data: [{id: 1}]};

            spyOn(DrugsService, 'getReviews').and.returnValue({
                $promise: {
                    then: function(callback) {
                        callback(returnData);
                    }
                }
            });

            var vm = controller('DrugsReviewsCtrl', {
                DrugsService: DrugsService,
                $stateParams: {id: 1}
            });

            expect(DrugsService.getReviews).toHaveBeenCalledWith(
                {
                    id: 1,
                    page: 1,
                    limit: 10,
                    min_age: null,
                    max_age: null,
                    gender: null,
                    user: null
                }
            );

            expect(vm.reviews[0].id).toEqual(1);
        }));

        it('should call drugs service to get more reviews and append them to existing list', inject(
            function(DrugsService) {
                var returnData = {data: [{id: 1}], next_page_url: 'http://foo.com/?page=2'};

                spyOn(DrugsService, 'getReviews').and.returnValue({
                    $promise: {
                        then: function(callback) {
                            callback(returnData);
                        }
                    }
                });

                var vm = controller('DrugsReviewsCtrl', {
                    DrugsService: DrugsService,
                    $stateParams: {id: 1}
                });

                expect(vm.page).toEqual(1);
                expect(vm.more).toEqual(true);

                vm.showMore();

                expect(vm.page).toEqual(2);
                expect(DrugsService.getReviews).toHaveBeenCalledWith(
                    {
                        id: 1,
                        page: 2,
                        limit: 10,
                        min_age: null,
                        max_age: null,
                        gender: null,
                        user: null
                    }
                );
                expect(vm.reviews.length).toEqual(2);
            }
        ));

        it('should call apply search filters to the list of reviews', inject(function(DrugsService) {
            spyOn(DrugsService, 'getReviews').and.returnValue({
                $promise: {
                    then: function() {
                    }
                }
            });

            var vm = controller('DrugsReviewsCtrl', {
                DrugsService: DrugsService,
                $stateParams: {id: 1}
            });

            expect(DrugsService.getReviews).toHaveBeenCalledWith(
                {
                    id: 1,
                    page: 1,
                    limit: 10,
                    min_age: null,
                    max_age: null,
                    gender: null,
                    user: null
                }
            );

            /* TODO: remove this workaround for this bug:  https://github.com/angular/material/issues/3243 */
            vm.onAgeRangeFilterSelected(vm.ageRanges[1]);
            vm.onGenderFilterSelected(vm.genders[1]);

            vm.onAgeRangeFilterSelected(vm.ageRanges[1]);

            expect(DrugsService.getReviews).toHaveBeenCalledWith(
                {
                    id: 1,
                    page: 1,
                    limit: 10,
                    min_age: 18,
                    max_age: 34,
                    gender: null,
                    user: null
                }
            );

            vm.onGenderFilterSelected(vm.genders[1]);

            expect(DrugsService.getReviews).toHaveBeenCalledWith(
                {
                    id: 1,
                    page: 1,
                    limit: 10,
                    min_age: 18,
                    max_age: 34,
                    gender: 'm',
                    user: null
                }
            );
        }));
    });
});
