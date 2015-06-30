describe('Password Service', function() {
    'use strict';

    var Password;
    var httpBackend;

    beforeEach(function() {
        module('antidote');
        inject(function($httpBackend, _Password_) {
            httpBackend = $httpBackend;
            Password = _Password_;
        });
    });

    afterEach(function() {
        httpBackend.flush();
        httpBackend.verifyNoOutstandingExpectation();
        httpBackend.verifyNoOutstandingRequest();
    });

    describe('forgotPassword', function() {
        it('should post to server that user forgot their password', function() {
            var usernameData = {};
            httpBackend.expectPOST('/api/auth/forgot', usernameData).respond({});
            Password.forgotPassword(usernameData);
        });
    });

    describe('resetPassword', function() {
        it('should post to server that user wants to reset their password', function() {
            var passwordData = {};
            httpBackend.expectPOST('/api/auth/reset/fooToken', passwordData).respond({});
            Password.resetPassword('fooToken', passwordData);
        });
    });

});
