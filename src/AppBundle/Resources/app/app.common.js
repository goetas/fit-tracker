angular.module('SharedServices', [])
.factory('errorHttpInterceptor', function ($q, $rootScope) {
    return {
        response: function (response) {
            $rootScope.error = null;
            $rootScope.saved = null;
            return response;
        },
        responseError: function (response) {
            $rootScope.saved = null;
            $rootScope.error = null;
            return $q.reject(response);
        }
    };
})
.directive('loading',   ['$http' ,function ($http) {
    return {
        restrict: 'A',
        link: function (scope, elm, attrs)
        {
            scope.isLoading = function () {
                return $http.pendingRequests.length > 0;
            };

            scope.$watch(scope.isLoading, function (v)
            {
                if(v){
                    elm.show();
                }else{
                    elm.hide();
                }
            });
        }
    };

}]);

;