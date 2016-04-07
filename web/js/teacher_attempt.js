//angular.module('myApp', []).controller('myCtrl', function($scope, $http, $interval) {
angular.module('teacher', ['ngSanitize']).controller('at', function ($scope, $http, $interval){


    $scope.setAttempt = function(attempt){
        //var attempt = attempt;
        $scope.attempt = attempt;
        //attempt = attempt;
        getQuestions($scope.attempt);
        $interval(getQuestions, 2500);

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
                //console.log(response.data.image);
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