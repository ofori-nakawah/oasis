@extends("layouts.master")

@section('title')
    Volunteer
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-users"></em> Volunteer </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">Organise</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a href="{{URL::previous()}}"
                   class="btn btn-outline-primary"><span>Back</span></a></li>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <form action="{{route('user.volunteerism.publish')}}" method="POST">
                {{csrf_field()}}
                <div class="">
                    <div class="mb-3">
                        <h2><b>Create your project</b></h2>
                    </div>
                    <div class="card-body1">
                        <div class="input-group1 mb-3">
                            <label for="name"><b>Activity Name</b></label>
                            <input type="text" class="form-control form-control-l @error('name') is-invalid @enderror" placeholder="Enter name of activity or project" name="name" value="{{ old('name') }}">

                            @error('name')
                            <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="description"><b>Description</b></label>
                            <textarea class="form-control form-control-l @error('description') is-invalid @enderror" placeholder="Enter brief description of the project" name="description">{{old('description')}}</textarea>

                            @error('description')
                            <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="date"><b>Date</b></label>
                                    <input type="date" class="form-control date form-control-l @error('date') is-invalid @enderror" placeholder="Select date of activity" value="{{ old('date') }}" name="date">

                                    @error('date')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="time"><b>Time</b></label>
                                    <input type="time" class="form-control form-control-l @error('time') is-invalid @enderror" placeholder="Select time of activity" name="time" value="{{ old('time') }}">

                                    @error('time')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="number_of_participants"><b>Number of Participants</b></label>
                                    <input type="number" class="form-control form-control-l @error('maximum_number_of_volunteers') is-invalid @enderror" placeholder="Specify number of participants" value="{{ old('maximum_number_of_volunteers') }}" name="maximum_number_of_volunteers">

                                    @error('maximum_number_of_volunteers')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="name"><b>Volunteer Hours</b></label>
                                    <input type="number" class="form-control form-control-l @error('volunteer_hours') is-invalid @enderror" placeholder="Specify volunteer hours" name="volunteer_hours" value="{{ old('volunteer_hours') }}">

                                    @error('volunteer_hours')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="name"><b>Location</b></label>
                            <input type="text" class="form-control form-control-l @error('location') is-invalid @enderror" placeholder="Provide location of activity" readonly id="location" value="{{ old('location') }}" name="location" data-toggle="modal" data-target="#locationModal">
                            <input type="hidden" value="{{old('onboardingLocationName')}}" name="location" id="onboardingLocationName">
                            <input type="hidden" value="{{old('onboardingLocationCoords')}}" name="coords" id="onboardingLocationCoords">
                            @error('location')
                            <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <!-- Modal Content Code -->
                        <div class="modal modal-lg fade" tabindex="-1" id="locationModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                        <em class="icon ni ni-cross"></em>
                                    </a>
                                    <div class="modal-header">
                                        <h5 class="modal-title">Choose location of activity</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-control-wrap">
                                                    <div class="form-icon form-icon-left">
                                                        <em class="icon ni ni-search"></em>
                                                    </div>
                                                    <input type="text" class="form-control form-control-lg" id="pac_input"
                                                           placeholder="Search location closest to you" style="z-index: 999;">
                                                </div>
                                                <span class="help-block">
                                               <small><em class="icon ni ni-info"></em> The location in the search box is what we are going to proceed with. Ensure that is correct before proceeding.</small>
                                            </span>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 15px;">
                                            <div class="col-md-12 text-right">
                                                <a href="#" id="useMyCurrentLocation" class="link"><b><i class="icon ni ni-map-pin-fill"></i> <span
                                                            style="font-size: 16px;">Use my current location</span></b></a>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 15px;">
                                            <div class="col-md-12">
                                                <div class="card card-bordered" style="padding: 0px;">
                                                    <div id="locationMap" style="height: 350px;"></div>
                                                    <div id="mapLoading" style="height: 350px;">
                                                        <div id="mapLoadingContent"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer bg-white text-right" style="float: right !important;">
                                        <a href="#" role="button" style="float: right;" class="btn btn-outline-primary" onclick="updateLocationInput()"><b>Continue</b></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="other_relevant_information"><b>Other Relevant Information</b></label>
                            <textarea type="tel" class="form-control form-control-l @error('other_relevant_information') is-invalid @enderror" placeholder="Specify any other relevant information" name="other_relevant_information">{{old('other_relevant_information')}}</textarea>

                            @error('other_relevant_information')
                            <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                    </div>
                    <div class="text-right">
                        <button class="btn btn-success btn-l"><b>Create & Publish</b></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section("scripts")
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{env("GOOGLE_MAPS_KEY")}}&libraries=places&callback=mountMap"
        async defer></script>
    <script>
        var markers = [];
        $('#mapLoading').hide();

        function mountMap() {
            var mapOptions = {
                center: {
                    lat: 5.6316733,
                    lng: -0.3290127
                },
                zoom: 14,
                componentRestrictions: {country: "gh"}
            };
            var map = new google.maps.Map(document.getElementById("locationMap"), mapOptions);
            this.map = map;

            var input = document.getElementById('pac_input');
            var autocomplete = new google.maps.places.Autocomplete(input, mapOptions);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();
                const _latlng = new google.maps.LatLng(place.geometry.location.lat(), place.geometry.location.lng());

                $('#mapLoading').hide()
                $('#locationMap').show()

                /**
                 * move the map focus to the user's current location
                 */
                map.panTo(_latlng)

                /**
                 * show marker of user's current location
                 * @type {google.maps.Marker}
                 */
                deleteMarkers()
                const marker = new google.maps.Marker({
                    position: _latlng,
                    map: map,
                });
                markers.push(marker);

                /**
                 * setup form fields
                 **/
                $('#onboardingLocationName').val(place.name)
                $('#onboardingLocationCoords').val(`${place.geometry.location.lat()}, ${place.geometry.location.lng()}`)

                /**
                 *
                 * enable submit button
                 **/
                $('#locationOnboardingSubmitBtn').attr("disabled", false)
            });

            $(document).ready(function () {
                $(document).on("click", "#useMyCurrentLocation", function (e) {
                    e.preventDefault();

                    if ($(this).hasClass('clicked')) {
                        return false;
                    } else {
                        $(this).addClass('clicked').trigger('click');
                    }

                    if (navigator.geolocation) {
                        var options = {
                            enableHighAccuracy: true,
                            timeout: Infinity,
                            maximumAge: 0
                        };

                        navigator.geolocation.getCurrentPosition(showPosition, showError, options);
                        const fetchUserLocationLoaderContent = `<div class="text-center">
                            <img src="{{asset('assets/html-template/src/images/search_location.svg')}}" style="height: 150px; width: 150px;margin-top: 30px;" alt="fetch user location">
                            <p style="font-size: 11px;color: #777">Getting your current location. This will only take a moment</div><br>
                            <div id="loader_" class="d-flex justify-content-center">
                              <div class="spinner-grow" role="status">
                                <span class="sr-only">Loading...</span>
                              </div>
                            </div>
                        </div>`

                        $('#locationMap').hide()
                        $('#mapLoadingContent').html('')
                        $('#mapLoadingContent').append(fetchUserLocationLoaderContent)
                        $('#mapLoading').show()

                        function showPosition(position) {
                            console.log('Latitude: ' + position.coords.latitude + ' Longitude: ' + position.coords.longitude);
                            const _latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                            const icon = 'http://www.google.com/intl/en_us/mapfiles/ms/micons/green-dot.png';
                            let userCurrentLocationName = null;

                            var geocoder;
                            geocoder = new google.maps.Geocoder();
                            geocoder.geocode(
                                {'latLng': _latlng},
                                function (results, status) {
                                    if (status == google.maps.GeocoderStatus.OK) {
                                        $('#loader_').html("")

                                        userCurrentLocationName = results[0].formatted_address.toString();
                                        const userCurrentLocationContent = `<div>
                                <p class="text-center">You are currently located at <br> <b><span style="font-size: 16px">${userCurrentLocationName}</span></b></p><br>
                                <div class="text-center"><a href="#" role="button" id="showUserCurrentLocationOnMap" class="btn btn-outline-secondary"><b>Great! Show me on the map</b></a></div>
                            </div>`
                                        $('#mapLoadingContent').append(userCurrentLocationContent)

                                        $('#pac_input').val(userCurrentLocationName)

                                        /**
                                         * setup form fields
                                         **/
                                        $('#onboardingLocationName').val(userCurrentLocationName)
                                        $('#onboardingLocationCoords').val(`${position.coords.latitude}, ${position.coords.longitude}`)

                                        /**
                                         *
                                         * enable submit button
                                         **/
                                        $('#locationOnboardingSubmitBtn').attr("disabled", false)

                                    } else {
                                        userCurrentLocationName = "Oops...We could not find your location due to: " + status;
                                        $('#mapLoadingContent').append(userCurrentLocationContent)
                                    }
                                }
                            );

                            /**
                             * show marker of user's current location
                             * @type {google.maps.Marker}
                             */
                            deleteMarkers()
                            const marker = new google.maps.Marker({
                                position: _latlng,
                                map: map,
                            });
                            markers.push(marker);

                            /**
                             * move the map focus to the user's current location
                             */
                            map.panTo(_latlng)


                            /**
                             * show the user's current location on the map
                             */
                            $(document).on("click", "#showUserCurrentLocationOnMap", function (e) {
                                $('#mapLoading').hide()
                                $('#locationMap').show()
                            })
                        }

                        function showError(error) {
                            switch (error.code) {
                                case error.PERMISSION_DENIED:
                                    alert('User denied the request for Geolocation.')
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    alert('Error getting user location.')
                                    break;
                                case error.TIMEOUT:
                                    alert('The request to get user location timed out.')
                                    break;
                                case error.UNKNOWN_ERROR:
                                    alert('An unknown error occurred.')
                                    break;
                            }
                        }
                    }
                });

                new google.maps.event.addListener(map, "click", function (event) {
                    var latitude = event.latLng.lat();
                    var longitude = event.latLng.lng();
                    const _latlng = new google.maps.LatLng(latitude, longitude);

                    var geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        'latLng': event.latLng

                    }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[0]) {
                                /**
                                 * show marker of user's current location
                                 * @type {google.maps.Marker}
                                 */
                                deleteMarkers()
                                const marker = new google.maps.Marker({
                                    position: _latlng,
                                    map: map,
                                });
                                markers.push(marker);
                                $("#pac_input").val(results[0].formatted_address);

                                /**
                                 * setup form fields
                                 **/
                                $('#onboardingLocationName').val(results[0].formatted_address)
                                $('#onboardingLocationCoords').val(`${latitude}, ${longitude}`)

                                /**
                                 *
                                 * enable submit button
                                 **/
                                $('#locationOnboardingSubmitBtn').attr("disabled", false)
                            }
                        }
                    });

                    /**
                     * move the map focus to the user's current location
                     */
                    map.panTo(_latlng)
                });
            });
        }

        function deleteMarkers() {
            //Loop through all the markers and remove
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
        }

        function updateLocationInput() {
            $("#location").val($("#pac_input").val());
            $('#locationModal').modal('hide');
        }

        $(function(){
            var today = new Date();

            var month = today.getMonth() + 1;
            var day = today.getDate();
            var year = today.getFullYear();
            if(month < 10)
                month = '0' + month.toString();
            if(day < 10)
                day = '0' + day.toString();
            var minDate = year + '-' + month + '-' + day;
            $('.date').attr('min', minDate);
        });
    </script>
@endsection
