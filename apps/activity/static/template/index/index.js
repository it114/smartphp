
angular.module('qijia',[])
.controller('indexCtrl', function ($scope, $http) {
	$http.get(_API_SERVER+'activity/index/members').success(function(data) {
	    $scope.members = data.data;
	    console.log($scope.members);
	});
});

