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
            var returnData = {data: [{id: 1}], last_page: 2};

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
                    limit: 300
                }
            );

            expect(vm.drugs[0].id).toEqual(1);
        }));

        it('should call drugs service to get more drugs and append them to the list', inject(function(DrugsService) {
            var returnData = {data: [{id: 1}], last_page: 2};

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
                    limit: 300
                }
            );

            expect(vm.more).toEqual(false);
            expect(vm.drugs.length).toEqual(2);
        }));
    });

    describe('DrugsViewCtrl', function() {
        it('should call drugs service to get the record and top 2 reviews', inject(function(DrugsService) {
            var drug = {id: 2};
            var reviews = {data: [{id: 1}, {id: 2}]};

            spyOn(DrugsService, 'get').and.returnValue({
                $promise: {
                    then: function(callback) {
                        callback(drug);
                    }
                }
            });

            spyOn(DrugsService, 'getReviews').and.returnValue({
                $promise: {
                    then: function(callback) {
                        callback(reviews);
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
                    limit: 10
                }
            );

            expect(vm.reviews[0].id).toEqual(1);
        }));

        it('should call drugs service to get more reviews and append them to existing list', inject(
            function(DrugsService) {
                var returnData = {data: [{id: 1}], last_page: 2};

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
                        limit: 10
                    }
                );
                expect(vm.more).toEqual(false);
                expect(vm.reviews.length).toEqual(2);
            }
        ));
    });
});
