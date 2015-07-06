describe('Error Handler Service', function() {
    'use strict';

    var mdToast;
    var ServerErrorHandlerService;
    var LoginSignupModalService;

    beforeEach(function() {
        module('antidote');
        inject(function($mdToast, _ServerErrorHandlerService_, _LoginSignupModalService_) {
            mdToast = $mdToast;
            ServerErrorHandlerService = _ServerErrorHandlerService_;
            LoginSignupModalService = _LoginSignupModalService_;
        });
    });

    describe('handle', function() {
        it('should open signup on auth error', function() {
            spyOn(LoginSignupModalService, 'open');

            ServerErrorHandlerService.handle({status: 401});

            expect(LoginSignupModalService.open).toHaveBeenCalled();
        });

        it('should open toast on regular error', function() {
            spyOn(mdToast, 'show');

            ServerErrorHandlerService.handle({
                status: 400,
                data: {
                    message: 'Foo'
                }
            });

            expect(mdToast.show).toHaveBeenCalled();
        });
    });
});
