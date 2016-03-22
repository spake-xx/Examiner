angular.module('myApp').controller('myCtrl', function ($scope, $http, $interval){
    $('#spinner').hide();
    $('#quiz-info').hide();
    server_time = 11;
    client_time = 11;

    $scope.attempt = null;
    $scope.ile = 1;

    $scope.$on('timer-tick', function (event, data) {
        client_time = data.millis / 1000;
        $scope.checkTime($scope.attempt);
        console.log(server_time);
        if(client_time%10==0) {
            if (client_time < server_time) {
                roznica = server_time - client_time;
                $scope.$broadcast('timer-add-cd-seconds', roznica);
                console.log("SERVER:" + server_time + ", KLIENT:"+ client_time + ", DODAWANIE " + roznica);
            } else if(client_time>server_time){
                roznica = client_time - server_time;
                $scope.$broadcast('timer-add-cd-seconds', -0);
                console.log("SERVER:" + server_time + ", KLIENT:"+ client_time + ", ODEJMOWANIE: " + roznica);
            }
        }
    });

        $scope.setAttempt = function(attempt){
            $scope.attempt = attempt;
            $scope.getAnswer();
            $http.get("/API/time/"+$scope.attempt)
                .then(function (response) {
                    server_time = response.data.time;
                    $scope.ile = server_time;
                    $scope.$broadcast('timer-add-cd-seconds', server_time);
                    $scope.$broadcast('timer-start');
                    $('#quiz-info').show();
                });
        }

    $scope.checkTime = function(){
        $http.get("/API/time/"+$scope.attempt)
            .then(function (response) {
                server_time = response.data.time;
                if(server_time<1){
                    window.location = "/quiz/end/"+$scope.attempt
                }
            });
    }

    $scope.getAnswer = function() {
        $http.get("/API/getAnswer/"+$scope.attempt)
            .then(function (response) {
                if(response.data!=false) {
                    $scope.answers = response.data.answers;
                    $scope.question = response.data.question;
                    $scope.user_answer.attempt = response.data.attempt;
                }else{
                    window.location = "/quiz/result/"+$scope.attempt
                }
            });
    }

    $scope.user_answer = {
        id: 0
    }

    $scope.send = function(data){
        if(!data){
            alert("Wybierz odpowiedź chujku.");
            return 0;
        }
        $('#spinner').show();
        $http.post('/ajax/sendAnswer/', data)
            .then(function(response){
                $('#spinner').hide();
                console.log(response);
                $scope.getAnswer();
            },function(response){
                $('#spinner').hide();
            });
    }

});

