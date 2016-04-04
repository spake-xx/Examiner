//angular.module('myApp', []).controller('myCtrl', function($scope, $http, $interval) {
angular.module('myApp', []).controller('attempt', function ($scope, $http, $interval){


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
        //$http.post('/attempt/ajax/question', question).then(function(response){
        //    //$scope.answers = response.data.answers;
        //    //$scope.user_answers = response.data.user_answers;
        //},function(response){
        //    alert("Wystąpił błąd.");
        //});
        $scope.question = question;

    };

    getQuestions = function(attempt){
      $http.post('/attempt/ajax/attempt/', $scope.attempt).then(function(response){
          $scope.attempt = response.data.attempt;
          $scope.answers = response.data.answers;
          $scope.result =  response.data.result;
          $scope.user_answers = response.data.user_answers;
      });
    };


});