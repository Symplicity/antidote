(function() {
    'use strict';

    var httpBackend;

    beforeEach(function() {
        module('antidote');
        inject(function($httpBackend) {
            httpBackend = $httpBackend;
        });
    });

    afterEach(function() {
        httpBackend.verifyNoOutstandingExpectation();
        httpBackend.verifyNoOutstandingRequest();
    });

    describe('DrugsListCtrl', function() {
        it('should show call drugs service to get the list', inject(function($controller) {
            var returnData = [{id: 1}];
            httpBackend.expectGET('/api/autocomplete/drugs').respond(returnData);

            var vm = $controller('DrugsListCtrl');

            httpBackend.flush();
            expect(vm.drugs[0].id).toEqual(1);
        }));
    });

    describe('DrugsViewCtrl', function() {
        it('should show call drugs service to get the record', inject(function($controller, $rootScope) {
            var returnData = {id: 2};
            httpBackend.expectGET('/api/drugs/2').respond(returnData);

            var scope = $rootScope.$new();
            var vm = $controller('DrugsViewCtrl', {
                $stateParams: {id: 2},
                $scope: scope
            });

            httpBackend.flush();
            expect(vm.drug.id).toEqual(2);
        }));
    });
})();
