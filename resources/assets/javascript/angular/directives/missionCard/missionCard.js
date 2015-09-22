(function() {
    var app = angular.module('app');

    app.directive('missionCard', function() {
        return {
            restrict: 'E',
            scope: {
                size: '@',
                mission: '='
            },
            link: function($scope) {
            },
            templateUrl: '/js/templates/missionCard.html'
        }
    });
})();