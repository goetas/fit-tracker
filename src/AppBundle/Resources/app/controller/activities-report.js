sampleApp.controller('ActivitiesReportController', function($scope, $http) {
    $scope.place = 'activities-report';
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
        $http.get($scope.user._links.activities_report.href, {params: $scope.filters}).then(function (data) {
            $scope.activities = data.data;
        });
    };
    $scope.doFilter();
});