sampleApp.controller('UserController', function ($scope, $routeParams, $http, $location) {
    var id = $routeParams.user;
    $scope.place = 'user';

    $scope.aUser = {};


    if (id !== 'new') {
        $http.get($scope.user._links.get.href.replace(":user:", id))
            .then(function (data) {
                $scope.aUser = data.data;
            });
    }

    $scope.doSave = function (goBack) {
        var saved = function (data) {
            $scope.aUser = data.data;
            if (goBack) {
                $location.path("/users");
            }
            id = data.data.id;
        };
        if (!$scope.aUser._links) {
            $http.put($scope.user._links.add.href, $scope.aUser).then(saved, $scope.errorHandler);
        } else {
            $http.post($scope.aUser._links.edit.href, $scope.aUser).then(saved, $scope.errorHandler);
        }
    };
});
;