(function() {
    'use strict';

    describe('controllers', function() {

        beforeEach(module('entic'));

        it('should show a list of drugs when i search', inject(function($controller) {
            var vm = $controller('DrugsCtrl');

            expect(angular.isArray(vm.drugs)).toBeTruthy();
            expect(vm.drugs.length > 5).toBeTruthy();
        }));
    });
})();
