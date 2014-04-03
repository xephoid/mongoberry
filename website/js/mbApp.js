app = angular.module("mbApp", ["ngRoute"]);

app.config(function($routeProvider) {
	$routeProvider.
		when("/home", {
			templateUrl: "parts/home.html",
			controller: "HomeCtrl"
		}).
		when("/blog", {
			templateUrl: "parts/blog.html",
			controller: "BlogCtrl"
		}).
		when("/about", {
			templateUrl: "parts/about.html",
			controller: "AboutCtrl"
		}).
		otherwise({redirectTo: "/home"})
});

app.controller("HomeCtrl", function($scope, $http) {
	$scope.saveJson = function() {
		$scope.reply = "";
		window.location = "/api?a=insert&db=home&co=json&do=" + $scope.someJson + "&pretty=true";
	}
});

app.controller("BlogCtrl", function($scope, $http) {
	$scope.entries = [];
	$http.get("/api?db=blog&co=entries&a=find").success(function(data) {
		$scope.entries = data;
		angular.forEach($scope.entries, function(entry) {
			if (entry.createdAt) {
				var dt = new Date(entry.createdAt.sec * 1000);
				entry.date = dt.toDateString();
			}
		});
	});
});

app.controller("AboutCtrl", function($scope) {
	$scope.about = "";
});