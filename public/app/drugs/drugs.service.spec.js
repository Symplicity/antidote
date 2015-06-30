describe('Drugs Service', function() {
    'use strict';

    var DrugsService;
    var httpBackend;

    beforeEach(function() {
        module('antidote');
        inject(function($httpBackend, _DrugsService_) {
            httpBackend = $httpBackend;
            DrugsService = _DrugsService_;
        });
    });

    afterEach(function() {
        httpBackend.flush();
        httpBackend.verifyNoOutstandingExpectation();
        httpBackend.verifyNoOutstandingRequest();
    });

    describe('query', function() {
        it('should query the server for list of drugs', function() {
            httpBackend.expectGET('/api/drugs').respond({});
            DrugsService.query();
        });
    });

    describe('queryAutocomplete', function() {
        it('should query the server for drugs for auto-complete', function() {
            httpBackend.expectGET('/api/autocomplete/drugs').respond([]);
            DrugsService.queryAutocomplete();
        });
    });

    describe('getReviews', function() {
        it('should query the server for reviews', function() {
            httpBackend.expectGET('/api/drugs/1/reviews').respond({});
            DrugsService.getReviews({id: 1});
        });
    });

    describe('postReview', function() {
        it('should post new review to server', function() {
            httpBackend.expectPOST('/api/drugs/1/reviews').respond({});
            DrugsService.postReview({id: 1, comment: 'Foo'});
        });
    });

    describe('voteOnReview', function() {
        it('should post new review votes to server', function() {
            httpBackend.expectPOST('/api/drug-reviews/1/vote').respond({});
            DrugsService.voteOnReview({id: 1, vote: -1});
        });
    });

    describe('getAlternatives', function() {
        it('should query the server for alternative drugs', function() {
            httpBackend.expectGET('/api/drugs/1/alternatives').respond({});
            DrugsService.getAlternatives({id: 1});
        });
    });
});
