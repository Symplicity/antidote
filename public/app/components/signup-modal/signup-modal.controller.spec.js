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

    describe('SignupModalCtrl', function() {
        it('should close the signup dialog when user dismisses it', inject(function(SignupModalService) {
            spyOn(SignupModalService, 'close');

            var vm = controller('SignupModalCtrl', {
                SignupModalService: SignupModalService
            });

            expect(vm.closeDialog).toBeDefined();
            vm.closeDialog();

            expect(SignupModalService.close).toHaveBeenCalled();
        }));

        it('should navigate the user to the login page', inject(function(SignupModalService, $state) {
            spyOn(SignupModalService, 'close');
            spyOn($state, 'go');

            var vm = controller('SignupModalCtrl', {
                SignupModalService: SignupModalService,
                $state: $state
            });

            expect(vm.login).toBeDefined();
            vm.login();

            expect(SignupModalService.close).toHaveBeenCalled();
            expect($state.go).toHaveBeenCalledWith('login');

            expect(vm.signup).toBeDefined();
            vm.signup();

            expect(SignupModalService.close).toHaveBeenCalled();
            expect($state.go).toHaveBeenCalledWith('signup');
        }));

        it('should navigate the user to the signup page', inject(function(SignupModalService, $state) {
            spyOn(SignupModalService, 'close');
            spyOn($state, 'go');

            var vm = controller('SignupModalCtrl', {
                SignupModalService: SignupModalService,
                $state: $state
            });

            expect(vm.signup).toBeDefined();
            vm.signup();

            expect(SignupModalService.close).toHaveBeenCalled();
            expect($state.go).toHaveBeenCalledWith('signup');
        }));
    });
});
