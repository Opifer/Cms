'use strict';

angular.module('OpiferNestedContent', ['ui.sortable'])
    /**
     * Schema Service
     */
    .factory('SchemaService', ['$resource', function($resource) {
        return $resource(Routing.generate('opifer_eav_api_schema'), {}, {
            index: {method: 'GET', isArray: true, params: {}}
        });
    }])

    /**
     * Render the nested content
     */
    .directive('nestedContent', ['$compile', function($compile) {
        var tpl =
            '<div ui-sortable ng-model="subjects">' +
            '   <div nested-content-form ng-repeat="subject in subjects track by $index"></div>' +
            '</div>' +
            '<select class="form-control select-schema" ng-options="schema.displayName for (key, schema) in schemas | orderBy:\'displayName\'" ng-model="selected" ng-change="addSubject()">' +
            '    <option value="">Add schema...</option>' +
            '</select>'
        ;

        return {
            restrict: 'E',
            schema: tpl,
            scope: {
                ids: '@',
                attribute: '@',
                attributeId: '@'
            },
            link: function(scope, element, attrs) {
                scope.addSubject = function() {
                    // Add the schema to the subjects array
                    scope.subjects.push(angular.copy(this.selected));

                    // Clear the select field so we can re-use it for adding more
                    // nested content.
                    this.selected = null;
                };

                scope.removeSubject = function(index) {
                    // Remove the subject from the subjects array
                    scope.subjects.splice(index, 1);
                };
            },
            controller: function($scope, $http, $attrs, SchemaService) {
                // Query all available schemas to make them available in the
                // select field.
                $scope.schemas = SchemaService.index({attribute: $scope.attributeId });
                $scope.subjects = [];

                // Retrieve all predefined nested content and add them to the
                // subjects array.
                if (typeof $scope.ids !== 'undefined') {

                    var ids = $scope.ids.split(',');
                    for (var i = 0; i < ids.length; i++) {
                        $scope.subjects.push({name: ids[i]});
                    };
                }
            }
        };
    }])

    /**
     * render the nested content form
     *
     * Requests a form type and manually compiles the return html
     */
    .directive('nestedContentForm', ['$compile', '$http', '$sce', function($compile, $http, $sce) {
        return {
            schema:
                '<article class="nested-content-item">'+
                '   <header class="form-group">' +
                '       <div class="col-xs-12 col-sm-9 col-lg-10">'+
                '           <div class="title" ng-click="toggle()">'+
                '               <div class="cell-icon cell-columns" ng-if="!subject.data.coverImage"></div>'+
                '               <div style="background-image: url({{ subject.data.coverImage }});" ng-if="subject.data.coverImage" class="content-cover"></div>'+
                '               <span class="template form-control-static"><span class="label label-info">{{ subject.data.templateDisplayName }}</span></span>'+
                '           </div>'+
                '       </div>'+
                '       <div class="col-xs-12 col-sm-3 col-lg-2">'+
                '           <div class="pull-right">'+
                '               <div class="btn-group"><a class="btn btn-sm btn-danger" ng-click="removeSubject($index)">Delete</a></div>'+
                '               <div class="btn-group"><a class="btn btn-sm btn-drag"></a></div>'+
                '           </div>'+
            '           </div>'+
                '   </header>' +
                '   <div ng-hide="hidden">' +
                '       <div class="form-nested-content" compile="subject.form"></div>'+
                '   </div>' +
                '</article>',
            replace: true,
            link: function (scope, element, attrs) {
                scope.subject.data = {};
                scope.subject.form = '';

                if (angular.isUndefined(scope.$parent.$parent.subject)) {
                    var parent = '';
                } else {
                    var parent = element.parent().parent().parent().find('input')[0].name;
                }

                // Request the form schema and compile it
                $http.post(Routing.generate('opifer_eav_form_render', {
                    attribute: scope.attribute,
                    id: scope.subject.name,
                    index: scope.$index,
                    parent: parent,
                    schema: scope.subject.id
                }), {}).success(function(data) {
                    scope.subject.form = data.form;
                    scope.subject.data = data.content;
                    scope.subject.name = data.name;

                    // If the subject's name is not an ID, it means it's a new nested content
                    // form, so don't hide it.
                    if (isNaN(parseFloat(scope.subject.name)) || !isFinite(scope.subject.name)) {
                        scope.hidden = false;
                    }
                });

                // Hide/Show the nested content form
                scope.toggle = function() {
                    if (scope.hidden == true) {
                        scope.hidden = false;
                    } else {
                        scope.hidden = true;
                    }
                }
            },
            controller: function($scope) {
                // Initially hide each form, to keep the form view clean.
                $scope.hidden = true;
            }
        };
    }])

    /**
     * Compile the passed html
     */
    .directive('compile', function ($compile) {
        return {
            restrict: 'A',
            replace: true,
            link: function (scope, element, attrs) {
                scope.$watch(attrs.compile, function(html) {
                    element.html(html);
                    $compile(element.contents())(scope);
                });
            }
        };
    })
;
