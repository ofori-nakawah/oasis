@extends("layouts.master")

@section('title')
    Work
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-briefcase"></em> Work </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">Quick Job</a></li>
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
            <form action="{{route('user.quick_jobs.publish')}}" method="POST">
                {{csrf_field()}}
                <div class="">
                    <div class="mb-3">
                        <h2><b>Create your opportunity</b></h2>
                    </div>
                    <div class="card-body1">
                        <div class="input-group1 mb-3">
                            <label for="name"><b>Select Category</b></label>
                            <select type="text" class="form-control form-control-l @error('category') is-invalid @enderror" name="category">
                                <option value="">Choose an option</option>
                                @foreach($categories as $category)
                                    <option value="{{$category->name}}">{{$category->name}}</option>
                                @endforeach
                            </select>

                            @error('category')
                            <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="description"><b>Brief Job Description</b></label>
                            <textarea class="form-control form-control-l @error('description') is-invalid @enderror" placeholder="Enter brief description of the project" name="description"></textarea>

                            @error('description')
                            <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="card card-bordered" style="height: 180px;margin-bottom: 15px;border-style: dotted;cursor: pointer;">
                            <div id="imageInputTrigger" class="text-center" style="padding: 15px;" onclick="document.getElementById('image_attachment').click();">
                                <img src="{{asset('assets/html-template/src/images/photo.svg')}}" style="height: 120px;" alt="">
                                <p class="text-muted">Add image attachment</p>
                            </div>
                            <img id="imageAttachment" style="height: 180px;"></img>
                            <input type="file" accept="image/png, image/gif, image/jpeg, image/jpg" hidden name="image_attachment" id="image_attachment" onchange="displayImageAttachment(event)">
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="date"><b>Date</b></label>
                                    <input type="date" class="form-control form-control-l @error('date') is-invalid @enderror" placeholder="Select date of activity" name="date">

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
                                    <input type="time" class="form-control form-control-l @error('time') is-invalid @enderror" placeholder="Select time of activity" name="time">

                                    @error('time')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="name"><b>Location</b></label>
                            <input type="text" class="form-control form-control-l @error('location') is-invalid @enderror" placeholder="Provide location of activity" readonly id="location" name="location" data-toggle="modal" data-target="#locationModal">
                            <input type="hidden" name="location" id="onboardingLocationName">
                            <input type="hidden" name="coords" id="onboardingLocationCoords">
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

                        <p><b>Employer Budget Range</b></p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                                                        <label for="number_of_participants">Min Budget (GHS)</label>
                                    <input type="number" class="form-control form-control-l @error('min_budget') is-invalid @enderror" placeholder="Min Budget (GHS)" name="min_budget">

                                    @error('min_budget')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                                                        <label for="name">Max Budget (GHS)</label>
                                    <input type="number" class="form-control form-control-l @error('max_budget') is-invalid @enderror" placeholder="Maximum Budget (GHS)" name="max_budget">

                                    @error('max_budget')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-control custom-control-lg custom-checkbox" style="margin-bottom: 15px;">
                                    <input type="checkbox" class="custom-control-input" name="negotiable" id="negotiable">
                                    <label class="custom-control-label" for="negotiable">Negotiable</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-control custom-control-lg custom-checkbox" style="margin-bottom: 15px;">
                                    <input type="checkbox" class="custom-control-input" name="includes_tax" id="includes_tax">
                                    <label class="custom-control-label" for="includes_tax">Includes WHT @ 5%</label>
                                </div>
                            </div>
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="other_relevant_information"><b>Other Relevant Information</b></label>
                            <textarea type="tel" class="form-control form-control-l @error('other_relevant_information') is-invalid @enderror" placeholder="Specify any other relevant information" name="other_relevant_information"></textarea>

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
        $('#imageAttachment').hide();

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

        function displayImageAttachment(event) {
            $("#imageInputTrigger").hide()
            $("#imageAttachment").show()
            var output = document.getElementById('imageAttachment');
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
        }
    </script>
@endsection
