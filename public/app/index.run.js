(function() {
    'use strict';

    angular
        .module('antidote')
        .run(runBlock);

    /** @ngInject */
    function runBlock($mdToast) {
        $mdToast.showSimple = function(message) {
            if (typeof message === 'object') {
                var toasts = [];
                angular.forEach(message, function(m) {
                    this.push(m);
                }, toasts);
                message = toasts.join(', ');
            }
            $mdToast.show(
                $mdToast.simple()
                    .content(message)
                    .position('top right')
                    .hideDelay(3000)
            );
        };
    }

})();
