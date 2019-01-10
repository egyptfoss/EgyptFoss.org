angular.module('viewerApp', [])
        .controller('ViewerController', ['$scope', '$http', function ($scope, $http) {
                $scope.data = "";
                $scope.feature = {};
                $http({
                    method: 'GET',
                    url: '/testing/docs/updateDocument.php'
                }).then(function successCallback(response) {
                    $scope.modules = response.data;
                    $scope.$watch("modules", function(value) {
                        if (value) {
                            $scope.$evalAsync(
                                            function() {
                                                initDisplay()
                                            }
                                    )
                        }
                    });
                });
            }])
        .filter('allScenarios', function () {
            return function(modules) {
                if(modules === undefined)
                    return;
                var allScenarios = [];
                var moduleIndex = 1;
                var featureIndex = 1;
                var scenarioIndex = 1;
                angular.forEach(modules, function(module){
                    features = module.features;
                    featureIndex = 1;
                    angular.forEach(features, function(feature){
                        scenarios = feature.scenarios;
                        scenarioIndex = 1;
                        angular.forEach(scenarios, function(scenario){
                            scenario["moduleIndex"] = moduleIndex;
                            scenario["featureIndex"] = featureIndex;
                            scenario["scenarioIndex"] = scenarioIndex;
                            allScenarios.push(scenario);
                            scenarioIndex++;
                        });
                        featureIndex++;
                    });
                    moduleIndex++;
                });
                return allScenarios;
            }
        })
        .filter('allBackgrounds', function () {
            return function(modules) {
                if(modules === undefined)
                    return;
                var allBackgrounds = [];
                var moduleIndex = 1;
                var featureIndex = 1;
                angular.forEach(modules, function(module){
                    features = module.features;
                    featureIndex = 1;
                    angular.forEach(features, function(feature){
                        background = feature.background;
                        background["moduleIndex"] = moduleIndex;
                        background["featureIndex"] = featureIndex;
                        allBackgrounds.push(background);
                        featureIndex++;
                    });
                    moduleIndex++;
                });
                return allBackgrounds;
            };
        });
;