sampleApp.controller('ActivitiesController', function($scope, $http) {
    //$scope.user = false;
    $scope.place = 'activities';
    $scope.activities = [];
    $scope.filters = {
        from:null,
        to:null
    };

    var picker = {
        autoclose:true,
        format:'yyyy-mm-dd'
    };
    $('.thedatepicker').datepicker(picker);

    $scope.doFilter = function (){
        $http.get($scope.user._links.activities.href, {params: $scope.filters}).then(function (data) {
            $scope.activities = data.data;
        })
    };
    $scope.doFilter();
});