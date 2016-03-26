angular.module('teacher', []);
angular.module('teacher').controller('pupils', function ($scope, $http, $interval){
    $scope.pupils_logged = null;
    $scope.session = null;

    refreshPupils = function(){
        data = {session:9}
        $http.post('/API/session/getPupils/', data)
            .then(function(response){
                $scope.pupils_logged = response.data.pupils_logged;
                $scope.pupils_ended = response.data.pupils_ended;
                console.log(response.data.pupils_logged);
            },function(response){
                alert("Wystąpił błąd.");
            });
    }



    $scope.setSession = function(session){
        $scope.session = session;
    }
    refreshPupils();
    $interval(refreshPupils, 2500);
});