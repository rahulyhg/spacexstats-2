// Original jQuery countdown timer written by /u/EchoLogic, improved and optimized by /u/booOfBorg.
// Rewritten as an Angular directive for SpaceXStats 4
(function() {
    var app = angular.module('app');

    app.directive('countdown', ['$interval', function($interval) {
        return {
            restrict: 'E',
            scope: {
                specificity: '=',
                countdownTo: '=',
                isPaused: '=',
                type: '@',
                callback: '&?'
            },
            link: function($scope, elem, attrs) {

                $scope.isLaunchExact = ($scope.specificity == 6 || $scope.specificity == 7);

                $scope.$watch('specificity', function(newValue) {
                    $scope.isLaunchExact = (newValue == 6 || newValue == 7);
                });

                var countdownProcessor = function() {

                    var launchUnixSeconds = $scope.launchUnixSeconds;
                    var currentUnixSeconds = Math.floor(Date.now() / 1000);

                    if (launchUnixSeconds >= currentUnixSeconds) {
                        $scope.secondsAwayFromLaunch = launchUnixSeconds - currentUnixSeconds;

                        var secondsBetween = $scope.secondsAwayFromLaunch;
                        // Calculate the number of days, hours, minutes, seconds
                        $scope.days = Math.floor(secondsBetween / (60 * 60 * 24));
                        secondsBetween -= $scope.days * 60 * 60 * 24;

                        $scope.hours = Math.floor(secondsBetween / (60 * 60));
                        secondsBetween -= $scope.hours * 60 * 60;

                        $scope.minutes = Math.floor(secondsBetween / 60);
                        secondsBetween -= $scope.minutes * 60;

                        $scope.seconds = secondsBetween;

                        $scope.daysText = $scope.days == 1 ? 'Day' : 'Days';
                        $scope.hoursText = $scope.hours == 1 ? 'Hour' : 'Hours';
                        $scope.minutesText = $scope.minutes == 1 ? 'Minute' : 'Minutes';
                        $scope.secondsText = $scope.seconds == 1 ? 'Second' : 'Seconds';
                    } else {
                    }

                    if (attrs.callback) {
                        $scope.callback();
                    }
                };

                // Countdown here
                if ($scope.isLaunchExact) {
                    $scope.launchUnixSeconds = moment($scope.countdownTo).unix();
                    $interval(countdownProcessor, 1000);
                } else {
                    $scope.countdownText = $scope.countdownTo;
                }
            },
            templateUrl: '/js/templates/countdown.html'
        }
    }]);
})();