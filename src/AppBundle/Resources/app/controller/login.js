sampleApp.controller('LoginController', function ($scope, $http, $location, $rootScope) {
    $scope.place = 'login';

    if ($scope.user) {
        $location.path("/dashboard");
    }

    $scope.formData = {};

    $scope.doLogin = function () {
        $http.post(
            configs.routing.auth_login,
            $scope.formData
        ).then(function (data) {
            $rootScope.user = data.data;
            $scope.user = data.data;
            $location.path("/dashboard");
        }, $scope.errorHandler);
    };
});