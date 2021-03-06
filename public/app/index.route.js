(function() {
    'use strict';

    angular
        .module('antidote')
        .config(routeConfig);

    /** @ngInject */
    function routeConfig($stateProvider, $urlRouterProvider) {

        $stateProvider
            .state('home', {
                url: '/',
                templateUrl: 'app/home/home.html'
            })
            .state('privacy', {
                url: '/privacy',
                templateUrl: 'app/legal/privacy.html'
            })
            .state('terms', {
                url: '/terms',
                templateUrl: 'app/legal/terms.html'
            })
            .state('drugs', {
                url: '/drugs',
                abstract:true,
                templateUrl: 'app/drugs/drugs.html'
            })
            .state('drugs.list', {
                url: '/?term&limit',
                templateUrl: 'app/drugs/drugs.list.html',
                controller: 'DrugsListCtrl as drugsList',
                params: {
                    term: {
                        value: 'a',
                        squash: true
                    }
                }
            })
            .state('drugs.view', {
                url: '/:id',
                abstract: true,
                templateUrl: 'app/drugs/drugs.view.html',
                controller: 'DrugsViewCtrl as drugsView'
            })
            .state('drugs.view.overview', {
                url: '/overview',
                templateUrl: 'app/drugs/drugs.overview.html',
                controller: 'DrugsOverviewCtrl as drugsOverview'
            })
            .state('drugs.view.reviews', {
                url: '/reviews',
                templateUrl: 'app/drugs/drugs.reviews.html',
                controller: 'DrugsReviewsCtrl as drugsReviews'
            })
            .state('drugs.view.alternatives', {
                url: '/alternatives',
                templateUrl: 'app/drugs/drugs.alternatives.html',
                controller: 'DrugsAlternativesCtrl as drugsAlternatives'
            })
            .state('about', {
                url: '/about',
                templateUrl: 'app/about/about.html'
            })
            .state('login', {
                url: '/login',
                templateUrl: 'app/login/login.html',
                controller: 'LoginCtrl as login'
            })
            .state('logout', {
                url: '/logout',
                template: null,
                controller: 'LogoutCtrl as logout'
            })
            .state('signup', {
                url: '/signup',
                templateUrl: 'app/signup/signup.html',
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
                    }
                }
            });

        $urlRouterProvider.otherwise('/');
    }

})();
