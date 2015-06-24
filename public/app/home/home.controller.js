(function() {
    'use strict';

    angular
        .module('antidote')
        .controller('HomeCtrl', HomeCtrl);

    /** @ngInject */
    function HomeCtrl() {

        this.search = {};
        this.search.keywords = '';
    }
})();
