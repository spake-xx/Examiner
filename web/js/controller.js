angular.module('myApp').controller('myCtrl', function ($scope, $http, $interval){
    $scope.image = null;
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
        if(client_time%1==0) {
            if (client_time < server_time) {
                roznica = server_time - client_time;
                $scope.$broadcast('timer-add-cd-seconds', roznica);
                console.log("SERVER:" + server_time + ", KLIENT:"+ client_time + ", DODAWANIE " + roznica);
            } else if(client_time>server_time){
                roznica = client_time - server_time;
                $scope.$broadcast('timer-add-cd-seconds', -roznica);
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
                //$scope.$broadcast('timer-add-cd-seconds', server_time);
                $scope.$broadcast('timer-start');
                $('#quiz-info').show();
            });
    }

    $scope.checkTime = function(){
        $http.get("/API/time/"+$scope.attempt)
            .then(function (response) {
                server_time = response.data.time;
                if(server_time<1){
                    endQuiz();
                }
            });
    }

    endQuiz = function(){
        $('#spinner').show();
        data = {
            attempt: $scope.attempt
        };
        console.log(angular.toJson(data));
        $http.post('/API/end/', data)
            .then(function(response){
                $('#spinner').hide();
                window.location.assign('/quiz/result/'+$scope.attempt)
            },function(response){
                alert("Wystąpił błąd.");
            });
    }

    $scope.getAnswer = function() {
        $http.get("/API/getAnswer/"+$scope.attempt)
            .then(function (response) {
                if(response.data!=false) {
                    $scope.answers = response.data.answers;
                    $scope.question = response.data.question;
                    $scope.answered = response.data.answered;
                    $scope.questions_count = response.data.questions_count;

                    if(response.data.image){
                        $scope.image = response.data.image;
                    }else{
                        $scope.image = null;
                    }
                    $scope.user_answer.attempt = response.data.attempt;
                }else{
                    endQuiz();
                }
            }, function(response){
                alert(response.data);
            });
    }

    $scope.user_answer = {
        id: null
    }

    $scope.checkImg = function(){
        if($scope.image != null){
            return 1;
        }
        return 0;
    }

    $scope.send = function(data){
        if(!data.id){
            $scope.bsAlert.msg = "Zaznacz odpowiedź !";
            return 0;
        }
        $('#spinner').show();
        $scope.bsAlert.msg = null;
        $http.post('/ajax/sendAnswer/', data)
            .then(function(response){
                $('#spinner').hide();
                $scope.user_answer.id = null;
                $scope.getAnswer();
            },function(response){
                $('#spinner').hide();
            });
    }

    $scope.bsAlert = {
        msg: null
    }

});
