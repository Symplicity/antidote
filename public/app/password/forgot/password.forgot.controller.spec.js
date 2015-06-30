describe('Password Forgot Controller', function() {
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

    describe('ForgotPasswordCtrl', function() {
        it('should call password service to forget', inject(function(Password, $mdToast) {
            spyOn(Password, 'forgotPassword').and.returnValue({
                then: function(callback) {
                    callback({data: {message: 'Foo'}});
                    return {
                        catch: function() {}
                    };
                }
            });

            spyOn($mdToast, 'show');

            var ctrl = controller('ForgotPasswordCtrl', {
                $mdToast: $mdToast,
                Password: Password
            });
            ctrl.username = 'foo';

            ctrl.forgot();

            expect($mdToast.show).toHaveBeenCalled();
        }));
    });
});
