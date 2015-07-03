describe('Error Handler Service', function() {
    'use strict';

    var mdToast;
    var ErrorHandlerService;
    var SignupModalService;

    beforeEach(function() {
        module('antidote');
        inject(function($mdToast, _ErrorHandlerService_, _SignupModalService_) {
            mdToast = $mdToast;
            ErrorHandlerService = _ErrorHandlerService_;
            SignupModalService = _SignupModalService_;
        });
    });

    describe('handle', function() {
        it('should open signup on auth error', function() {
            spyOn(SignupModalService, 'open');

            ErrorHandlerService.handle({status: 401});

            expect(SignupModalService.open).toHaveBeenCalled();
        });

        it('should open toast on regular error', function() {
            spyOn(mdToast, 'show');

            ErrorHandlerService.handle({
                status: 400,
                data: {
                    message: 'Foo'
                }
            });

            expect(mdToast.show).toHaveBeenCalled();
        });
    });
});
