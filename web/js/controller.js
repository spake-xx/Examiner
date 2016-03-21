angular.module('myApp').controller('myCtrl', function ($scope, $http){
    $('#spinner').hide();
    $scope.attempt = 12;
    $scope.getAnswer = function() {
        $http.get("/API/getAnswer/"+$scope.attempt)
            .then(function (response) {
                $scope.answers = response.data.answers;
                $scope.question = response.data.question;
                $scope.user_answer.attempt = response.data.attempt;
            });
    }
    $scope.getAnswer();

    $scope.user_answer = {
        id: 0
    }

    $scope.send = function(data){
        if(!data){
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

    //$scope.question = 'Hello world!';
    //$scope.list = [
    //    {
    //        firstName: 'John',
    //        lastName: 'Doe'
    //    },
    //    {
    //        firstName: 'Mark',
    //        lastName: 'Smith'
    //    },
    //    {
    //        firstName: 'James',
    //        lastName: 'Mole'
    //    }
    //];
});