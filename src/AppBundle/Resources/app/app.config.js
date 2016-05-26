var sampleApp = angular.module('app', ['ngRoute', 'SharedServices']);

sampleApp.config(function ($httpProvider) {
    $httpProvider.interceptors.push('errorHttpInterceptor');
});

sampleApp.config(function ($routeProvider) {
    $routeProvider
        .when('/', {
            templateUrl: configs.routing.root + '/web/home.html',
            controller: 'HomeController'
        })
        .when('/login', {
            templateUrl: configs.routing.root + '/web/login.html',
            controller: 'LoginController'
        })
        .when('/register', {
            templateUrl: configs.routing.root + '/web/register.html',
            controller: 'RegisterController'
        })
        .when('/profile', {
            templateUrl: configs.routing.root + '/web/profile.html',
            controller: 'ProfileController'
        })
        .when('/dashboard', {
            templateUrl: configs.routing.root + '/web/dashboard.html',
            controller: 'DashboardController'
        })
        .when('/activities', {
            templateUrl: configs.routing.root + '/web/activities.html',
            controller: 'ActivitiesController'
        })
        .when('/activities-report', {
            templateUrl: configs.routing.root + '/web/activities-report.html',
            controller: 'ActivitiesReportController'
        })
        .when('/activities/:activity', {
            templateUrl: configs.routing.root + '/web/activity.html',
            controller: 'ActivityController'
        })
        .when('/users', {
            templateUrl: configs.routing.root + '/web/users.html',
            controller: 'UsersController'
        })
        .when('/users/:user', {
            templateUrl: configs.routing.root + '/web/user.html',
            controller: 'UserController'
        })
        .otherwise({
            redirectTo: '/'
        });
});
// run blocks
sampleApp.run(function($rootScope, $http, $location) {
    $rootScope.doLogout = function () {
        $http.post(configs.routing.auth_logout).then(function (data) {
            $rootScope.user = false;
            $location.path("/");
        }, function (data) {
            $scope.error = data;
        });
    };
    
    $rootScope.hideError = function (goBack) {
        $rootScope.error = null;
    };
    $rootScope.errorHandler = function (data) {
        $rootScope.error = data.data;
    };

    $rootScope.user = configs.user;
});