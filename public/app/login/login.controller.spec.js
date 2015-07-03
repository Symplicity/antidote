describe('Login Controller', function() {
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

    describe('LoginCtrl', function() {
        it('should call auth service to login user in and redirect to home', inject(
            function($mdToast, $auth, $state) {
                spyOn($auth, 'login').and.returnValue({
                    then: function(callback) {
                        callback({'token': 'abc.123.xyz-abc'});
                        return {
                            catch: function() {
                            }
                        };
                    }
                });

                spyOn($mdToast, 'showSimple');
                spyOn($state, 'go');

                $state.current = {
                    name: 'login'
                };

                var vm = controller('LoginCtrl', {
                    $mdToast: $mdToast,
                    $auth: $auth,
                    $state: $state
                });

                vm.username = 'foo';
                vm.password = 'bar';

                vm.login();

                expect($auth.login).toHaveBeenCalledWith({username: 'foo', password: 'bar'});
                expect($mdToast.showSimple).toHaveBeenCalledWith('You have successfully logged in');
                expect($state.go).toHaveBeenCalledWith('home');
            }));

        it('should call auth service to login user in and close modal', inject(
            function($mdToast, $auth, $state, LoginSignupModalService) {
                spyOn($auth, 'login').and.returnValue({
                    then: function(callback) {
                        callback({'token': 'abc.123.xyz-abc'});
                        return {
                            catch: function() {
                            }
                        };
                    }
                });

                spyOn($mdToast, 'showSimple');
                spyOn(LoginSignupModalService, 'close');

                var vm = controller('LoginCtrl', {
                    $mdToast: $mdToast,
                    $auth: $auth,
                    $state: {current: {name: 'baz'}},
                    LoginSignupModalService: LoginSignupModalService
                });

                vm.username = 'foo';
                vm.password = 'bar';

                vm.login();

                expect($auth.login).toHaveBeenCalledWith({username: 'foo', password: 'bar'});
                expect($mdToast.showSimple).toHaveBeenCalledWith('You have successfully logged in');
                expect(LoginSignupModalService.close).toHaveBeenCalled();
            }));
    });
});
