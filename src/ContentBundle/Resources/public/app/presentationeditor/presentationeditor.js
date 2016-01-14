angular.module('OpiferPresentationEditor', ['ngModal'])

    /**
     * Layout Service
     */
    .factory('LayoutService', ['$resource', function($resource) {
        return $resource(Routing.generate('opifer_content_api_layout'), {}, {
            index: {method: 'GET', isArray: true, params: {}}
        });
    }])

    /**
     * Presentation Editor
     */
    .directive('presentationEditor', function() {
        var tpl =
            '<input type="hidden" id="{{ formid }}" name="{{ name }}" value="{{ presentation }}" >' +
            '<a ng-click="isVisible = !isVisible" class="btn btn-default" ng-show="!isVisible">Click to edit presentation</a>' +
            '<div ng-show="isVisible" class="form-group row presentation">' +
            '    <div class="col-xs-12">' +
            '        <div ng-if="presentation">' +
            '            <layout subject="presentation" catalog="catalog" level="0"></layout>' +
            '        </div>' +
            '        <div ng-if="presentation == null" class="form-group row">' +
            '            <div class="layoutselect">' +
            '                <select class="form-control" ng-options="choice.name for choice in catalog" ng-model="selected" ng-change="addLayout()" placeholder="Add layout...">' +
            '                    <option value="">Add layout...</option>' +
            '                </select>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            //'    <div><pre>{{ presentation | json: object }}</pre></div>' +
            '</div>'
        ;

        return {
            restrict: 'E',
            transclude: true,
            template: tpl,
            scope: {
                name: '@',
                value: '@',
                formid: '@'
            },
            controller: function($scope, $http, $attrs, LayoutService) {
                $scope.isVisible = false;

                // We just return all available layouts for now
                $scope.catalog = LayoutService.index({});

                if ($scope.value.length <= 2 || angular.isUndefined($scope.value) || $scope.value === null) {
                   $scope.presentation = null;
                } else {
                    var json = JSON.parse($scope.value);
                    $scope.presentation = angular.fromJson(json);
                }

                $scope.addLayout = function() {
                    $scope.presentation = angular.copy(this.selected);
                    this.selected = null;
                };
            }
        };
    })

    /**
     * Layout
     */
    .directive('layout', ['$compile', function($compile) {
        var tpl =
            '<div class="form-group row">' +
            '    <div class="layoutselect">' +
            '        <span class="faux-label">{{ subject.name }}</span>' +
            '        <div class="controls">' +
            '            <a href="#" class="glyphicon glyphicon-th-list"></a> ' +
            '            <a class="glyphicon glyphicon-edit" ng-click="showPicker()" ng-show="subject.parameterSet !== undefined"></a> ' +
            '            <a class="glyphicon glyphicon-remove danger" ng-click="remove()"></a> ' +
            '        </div>' +
            '    </div>' +
            '</div>' +
            '<ng-modal show="picker.visible" width="80%" height="500px">' +
            '    <div ng-if="subject.parameters"><pre>{{ subject.parameters[\'query\'] | json: object }}</pre></div>' +
            '    <div ng-if="subject.parameterSet !== undefined" symfonyform="subject"></div>' +
            '    <a ng-click="closeModal()" class="btn btn-default">Update parameters</a>' +
            '</ng-modal>' +
            ' <div ng-repeat="(placeholder, layouts) in subject.placeholders track by $index">' +
            '    <div class="form-group row" ng-model="value">' +
            '        <label class="control-label col-xs-2">{{ placeholder }}</label>' +
            '        <div class="col-xs-10" ui-sortable ng-model="layouts">' +
            '            <div ng-repeat="layout in layouts">' +
            '                <layout subject="layout" catalog="catalog" holder="placeholder"></layout>' +
            '            </div>' +
            '            <div class="form-group row">' +
            '                <div class="layoutselect">' +
            '                    <select class="form-control" ng-options="choice.name for choice in catalog" ng-model="selected" ng-change="addLayout(placeholder)" placeholder="Add layout...">' +
            '                        <option value="">Add layout...</option>' +
            '                    </select>' +
            '                </div>' +
            '            </div>' +
            '        </div>' +
            '    </div>'+
            ' </div>'
        ;

        return {
            restrict: 'E',
            terminal: true,
            template: tpl,
            scope: {
                subject: '=', // The current layout
                catalog: '=', // The available select options
                holder:  '=',  // The placeholder in which this layout is placed on the parent scope
                level:   '@'
            },
            link: function(scope, element, attrs) {
                $compile(element.contents())(scope.$new());

                // Set empty parameters when they're not defined yet
                if (angular.isUndefined(scope.subject.parameters) && angular.isDefined(scope.subject.parameterSet)) {
                    scope.subject.parameters = {};
                    angular.forEach(scope.subject.parameterSet.attributes, function(value) {
                        this[value.name] = '';
                    }, scope.subject.parameters);
                }

                scope.picker = {
                    visible: false
                };

                scope.showPicker = function() {
                    scope.picker.visible = true;
                };
                scope.remove = function() {
                    if (scope.level == 0) {
                        scope.$parent.$parent.presentation = null;
                    } else {
                        scope.$parent.removeLayout(scope.holder, scope.subject);
                    }
                };
                scope.removeLayout = function(placeholder, layout) {
                    scope.subject.placeholders[placeholder].splice( scope.subject.placeholders[placeholder].indexOf(layout), 1 );
                };
                scope.addLayout = function(placeholder) {
                    scope.subject.placeholders[placeholder].push(angular.copy(this.selected));
                    this.selected = null;
                };
            }
        };
    }])

    /**
     * Symfony form directive
     *
     * Requests a symfonyform and manually compiles the return html
     */
    .directive('symfonyform', ['$compile', '$http', function($compile, $http) {
        return function (scope, element, attrs) {

            scope.attributes = [];

            if (scope.subject.parameterSet !== undefined) {
                scope.attributes = scope.subject.parameterSet.attributes;

                // Request the template and compile it
                $http.post(Routing.generate('opifer_content_formtype_angular'), {'attributes': scope.attributes}).success(function(data) {
                    var el = angular.element(data);
                    compiled = $compile(el);
                    element.append(el);
                    compiled(scope);
                });
            }
        };
    }])
;
