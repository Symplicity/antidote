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
            var returnData = {data: [{id: 1}]};
            httpBackend.expectGET('/api/drugs').respond(returnData);

            var vm = $controller('DrugsListCtrl');

            httpBackend.flush();
            expect(vm.drugs.data[0].id).toEqual(1);
        }));
    });

    describe('DrugsViewCtrl', function() {
        it('should show call drugs service to get the record', inject(function($controller) {
            var returnData = {id: 2};
            httpBackend.expectGET('/api/drugs/2').respond(returnData);

            var vm = $controller('DrugsViewCtrl', {$stateParams: {id: 2}});

            httpBackend.flush();
            expect(vm.drug.id).toEqual(2);
        }));
    });
})();
