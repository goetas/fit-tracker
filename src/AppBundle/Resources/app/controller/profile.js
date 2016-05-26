sampleApp.controller('ProfileController', function($scope, $http, $location) {

    $scope.place = 'profile';

    $scope.doSave = function () {
        $http.post(
            $scope.user._links.edit.href,
            $scope.user
        ).then(function (data) {
            $scope.user = data.data;
        }, $scope.errorHandler);
    };
});