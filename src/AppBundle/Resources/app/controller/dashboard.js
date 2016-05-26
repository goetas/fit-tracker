sampleApp.controller('DashboardController', function($scope, $http) {

    $scope.place = 'dashboard';
    $scope.hasActivities = false;

    var area = Morris.Area({
        element: 'morris-area-chart',
        data: [],
        xkey: 'day',
        ykeys: ['speed', 'time', 'distance'],
        labels: ['Speed', 'Time', 'Distance'],
        pointSize: 2,
        hideHover: 'auto',
        resize: true
    });

    $http.get($scope.user._links.activities.href).then(function (data) {
        $scope.hasActivities = data.data.length>2;
        setTimeout(function(){
            area.setData(data.data.map(function(activity) {
                var activityEdited = activity;
                activityEdited.speed = Math.round(activity.distance/activity.time);
                return activityEdited;
            }));
        },100);

    });

});