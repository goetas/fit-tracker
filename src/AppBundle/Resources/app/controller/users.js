sampleApp.controller('UsersController', function($scope, $http, $rootScope, $location) {
    $scope.place = 'users';
    $scope.users = [];

    $http.get(configs.routing.user_list).then(function (data) {
        $scope.users = data.data;
    });

    $scope.doLogin = function (user){
        $http.post(user._links.login_as.href)
            .then(function(data){
                $rootScope.user = data.data;
                $scope.user = data.data;
                $location.path("/dashboard");
            });
    };
});