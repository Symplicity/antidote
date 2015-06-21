'use strict';

angular.module('entic', [
    'ngResource',
    'ngMessages',
    'ngMaterial',
    'ui.router',
    'satellizer'
])
    .config(function ($stateProvider, $urlRouterProvider, $mdThemingProvider, $authProvider) {
        $mdThemingProvider.theme('default')
            .primaryPalette('blue')
            .accentPalette('grey');

        $authProvider.loginUrl = '/api/auth/login';
        $authProvider.signupUrl = '/api/auth/signup';

        $urlRouterProvider.otherwise('/');

        $stateProvider
            .state('home', {
                url: '/',
                templateUrl: 'app/home/home.html',
                controller: 'HomeCtrl as home'
            })
            .state('drugs', {
                url: '/drugs',
                abstract: true,
                templateUrl: '/app/drugs/drugs.html',
                controller: 'DrugsCtrl as drugs'
            })
            .state('drugs.view', {
                url: '/:id',
                abstract: true,
                templateUrl: '/app/drugs/drugs.view.html',
                controller: 'DrugsViewCtrl as drugsView',
                resolve: {
                    drug: ['$stateParams', 'DrugsService', function ($stateParams, DrugsService) {
                        return DrugsService.get({id: $stateParams.id}).$promise;
                    }]
                }
            })
            .state('drugs.view.overview', {
                url: '/overview',
                templateUrl: '/app/drugs/drugs.overview.html',
                controller: 'DrugsOverviewCtrl as drugsOverview'
            })
            .state('drugs.view.reviews', {
                url: '/reviews',
                templateUrl: '/app/drugs/drugs.reviews.html',
                controller: 'DrugsReviewsCtrl as drugsReviews'
            })
            .state('drugs.view.alternatives', {
                url: '/alternatives',
                templateUrl: '/app/drugs/drugs.alternatives.html',
                controller: 'DrugsAlternativesCtrl as drugsAlternatives'
            })
            .state('login', {
                url: '/login',
                templateUrl: 'app/login/login.form.html',
                controller: 'LoginCtrl as login'
            })
            .state('logout', {
                url: '/logout',
                template: null,
                controller: 'LogoutCtrl as logout'
            })
            .state('signup', {
                url: '/signup',
                templateUrl: 'app/signup/signup.form.html',
                controller: 'SignupCtrl as signup'
            })
            .state('password', {
                abstract: true,
                url: '/password',
                template: '<ui-view/>'
            })
            .state('password.forgot', {
                url: '/forgot',
                templateUrl: 'app/password/forgot/password.forgot.html',
                controller: 'ForgotPasswordCtrl as forgotPassword'
            })
            .state('password.reset', {
                abstract: true,
                url: '/reset',
                template: '<ui-view/>'
            })
            .state('password.reset.invalid', {
                url: '/invalid',
                templateUrl: 'app/password/reset/password.reset.invalid.html'
            })
            .state('password.reset.form', {
                url: '/:token',
                templateUrl: 'app/password/reset/password.reset.form.html',
                controller: 'ResetPasswordCtrl as resetPassword'
            })
            .state('profile', {
                url: '/profile',
                templateUrl: 'app/profile/profile.form.html',
                controller: 'ProfileCtrl as profile',
                resolve: {
                    authenticated: function($q, $location, $auth) {
                        var deferred = $q.defer();

                        if (!$auth.isAuthenticated()) {
                            $location.path('/login');
                        } else {
                            deferred.resolve();
                        }

                        return deferred.promise;
                    },
                    user: ['ProfileService', function (ProfileService) {
                        return ProfileService.get().$promise;
                    }]
                }
            });
    })
    .controller('AppCtrl', function ($scope, $mdToast) {

        $scope.$on('$stateChangeError', function() {
            var message = 'A website error has occurred. The website administrator has been notified of the issue. Sorry for the temporary inconvenience.';

            $mdToast.show(
                $mdToast.simple()
                    .content(message)
                    .position('bottom right')
                    .hideDelay(3000)
            );
        });
    });
