(function() {
    'use strict';

    angular
        .module('antidote')
        .filter('range', function() {
            return function(range, min, max, step) {
                step = step || 1;
                for (var i = min; i <= max; i += step) {
                    range.push(i);
                }
                return range;
            };
        });
})();
