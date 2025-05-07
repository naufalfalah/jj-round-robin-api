@push('scripts-head')
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDD91Q_orsd-0xev9rzzSyE9ANZs5mfxtU&libraries=geometry,marker"></script>
@endpush

@push('styles')
    <style>
        #map {
            height: 50vh;
        }
    </style>
@endpush

<div class="modal fade" id="locationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="form-group mb-4">
                            <div class="form-check-inline">
                                <input class="form-check-input" type="radio" name="location_advance" id="location-advance-name" value="LOCATION" checked>
                                <label class="form-check-label" for="location-advance-name">
                                    Location
                                </label>
                            </div>
                            <div class="form-check-inline">
                                <input class="form-check-input" type="radio" name="location_advance" id="location-advance-radius" value="RADIUS">
                                <label class="form-check-label" for="location-advance-radius">
                                    Radius
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-4" id="location-name-section">
                            <select class="form-control location-select">
                            </select>
                        </div>
                        <div class="form-group mb-4" id="location-radius-section">
                            <div class="row">
                                <div class="col-xl-8">
                                    <select class="form-control location-select">
                                    </select>
                                </div>
                                <div class="col-6 col-xl-2">
                                    <input type="number" name="radius" id="radius" class="form-control" value="{{ old('radius', 10) }}" required>
                                </div>
                                <div class="col-6 col-xl-2">
                                    <select class="form-control" name="length" id="length">
                                        <option value="mi">mi</option>
                                        <option value="km">km</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="border p-2 radius-5 location-result" id="location-result">
                            <p>Locations</p>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <button type="button" class="btn btn-primary mb-2" id="pinLocation">Pin Location</button>
                        <div id="map"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        let map;
        let marker;
        let circle;
        let radiusInMeters = 10 * 1609.34; // default value 10 mil
        let locationName;
        let radiusInput = $('#radius').val();
        let lengthUnit = $('#length').val();

        function isRadiusChecked() {
            return $('#location-advance-radius').is(':checked');
        }

        function updateCircleRadius() {
            radiusInput = $('#radius').val();
            lengthUnit = $('#length').val();

            if (lengthUnit === 'mi') {
                radiusInMeters = radiusInput * 1609.34;  // 1 mile = 1609.34 meters
            } else {
                radiusInMeters = radiusInput * 1000;  // 1 kilometer = 1000 meters
            }

            circle.setRadius(radiusInMeters);
        }

        function updateDataDisplay() {
            const position = marker.getPosition();
            $('#data').text(`Lat: ${position.lat()}, Lng: ${position.lng()}`);
        }

        function getMarkerData() {
            radiusInput = $('#radius').val();
            lengthUnit = $('#length').val();
            const position = marker.getPosition();
            const latitude = position.lat();
            const longitude = position.lng();

            const locationString = `${radiusInput} ${lengthUnit} around (${latitude.toFixed(6)}, ${longitude.toFixed(6)}) (custom) radius`;

            $('.location-result').append(
                `<div class="border p-2 mb-2 d-flex justify-content-between" data-value="(${latitude.toFixed(6)}, ${longitude.toFixed(6)})">
                    <span>${locationString}</span>
                    <button class="remove-btn border-0 radius-10">x</button>
                </div>`
            );
        }

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 1.352083, lng: 103.819836},
                zoom: 10
            });

            marker = new google.maps.Marker({
                position: {lat: 1.352083, lng: 103.819836},
                map: map,
                draggable: true
            });

            circle = new google.maps.Circle({
                map: map,
                radius: 10000,  // default radius in meters
                fillColor: '#AA0000',
                fillOpacity: 0.35,
                visible: false  // initially hidden
            });
            circle.bindTo('center', marker, 'position');

            marker.addListener('position_changed', function() {
                map.panTo(marker.position);
            });
            
            circle.setRadius(radiusInMeters)

            // Add event listener for location_advance radio buttons
            $('input[name="location_advance"]').on('change', function() {
                if ($(this).val() === 'RADIUS') {
                    circle.setVisible(true);
                } else {
                    circle.setVisible(false);
                }
            });

            $('.location-select').on('change', function() {
                var selectedValue = $(this).val();
                var selectedOption = $(this).find('option:selected');
                var selectedData = selectedOption.data('location-name');
                var selectedLocation;
                
                if (isRadiusChecked()) {
                    selectedLocation = `${radiusInput} ${lengthUnit} around ${selectedData}`;
                } else {
                    selectedLocation = selectedData;
                }

                if (selectedValue) {
                    $('.location-result').append(
                        `<div class="border p-2 mb-2 d-flex justify-content-between" data-value="${selectedValue}">
                            <span>${selectedLocation}</span>
                            <button class="remove-btn border-0 radius-10">x</button>
                        </div>`
                    );
                    $('#locationData').append(
                        `<input type="hidden" name="locations[]" value="${selectedValue}" data-value="${selectedValue}">`
                    )
                }
            });

            $('.location-result').on('click', '.remove-btn', function() {
                var valueToRemove = $(this).parent().data('value');
                $('.location-result').each(function() {
                    $(this).find(`[data-value="${valueToRemove}"]`).remove();
                });
                $('#locationData').each(function() {
                    $(this).find(`[data-value="${valueToRemove}"]`).remove();
                });
            });

            // Add event listener for radius and length changes
            $('#radius, #length').on('input change', updateCircleRadius);

            $('#pinLocation').on('click', getMarkerData);
        }

        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
@endpush
