describe('Signup Modal Service', function() {
    'use strict';

    var SignupModalService;
    var $mdDialog;

    beforeEach(function() {
        module('antidote');
        inject(function(_$mdDialog_, _SignupModalService_) {
            $mdDialog = _$mdDialog_;
            SignupModalService = _SignupModalService_;
        });
    });

    describe('open', function() {
        it('should open the signup dialog box', function() {
            spyOn($mdDialog, 'show');

            expect(SignupModalService.open).toBeDefined();

            SignupModalService.open();

            expect($mdDialog.show).toHaveBeenCalledWith({
                controller: 'SignupModalCtrl',
                controllerAs: 'signupModal',
                templateUrl: 'app/components/signup-modal/signup-modal.html',
                clickOutsideToClose: true,
                hasBackdrop: true
            });

        });
    });

    describe('close', function() {
        it('should close the signup dialog box', function() {
            spyOn($mdDialog, 'hide');

            expect(SignupModalService.close).toBeDefined();
            SignupModalService.close();

            expect($mdDialog.hide).toHaveBeenCalled();
        });
    });
});
