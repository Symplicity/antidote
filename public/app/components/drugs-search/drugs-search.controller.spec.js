describe('Drugs Search Controller', function() {
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

    describe('DrugsSearchCtrl', function() {
        it('should call drugs service to get autocomplete matches', inject(function(DrugsService) {
            spyOn(DrugsService, 'queryAutocomplete').and.returnValue({
                $promise: {
                    then: function() {
                    }
                }
            });

            var vm = controller('DrugsSearchCtrl', {
                DrugsService: DrugsService
            });

            vm.getMatches('foo');

            expect(DrugsService.queryAutocomplete).toHaveBeenCalledWith({'term': 'foo'});
        }));

        it('should navigate to drugs view when a match is selected', inject(function($state) {
            spyOn($state, 'go');
            var item = {id: 1};

            var vm = controller('DrugsSearchCtrl', {
                $state: $state
            });

            vm.itemChange(item);
            expect($state.go).toHaveBeenCalledWith('drugs.view.overview', {'id': 1});
        }));
    });
});
