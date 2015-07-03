describe('Error Handler Service', function() {
    'use strict';

    var mdToast;
    var ServerErrorHandlerService;
    var SignupModalService;

    beforeEach(function() {
        module('antidote');
        inject(function($mdToast, _ServerErrorHandlerService_, _SignupModalService_) {
            mdToast = $mdToast;
            ServerErrorHandlerService = _ServerErrorHandlerService_;
            SignupModalService = _SignupModalService_;
        });
    });

    describe('handle', function() {
        it('should open signup on auth error', function() {
            spyOn(SignupModalService, 'open');

            ServerErrorHandlerService.handle({status: 401});

            expect(SignupModalService.open).toHaveBeenCalled();
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
