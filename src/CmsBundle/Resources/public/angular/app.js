'use strict';

/* App Module */

angular.module('MainApp', [
    'chieffancypants.loadingBar',
    'ngRoute',
    'ngResource',
    'mediaLibrary',
    'OpiferContent',
    'ui.sortable',
    'afkl.lazyImage',
    'angularFileUpload'
]);

angular
    .module('MainApp')
    .controller('CKEditorController', ['$scope', '$rootScope', 'MediaService', function($scope, $rootScope, MediaService) {
        $scope.funcNum = false;
        $scope.type = false;

        var selectMediaEvent = $rootScope.$on('mediaLibrary.selectMedia', function(event, media) {
            MediaService.get({id: media.id}, function(response) {
                var location;
                // In case of images, do not pass the original file, but a cached/resized one.
                if ($.inArray(response.contentType, ['image/png', 'image/jpeg', 'image/gif']) > -1) {
                    location = response.images.inline;
                } else {
                    location = response.original;
                }

                // If the user is trying to add a link to the file, strip the protocol
                if ($scope.type == 'link') {
                    location = location.replace(/.*?:\/\//g, "");
                }

                window.opener.CKEDITOR.tools.callFunction( $scope.funcNum, location, function() {
                    // Get the reference to a dialog window.
                    var element, dialog = this.getDialog();
                    // Check if this is the Image dialog window.
                    if ( dialog.getName() == 'image' ) {
                        // Get the reference to a text field that holds the "alt" attribute.
                        element = dialog.getContentElement( 'info', 'txtAlt' );
                        // Assign the new value.
                        if ( element ) {
                            element.setValue( response.name );
                        }
                    }
                });

                window.close();
            });
        });

        $scope.$on('$destroy', selectMediaEvent);

        var pickContentEvent = $rootScope.$on('contentPicker.pickContent', function(event, content) {
            window.opener.CKEDITOR.tools.callFunction( $scope.funcNum, content.path );
            window.close();
        });

        $scope.$on('$destroy', pickContentEvent);
    }
]);
