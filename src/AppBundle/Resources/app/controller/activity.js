sampleApp.controller('ActivityController', function ($scope, $routeParams, $http, $location) {
    var id = $routeParams.activity;
    $scope.place = 'activity';

    $scope.activity = {};

    var picker = {
        autoclose:true,
        format:'yyyy-mm-dd'
    };

    if (id !== 'new') {
        $http.get($scope.user._links.activities_get.href.replace(":activity:", id))
            .then(function (data) {
                $scope.activity = data.data;
                $('.thedatepicker').datepicker(picker)

            });
    } else{
        $('.thedatepicker').datepicker(picker);
    }

    $scope.doSave = function (goBack) {
        var saved = function (data) {
            $scope.activity = data.data;
            if (goBack) {
                $location.path("/activities");
            }
            id = data.data.id;
        };
        if (!$scope.activity._links) {
            $http.put($scope.user._links.activities_add.href, $scope.activity).then(saved, $scope.errorHandler);
        } else {
            $http.post($scope.activity._links.edit.href, $scope.activity).then(saved, $scope.errorHandler);
        }
    }
});
;