(function() {
    var app = angular.module('app', []);

    app.directive("dropdown", function() {
        return {
            restrict: 'E',
            require: '^ngModel',
            scope: {
                data: '=options',
                uniqueKey: '@',
                titleKey: '@',
                imageKey: '@?',
                descriptionKey: '@?',
                searchable: '@',
                placeholder: '@',
                idOnly: '@?'
            },
            link: function($scope, element, attributes, ngModelCtrl) {

                ngModelCtrl.$viewChangeListeners.push(function() {
                    $scope.$eval(attributes.ngChange);
                });

                $scope.$watch("data", function() {
                    $scope.options = $scope.data.map(function(option) {
                        var props = {
                            id: option[$scope.uniqueKey],
                            name: option[$scope.titleKey],
                            image: option.featuredImage ? option.featuredImage.media_thumb_small : option.media_thumb_small
                        };

                        if (typeof $scope.descriptionKey !== 'undefined') {
                            props.description = option[$scope.descriptionKey];
                        }

                        return props;
                    });
                });

                ngModelCtrl.$render = function() {
                    $scope.selectedOption = ngModelCtrl.$viewValue;
                }

                ngModelCtrl.$parsers.push(function(viewValue) {
                    if ($scope.idOnly === 'true') {
                        return viewValue.id;
                    } else {
                        return viewValue;
                    }
                });

                ngModelCtrl.$formatters.push(function(modelValue) {
                    if ($scope.idOnly === 'true' && angular.isDefined($scope.options)) {
                        return $scope.options.filter(function(option) {
                            return option.id = modelValue;
                        });
                    } else {
                        return modelValue;
                    }
                });

                $scope.selectOption = function(option) {
                    $scope.selectedOption = option;
                    ngModelCtrl.$setViewValue(option);
                    $scope.dropdownIsVisible = false;
                };

                $scope.toggleDropdown = function() {
                    $scope.dropdownIsVisible = !$scope.dropdownIsVisible;
                    if (!$scope.dropdownIsVisible) {
                        $scope.search = null;
                    }
                };

                $scope.dropdownIsVisible = false;
            },
            templateUrl: '/js/templates/dropdown.html'
        }
    });
})();
