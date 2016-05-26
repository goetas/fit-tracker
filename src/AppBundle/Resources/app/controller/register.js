sampleApp.controller('RegisterController', function($scope, $http, $location, $rootScope) {

    $scope.place = 'register';

    if ($scope.user) {
        $location.path("/dashboard");
    }

    $scope.formData = {};

    $scope.doRegister = function (){
        $http.put(
            configs.routing.user_register,
            $scope.formData
        ).then(function (data) {
            $rootScope.user = data.data;
            $scope.user = data.data;
            $location.path( "/dashboard" );
        }, $scope.errorHandler);
    };
});