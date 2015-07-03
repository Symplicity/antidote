describe('Signup Controller', function() {
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

    describe('SignupCtrl', function() {
        it('should call auth service to login user in and redirect to home', inject(
            function($mdToast, $auth, $state) {
                spyOn($auth, 'signup').and.returnValue({
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
                    name: 'signup'
                };

                var vm = controller('SignupCtrl', {
                    $mdToast: $mdToast,
                    $auth: $auth,
                    $state: $state
                });

                vm.user = {
                    username: 'foo',
                    email: 'foo@bar.com',
                    password: 'pw',
                    gender: 'm',
                    age: 34
                };

                vm.signup();

                expect($auth.signup).toHaveBeenCalledWith({
                    username: 'foo',
                    email: 'foo@bar.com',
                    password: 'pw',
                    gender: 'm',
                    age: 34
                });

                expect($mdToast.showSimple).toHaveBeenCalledWith('You have successfully signed up');
                expect($state.go).toHaveBeenCalledWith('profile');
            }));

        it('should call auth service to sign user up and close modal', inject(
            function($mdToast, $auth, $state, LoginSignupModalService) {
                spyOn($auth, 'signup').and.returnValue({
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

                var vm = controller('SignupCtrl', {
                    $mdToast: $mdToast,
                    $auth: $auth,
                    $state: {current: {name: 'baz'}},
                    LoginSignupModalService: LoginSignupModalService
                });

                vm.user = {
                    username: 'foo',
                    email: 'foo@bar.com',
                    password: 'pw',
                    gender: 'm',
                    age: 34
                };

                vm.signup();

                expect($auth.signup).toHaveBeenCalledWith({
                    username: 'foo',
                    email: 'foo@bar.com',
                    password: 'pw',
                    gender: 'm',
                    age: 34
                });

                expect($mdToast.showSimple).toHaveBeenCalledWith('You have successfully signed up');
                expect(LoginSignupModalService.close).toHaveBeenCalled();
            }));
    });
});
