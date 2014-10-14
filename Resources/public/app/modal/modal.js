
angular.module('ngModal', [])

    /**
     * ng-modal Directive
     *
     * Loads the picker modal.
     * All modal content should be placed within this directive. E.g.;
     * <ng-modal>modal content</ng-modal>
     */
    .directive('ngModal', ['$sce', function($sce) {

        function link(scope, element, attrs) {
            var setupCloseButton, setupStyle;

            setupStyle = function() {
                scope.dialogStyle = {};
                if (attrs.width) {
                    scope.dialogStyle['width'] = attrs.width;
                }
                if (attrs.height) {
                    return scope.dialogStyle['height'] = attrs.height;
                }
            };

            scope.hidePicker = function() {
                return scope.show = false;
            };

            scope.$watch('show', function(newVal, oldVal) {
                if (newVal && !oldVal) {
                    document.getElementsByTagName("body")[0].style.overflow = "hidden";
                } else {
                    document.getElementsByTagName("body")[0].style.overflow = "";
                }
                if ((!newVal && oldVal) && (scope.onClose != null)) {
                    return scope.onClose();
                }
            });

            scope.closeButtonHtml = $sce.trustAsHtml("<span class='ng-modal-close-x'>X</span>");

            return setupStyle();
        }

        return {
            restrict: 'E',
            scope: {
                show: '=',
                onClose: '&?'
            },
            replace: true,
            transclude: true,
            link: link,
            templateUrl: "/bundles/opifermedia/app/modal/modal.html"
        };
    }])
;
