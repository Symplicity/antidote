'use strict';

angular.module('entic', ['ngResource', 'ui.router', 'ngMaterial'])
    .config(function ($stateProvider, $urlRouterProvider) {
        $stateProvider
            .state('home', {
                url: '/',
                templateUrl: 'app/main/main.html',
                controller: 'MainCtrl'
            });

        $urlRouterProvider.otherwise('/');
    })
;
