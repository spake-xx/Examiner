angular.module('teacher', []);
angular.module('teacher').controller('pupils', function ($scope, $http, $interval){
    $scope.pupils_logged = [];
    $scope.pupils_ended = [];
    $scope.session = null;
    $scope.time = null;

    refreshPupils = function(){
        data = {session:$scope.session, time:$scope.time}
        $http.post('/API/session/getPupils/', data)
            .then(function(response){
                $scope.pupils_logged = response.data.pupils_logged;
                $scope.pupils_ended = response.data.pupils_ended;
                console.log($scope.pupils_logged.length > 0);
            });
    }

    $scope.pupilsLoggedEmpty = function(){
        return !($scope.pupils_logged.length > 0);
    }
    $scope.pupilsEndedEmpty = function(){
        return !($scope.pupils_ended.length > 0);
    }

    $scope.setSession = function(session, time){
        $scope.session = session;
        $scope.time = time;
        refreshPupils();
        $interval(refreshPupils, 800);
    }

    $scope.setTime = function(time){
        $scope.time = time;
        refreshPupils();
    }
});