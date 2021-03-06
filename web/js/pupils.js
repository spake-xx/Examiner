angular.module('teacher', ['ngSanitize']);
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
    $scope.getAttemptView = function(attempt, firstname, lastname, grade){
      //$http.get('/attempt/view/teacher_attempt/'+attempt).then(function(response){
            $scope.modal_attempt = attempt;
            //$scope.modal_content = response.data;
            $scope.modal_name = firstname + " " + lastname + " - " + grade;
            setAttempt(attempt);

            //console.log(response.data);
      //});
    };
    setAttempt = function(attempt){
        //var attempt = attempt;
        $scope.attempt = attempt;
        //attempt = attempt;
        getQuestions($scope.attempt);
        //$interval(getQuestions, 2000);

    };
    $scope.refreshQuestions = function(attempt){
        $scope.attempt = attempt;
        getQuestions($scope.attempt);
    };
    //tutaj defaultowe wartości

    //$scope.question = "Choose a question from the list on the right";
    //end defaultowych wartości
    $scope.getQuestion = function(question){
        //question = $scope.question
        $http.post('/attempt/ajax/question/', {'attempt':$scope.attempt, 'question':question})
            .then(function(response){
                $scope.answers = response.data.answers;
                //$scope.user_answers = response.data.user_answers;
                $scope.image = response.data.image;
                //$scope.result = response.data.result;
                $scope.question = response.data.question;
                console.log(response.data.question);
                //$interval(getQuestion, 1000);
            },function(response){
                alert(response.data);
            });
        //alert(question);
        //$scope.question = question;

    };

    getQuestions = function(attempt){
        $http.post('/attempt/ajax/attempt/', $scope.attempt).then(function(response){
            $scope.attempt = response.data.attempt;
            //$scope.answers = response.data.answers;
            $scope.result =  response.data.result;
            $scope.user_answers = response.data.user_answers;
            //$scope.question_image = response.data.question_image;
            //console.log(response.data.result);
        });
    };


});