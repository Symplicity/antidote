(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('ProfileCtrl', ProfileCtrl);

    /** @ngInject */
    function ProfileCtrl($mdToast, ProfileService) {
        var that = this;
        activate();

        function activate() {
            ProfileService.get().$promise.then(function(user) {
                that.user = user;
            });
        }

        /**
         * Update user's profile information.
         */
        this.updateProfile = function() {
            this.user.$update()
                .then(function() {
                    showDefaultToast('Profile has been updated');
                })
                .catch(function(response) {
                    showDefaultToast(response.data.message);
                });
        };

        this.range = function(min, max, step) {
            step = step || 1;
            var input = [];
            for (var i = min; i <= max; i += step) {
                input.push(i);
            }
            return input;
        };
        function showDefaultToast(message) {
            $mdToast.show(
                $mdToast.simple()
                    .content(message)
                    .position('top right')
                    .hideDelay(3000)
            );
        }

    }
})();
