(function() {
    'use strict';

    angular
        .module('antidote')
        .factory('ServerErrorHandlerService', ServerErrorHandlerService);

    /** @ngInject */
    function ServerErrorHandlerService($mdToast, SignupModalService) {
        return {
            handle: function(response) {
                if (response.status === 401) {
                    //on 401 error from server ask user to log in (prob. token expired)
                    SignupModalService.open();
                } else {
                    $mdToast.showSimple(response.data.message);
                }
            }
        };
    }
})();
