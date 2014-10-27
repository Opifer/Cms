'use strict';

angular.module('googleAddress', ['google-maps'])

    .controller('searchAddress', ['$scope', function($scope) {
        $scope.location = '';
        $scope.formid = '';
    }])

    // Renders a searchfield and google map to search addresses
    .directive('googlePlaces', function(){
        return {
            restrict: 'E',
            replace: true,
            transclude: true,
            templateUrl: "/bundles/opifereav/app/googleaddress/googleaddress.html",
            scope: {
                location: '=',
                formid: '@',
                value: '@',
                lat: '@',
                lng: '@'
            },
            controller: function($scope) {
                // Set google map scopes here to be able to access them on map load.
                $scope.map = {
                    center: {
                        latitude: $scope.lat,
                        longitude: $scope.lng
                    },
                    zoom: 16
                }
                $scope.marker = {
                    coords: {
                        latitude: $scope.lat,
                        longitude: $scope.lng
                    }
                }
            },
            link: function($scope, elm, attrs){
                // Autocomplete listener. Will be executed when an address is chosen
                var autocomplete = new google.maps.places.Autocomplete($("#google_places_ac")[0], {});
                google.maps.event.addListener(autocomplete, 'place_changed', function() {
                    var place = autocomplete.getPlace();

                    // Transform address_components to a more readable array
                    var components = [];
                    components['lat'] = place.geometry.location.lat();
                    components['lng'] = place.geometry.location.lng();
                    for (var i = 0; i < place.address_components.length; i++) {
                        if (place.address_components[i].types[0] == 'country') {
                            components[place.address_components[i].types[0]] = place.address_components[i]['short_name'];
                        } else {
                            components[place.address_components[i].types[0]] = place.address_components[i]['long_name'];
                        }
                    }

                    // Avoid passing an 'undefined' string when the values are empty, so
                    // we can validate them in the form.
                    var street = (components['route']) ? components['route'] : '';
                    var street_number = (components['street_number']) ? components['street_number'] : '';
                    var zipcode = (components['postal_code']) ? components['postal_code'] : '';
                    var country = (components['country']) ? components['country'] : '';
                    var city = (components['locality']) ? components['locality'] : '';
                    var lat = (components['lat']) ? components['lat'] : '';
                    var lng = (components['lng']) ? components['lng'] : '';

                    // Set values to hidden form fields
                    angular.element('#'+$scope.formid+'_city').val(city);
                    angular.element('#'+$scope.formid+'_street').val(street + ' ' + street_number);
                    angular.element('#'+$scope.formid+'_zipcode').val(zipcode);
                    angular.element('#'+$scope.formid+'_country').val(country);
                    angular.element('#'+$scope.formid+'_lat').val(lat);
                    angular.element('#'+$scope.formid+'_lng').val(lng);

                    // Change google map coordinates
                    $scope.map.center.latitude = components['lat'];
                    $scope.map.center.longitude = components['lng'];
                    $scope.marker.coords.latitude = components['lat'];
                    $scope.marker.coords.longitude = components['lng'];

                    $scope.location = components['lat'] + ',' + components['lng'];
                    $scope.$apply();
                });
            }
        }
    })
;
