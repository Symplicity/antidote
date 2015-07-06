describe('Signup Modal Service', function() {
    'use strict';

    var LoginSignupModalService;
    var $mdDialog;

    beforeEach(function() {
        module('antidote');
        inject(function(_$mdDialog_, _LoginSignupModalService_) {
            $mdDialog = _$mdDialog_;
            LoginSignupModalService = _LoginSignupModalService_;
        });
    });

    describe('open', function() {
        it('should open the login signup dialog box', function() {
            spyOn($mdDialog, 'show');

            expect(LoginSignupModalService.open).toBeDefined();

            LoginSignupModalService.open();

            expect($mdDialog.show).toHaveBeenCalledWith({
                controller: 'LoginSignupModalCtrl',
                controllerAs: 'loginSignupModal',
                templateUrl: 'app/components/login-signup-modal/login-signup-modal.html',
                clickOutsideToClose: true,
                hasBackdrop: true
            });

        });
    });

    describe('close', function() {
        it('should close the login signup dialog box', function() {
            spyOn($mdDialog, 'hide');

            expect(LoginSignupModalService.close).toBeDefined();
            LoginSignupModalService.close();

            expect($mdDialog.hide).toHaveBeenCalled();
        });
    });
});
