(function() {
    'use strict';

    angular
        .module('antidote')
        .run(runBlock);

    /** @ngInject */
    function runBlock($mdToast) {
        $mdToast.showSimple = function(message) {
            $mdToast.show(
                $mdToast.simple()
                    .content(message)
                    .position('top right')
                    .hideDelay(3000)
            );
        };
    }

})();
