describe('Signup Modal Controller', function() {
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

    describe('LoginSignupModalCtrl', function() {
        it('should close the login signup dialog when user dismisses it', inject(function(LoginSignupModalService) {
            spyOn(LoginSignupModalService, 'close');

            var vm = controller('LoginSignupModalCtrl', {
                LoginSignupModalService: LoginSignupModalService
            });

            expect(vm.closeDialog).toBeDefined();
            vm.closeDialog();

            expect(LoginSignupModalService.close).toHaveBeenCalled();
        }));
    });
});
