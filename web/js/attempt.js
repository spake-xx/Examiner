//angular.module('myApp', []).controller('myCtrl', function($scope, $http, $interval) {
angular.module('myApp', []).controller('attempt', function ($scope, $http, $interval){

    $scope.setAttempt = function(attempt){
        $scope.attempt = attempt;
    };

    //tutaj defaultowe wartości

    $scope.question = "Choose a question from the list on the right";
         //end defaultowych wartości
    $scope.getQuestion = function(question){
        $http.post('/attempt/ajax/question', question).then(function(response){
            $scope.answers = response.data.answers;
            $scope.user_answers = response.data.user_answers;
        },function(response){
            alert("Wystąpił błąd.");
        });
    };

    getQuestions = function(attempt){
      $http.post('/attempt/ajax/attempt', 1).then(function(response){
          $scope.attempt = response.data.attempt;
          $scope.answers = response.data.answers;
          $scope.result =  response.data.result;
          $scope.user_answers = response.data.user_answers;
      },function(response){
          alert("Wystąpił błąd.");
      });
    };
    getQuestions(1);
});