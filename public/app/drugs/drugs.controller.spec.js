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
            var returnData = [{id: 1}];

            spyOn(DrugsService, 'queryAutocomplete').and.returnValue({$promise: {
                then: function(callback) {
                    callback(returnData);
                }
            }});

            var vm = controller('DrugsListCtrl', {
                DrugsService: DrugsService,
                $stateParams: {}
            });

            expect(vm.drugs[0].id).toEqual(1);
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
});
