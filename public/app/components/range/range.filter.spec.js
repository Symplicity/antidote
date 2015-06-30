describe('Range Filter', function() {
    'use strict';

    beforeEach(function() {
        module('antidote');
    });

    describe('rangeFilter', function() {
        it('should create a range with step 1 by default', inject(function(rangeFilter) {
            expect(rangeFilter([], 0, 0)).toEqual([0]);
            expect(rangeFilter([], 0, 1)).toEqual([0, 1]);
            expect(rangeFilter([], 1, 4)).toEqual([1, 2, 3, 4]);
        }));

        it('should create a range with provided step', inject(function(rangeFilter) {
            expect(rangeFilter([], 0, 0, 10)).toEqual([0]);
            expect(rangeFilter([], 0, 1, 2)).toEqual([0]);
            expect(rangeFilter([], 2, 10, 2)).toEqual([2, 4, 6, 8, 10]);
        }));

        it('should create an empty range with silly args', inject(function(rangeFilter) {
            expect(rangeFilter([], 5, 2)).toEqual([]);
            expect(rangeFilter([], 1, 0, -1)).toEqual([]);
        }));
    });
});
