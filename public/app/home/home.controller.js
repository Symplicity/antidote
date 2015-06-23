(function() {
    'use strict';

    angular
        .module('entic')
        .controller('HomeCtrl', HomeCtrl);

    /** @ngInject */
    function HomeCtrl() {

        this.search = {};
        this.search.keywords = '';
    }
})();
