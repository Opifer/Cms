
angular.module('mediaLibrary', ['infinite-scroll', 'ngModal', 'angularFileUpload'])

    /**
     * Media Service
     */
    .factory('MediaService', ['$resource', '$routeParams', function($resource, $routeParams) {
        return $resource(Routing.generate('opifer_api_media') + '/:id', {}, {
            index: {method: 'GET', cache: true, params: {}}
        });
    }])

    /**
     * The media library Controller
     */

    .controller('MediaLibraryController', ['$scope', '$rootScope', '$http', '$location', '$upload', '$window', 'MediaCollection', 'MediaService', function($scope, $rootScope, $http, $location, $upload, $window, MediaCollection, MediaService) {
        $scope.mediaCollection = new MediaCollection();
        $scope.selecteditems = [];
        $scope.searchmedia = '';
        $scope.type = 'default';
        $scope.uploader = {
            shown: false
        };
        $scope.confirmation = {
            shown: false,
            name: ''
        };
        $scope.picker = {
            pickerShown: false,
            name: "",
            multiple: false
        };

        /**
         * Initialize the media library
         *
         * @param  {string} type
         * @param  {array} providers
         * @param  {string} name
         * @param  {array} items
         * @param  {boolean} multiple
         *
         * @return {void}
         */
        $scope.init = function(type, providers, name, items, multiple, defer) {
            $scope.type = type;
            $scope.picker.multiple = multiple;
            $scope.picker.name = name;

            if (typeof defer === 'undefined' || defer === true) {
                // Do a first load of media items when the controller is setup
                $scope.mediaCollection.loadMore();
            }

            if (angular.isDefined(providers) && providers.length) {
                $scope.mediaCollection.addProviders(providers);
            }

            // When items have been passed to the init function, retrieve the related data.
            if (angular.isDefined(items) && items.length) {
                items = JSON.parse(items);
                items = items.toString();
                if (items) {
                    MediaService.index({ids: items}, function(response, headers) {
                        var results = response.results;

                        for (var i = 0; i < results.length; i++) {
                            $scope.selecteditems.push(results[i]);
                        }
                    });
                }
            }
        };

        $scope.dropSupported = true;

        /**
         * Upload the dropped files
         *
         * @param  {array} $files
         */
        $scope.onFileSelect = function($files) {
            $scope.uploadingFiles = $files;
            $scope.progress = [];
            $scope.upload = [];

            for (var i = 0; i < $files.length; i++) {
                $scope.startUpload(i);
            }
        };

        /**
         * Start the upload progress
         *
         * @param  {integer} index
         */
        $scope.startUpload = function(index) {
            $scope.progress[index] = 0;

            $scope.upload[index] = $upload.upload({
                url: Routing.generate('opifer_api_media_upload'),
                method: 'POST',
                data: {},
                file: $scope.uploadingFiles[index],
            }).progress(function(evt) {
                // Update the upload progress
                $scope.progress[index] = parseInt(100.0 * evt.loaded / evt.total);
            }).success(function(data, status, headers, config) {
                // file is uploaded successfully
                data.image = Routing.generate('liip_imagine_filter', {'path': data.reference, 'filter' : 'medialibrary'});

                // Add the image to the media collection
                $scope.mediaCollection.items.push(data);
            });
        };

        /**
         * Toggle the picker
         */
        $scope.togglePicker = function() {
            $scope.mediaCollection.loadMore('');
            $scope.picker.pickerShown = !$scope.picker.pickerShown;
        };

        /**
         * Toggle the uploader
         */
        $scope.toggleUploader = function(provider) {
            $scope.uploader.provider = provider;
            $scope.uploader.shown = !$scope.uploader.shown;
        };

        /**
         * Select a media item
         *
         * @param  {integer} id
         */
        $scope.selectMedia = function(id) {
            var selected = $scope.mediaCollection.items[id];

            $rootScope.$emit('mediaLibrary.selectMedia', selected);

            if ($scope.type == 'picker') {
                if (selected.provider == 'youtube') {
                    var reference = selected.thumb.reference;
                } else {
                    var reference = selected.reference;
                }

                selected.image = Routing.generate('liip_imagine_filter', {'path': reference, 'filter' : 'medialibrary'});

                // Check if the selected item is not already part of the selecteditems
                var pos = $scope.selecteditems.map(function(item) { return item.id; }).indexOf(selected.id);
                if (pos == -1) {
                    $scope.selecteditems.push(selected);
                }

                $scope.picker.pickerShown = false;
            } else {
                window.location = Routing.generate('opifer_media_media_edit', {'id': selected.id});
            }
        };

        $scope.saveMedia = function(idx) {
            var data = $scope.selecteditems[idx];

            $http.put(Routing.generate('opifer_media_api_update'), data)
                .success(function(data) {
                    //console.log('success');
                }
            );
        };

        /**
         * Remove media
         *
         * Removes a media item from the selected media items
         */
        $scope.removeMedia = function(idx) {
            $scope.selecteditems.splice(idx, 1);
        };

        /**
         * Confirm deletion
         *
         * Prompts a confirmation modal
         */
        $scope.confirmDelete = function(idx) {
            var selected = $scope.mediaCollection.items[idx];

            $scope.confirmation.idx = idx;
            $scope.confirmation.name = selected.name;
            $scope.confirmation.shown = !$scope.confirmation.shown;
        };

        $scope.editMedia = function(idx) {
            var selected = $scope.mediaCollection.items[idx];

            $window.location.href = Routing.generate('opifer_media_media_edit', {'id': selected.id});
        };

        /**
         * Delete media
         *
         * Completely removes a media item from the filesystem.
         */
        $scope.deleteMedia = function(idx) {
            var selected = $scope.mediaCollection.items[idx];

            $http.delete(Routing.generate('opifer_api_media_delete', {'id': selected.id}))
                .success(function(data) {
                    if (data.success == true) {
                        $scope.mediaCollection.items.splice(idx, 1);
                    }
                }
            );

            // Reset confirmation defaults
            $scope.confirmation.shown = false;
        };
    }])

    /**
     * MediaCollection
     *
     * Holds all loaded medialibrary items
     *
     * @param   {http}  $http
     *
     * @return  {object}
     */
    .factory('MediaCollection', function($http) {

        var MediaCollection = function() {
            this.items = [];
            this.providers = [];
            this.busy = false;
            this.end = false;
            this.page = 1;
            this.orderBy = 'createdAt';
            this.orderDir = 'desc';
            this.search = '';
        };

        /**
         * Add providers to the collection
         */
        MediaCollection.prototype.addProviders = function(providers) {
            providers = JSON.parse(providers);

            angular.forEach(providers, function(provider, key) {
                provider.newlink = Routing.generate('opifer_media_media_new', {'provider': key});
                provider.name = key;

                this.push(provider);
            }, this.providers);
        }

        /**
         * Load more media to the collection
         */
        MediaCollection.prototype.loadMore = function(search) {
            if (this.busy) {
                return;
            }

            this.busy = true;

            // When it's a search, reset all data, to make sure we don't have
            // any unrelated items inside the search results
            if (angular.isDefined(search)) {
                this.end = false;
                this.items = [];
                this.page = 1;
            }

            // Return when the last page is reached
            if (this.end) {
                this.busy = false;
                return;
            }

            // Retrieve more items and add them to the already loaded items
            $http.get(Routing.generate('opifer_api_media', {'page': this.page, 'search': this.search, 'order': this.orderBy, 'orderdir': this.orderDir})).success(function(data) {

                this.items = this.items.concat(data.results);

                if ((data.total_results / data.results_per_page) <= this.page) {
                    this.end = true;
                }

                this.page = this.page + 1;
                this.busy = false;
            }.bind(this));
        };

        return MediaCollection;
    })

    /**
     * ng-media-library Directive
     *
     * Loads the infinite scrollable media library
     *
     * @return  {object}
     */
    .directive('ngMediaLibrary', function() {
        return {
            templateUrl: "/bundles/opifermedia/app/medialibrary/medialibrary.html",
            restrict: 'E'
        };
    })
;
