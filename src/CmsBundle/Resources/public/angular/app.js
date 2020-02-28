'use strict';

/* App Module */

angular.module('MainApp', [
    'chieffancypants.loadingBar',
    'ngRoute',
    'ngResource',
    'OpiferContent',
    'ui.sortable',
    'afkl.lazyImage',
    'translationApp'
]);

angular
    .module('MainApp')
    .controller('CKEditorController', ['$scope', '$rootScope', function($scope, $rootScope) {
        $scope.funcNum = false;
        $scope.type = false;

        var pickContentEvent = $rootScope.$on('contentPicker.pickContent', function(event, content) {
            // Pass the content ID to the URL field, so we can parse it later
            window.opener.CKEDITOR.tools.callFunction( $scope.funcNum, '[content_url]'+content.id+'[/content_url]', function() {
                var dialog = this.getDialog();
                if (dialog.getName() == 'link') {
                    // Clear the protocol, since we will replace the URL with the actual url + protocol.
                    dialog.setValueOf('info','protocol','');
                }
            });
            window.close();
        });

        $scope.$on('$destroy', pickContentEvent);
    }
]);
