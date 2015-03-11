angular.module('OpiferContent', ['angular-inview'])

    .factory('ContentService', ['$resource', '$routeParams', function($resource, $routeParams) {
        return $resource(Routing.generate('opifer_content_api_content') + '/:id', {}, {
            index:  {method: 'GET', params: {}},
            delete: {method: 'DELETE', params: {id: $routeParams.id}}
        });
    }])

    .factory('DirectoryService', ['$resource', '$routeParams', function($resource, $routeParams) {
        return $resource(Routing.generate('opifer_content_api_directory'), {}, {
            index: {method: 'GET', isArray: true, params: {}}
        });
    }])

    /**
     * Content browser directive
     */
    .directive('contentBrowser', function() {

        return {
            restrict: 'E',
            transclude: true,
            scope: {
                name: '@',
                value: '@',
                formid: '@',
                provider: '@',
                context: '@',
                siteId: '@',
                directoryId: '@',
                //locale: '@',
                mode: '@',
                receiver: '@'
            },
            templateUrl: '/bundles/opifercontent/app/content/content.html',
            controller: function($scope, ContentService, DirectoryService, $attrs) {
                $scope.navto = false;
                $scope.maxPerPage = 25;
                $scope.currentPage = 1;
                $scope.numberOfResults = 0;
                $scope.remainingResults = 0;
                $scope.lastBrowsedResults = [];
                $scope.contents = [];
                $scope.lblPaginate = "Meer resultaten";
                $scope.query = null;
                $scope.inSearch = false;
                $scope.confirmation = {
                    shown: false,
                    name: '',
                    action: ''
                }

                if (typeof $scope.history === "undefined") {
                    $scope.history = [];
                    $scope.histPointer = 0;
                    $scope.history.push(0);
                }

                $scope.$watch('directoryId', function(newValue, oldValue) {
                    if ($scope.navto === true) {
                        if ($scope.history.length) {
                            $scope.history.splice($scope.histPointer + 1, 99);
                            $scope.histPointer++;
                            $scope.history.push(newValue);
                        } else {
                            $scope.history.push(newValue);
                            $scope.histPointer = 1;
                        }
                        $scope.navto = false;
                    }
                });


                $scope.fetchContents = function() {
                    ContentService.index({
                        site_id: $scope.siteId,
                        directory_id: $scope.directoryId,
                        //locale: $scope.locale,
                        q: $scope.query,
                        p: $scope.currentPage,
                        limit: $scope.maxPerPage
                    },
                    function(response, headers) {
                        for (var key in response.results) {
                            if (response.results[key].pivotedAttributes.coverImage) {
                                response.results[key].pivotedAttributes.coverImage = Routing.generate('liip_imagine_filter', {'path':  response.results[key].pivotedAttributes.coverImage, 'filter' : 'medialibrary'});
                            }
                            $scope.contents.push(response.results[key]);
                        }
                        $scope.numberOfResults = response.total_results;
                        $scope.lblPaginate = "Meer content (" + ($scope.numberOfResults - ($scope.currentPage * $scope.maxPerPage)) + ")";
                    });
                };

                $scope.searchContents = function() {
                    ContentService.index({
                        site_id: $scope.siteId,
                        directory_id: 0,
                        //locale: $scope.locale,
                        q: $scope.query,
                        p: $scope.currentPage,
                        limit: $scope.maxPerPage
                    },
                    function(response, headers) {
                        $scope.directorys = [];
                        $scope.contents = [];
                        for (var key in response.results) {
                            if (response.results[key].pivotedAttributes.coverImage) {
                                response.results[key].pivotedAttributes.coverImage = Routing.generate('liip_imagine_filter', {'path':  response.results[key].pivotedAttributes.coverImage, 'filter' : 'medialibrary'});
                            }
                            $scope.contents.push(response.results[key]);
                        }
                        $scope.numberOfResults = response.total_results;
                        $scope.lblPaginate = "Meer content (" + ($scope.numberOfResults - ($scope.currentPage * $scope.maxPerPage)) + ")";
                    });
                };

                $scope.$watchCollection('[query]', _.debounce(function() {
                    if ($scope.query) {
                        $scope.currentPage = 1;
                        $scope.inSearch = true;
                        $scope.searchContents();
                    } else if ($scope.inSearch) {
                        $scope.clearSearch();
                    }
                }, 300));
                $scope.fetchContents();

                $scope.fetchDirectorys = function() {
                    DirectoryService.index({
                        site_id: $scope.siteId,
                        directory_id: $scope.directoryId,
                        //locale: $scope.locale
                    },
                    function(directorys) {
                        $scope.directorys = directorys;
                    });
                };
                $scope.fetchDirectorys();

                $scope.reloadContents = function() {
                    $scope.contents = [];
                    $scope.currentPage = 1;
                    $scope.fetchContents();
                    $scope.fetchDirectorys();
                };

                $scope.clearSearch = function() {
                    $scope.query = null;
                    $scope.inSearch = false;
                    $scope.currentPage = 1;
                    $scope.contents = [];
                    $scope.fetchContents();
                    $scope.fetchDirectorys();
                };

                $scope.deleteContent = function(id) {
                    angular.forEach($scope.contents, function(c, index) {
                        if (c.id === id) {
                            ContentService.delete({id: c.id}, function() {
                                $scope.contents.splice(index, 1);
                            });
                        }
                    });

                    $scope.confirmation.shown = false;
                };

                $scope.confirmDeleteContent = function(idx, $event) {
                    var selected = $scope.contents[idx];

                    $scope.confirmation.idx = selected.id;
                    $scope.confirmation.name = selected.title;
                    $scope.confirmation.dataset = $event.currentTarget.dataset;
                    $scope.confirmation.action = $scope.deleteContent;
                    $scope.confirmation.shown = !$scope.confirmation.shown;
                };

                $scope.previous = function() {
                    if ($scope.history.length) {
                        $scope.directoryId = $scope.history[$scope.histPointer - 1];
                        $scope.histPointer--;
                        $scope.reloadContents();
                    }
                };

                $scope.next = function() {
                    if ($scope.history.length) {
                        $scope.directoryId = $scope.history[$scope.histPointer + 1];
                        $scope.histPointer++;
                        $scope.reloadContents();
                    }
                };

                $scope.navigateToDirectory = function(directory) {
                    $scope.navto = true;
                    $scope.directoryId = directory.id;
                    $scope.numberOfResults = $scope.maxPerPage - 1; // prevent infinitescrolling
                    $scope.reloadContents();
                };

                $scope.editContent = function(id) {
                    window.location = Routing.generate('opifer_content_content_edit', {'id': id});
                };
                
                $scope.copyContent = function(id) {
                    window.location = Routing.generate('opifer_content_content_duplicate', {'id': id});
                };
                
                $scope.confirmCopyContent = function(idx, $event) {
                    var selected = $scope.contents[idx];

                    $scope.confirmation.idx = selected.id;
                    $scope.confirmation.name = selected.title;
                    $scope.confirmation.dataset = $event.currentTarget.dataset;
                    $scope.confirmation.action = $scope.copyContent;
                    $scope.confirmation.shown = !$scope.confirmation.shown;
                };

                $scope.loadMore = function(e) {
                    if ($scope.remainingResults == 0) return;
                    $scope.currentPage++;
                    $scope.lblPaginate = "Laden…";
                    $scope.fetchContents();
                };

                $scope.pickObject = function(contentId) {
                    $scope.$parent.pickObject(contentId);
                };

                $scope.unpickObject = function(contentId) {
                    $scope.$parent.unpickObject(contentId);
                };

                $scope.hasObject = function(contentId) {
                    if (angular.isUndefined($scope.$parent.subject.right.value)) {
                        return false;
                    }
                    var idx = $scope.$parent.subject.right.value.indexOf(contentId);

                    return (idx >= 0) ? true : false;
                };
            },
            compile: function(element, attrs ){
               if (!attrs.mode) { attrs.mode = 'ADMIN'; }
            }
        };
    })
;
