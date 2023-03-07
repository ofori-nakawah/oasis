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
                            <li class="breadcrumb-item"><a href="#">Join</a></li>
                            <li class="breadcrumb-item"><a href="#">Upcoming Activities Near You</a></li>
                            <li class="breadcrumb-item"><a href="#">{{$original_post->name}}</a></li>
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
        <div class="col-md-5 d-none d-md-block">
            @foreach($posts as $post)
                <div class="card card-bordered">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$post->name}}</b></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="title" style="font-size: 10px;color: #777;">Issuer</div>
                                <div class="issuer"><b>{{$post->user->name}}</b></div>
                            </div>
                            <div class="col-md-6">
                                <div class="title" style="font-size: 10px;color: #777;">Date</div>
                                <b>
                                    <div class="date text-danger">{{$post->date}}</div>
                                </b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                <div class="issuer"><b>{{$post->location}}</b></div>
                            </div>
                            <div class="col-md-6">
                                <div class="title" style="font-size: 10px;color: #777;">Volunteer Hours</div>
                                <b>
                                    <div class="date text-success">{{$post->volunteer_hours}}</div>
                                </b>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                        <span class="pull-left" style="float: left !important;">
                            @if($post->has_already_applied === "yes") <span>Already applied</span> @endif
                        </span>
                        <a href="{{route('user.volunteerism.show', ['uuid' => $post->id])}}" class="btn btn-outline-secondary">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-7">
            @include("utilities.alerts.alerts")
            <div class="card card-bordered">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="title" style="font-size: 10px;color: #777;">Activity Name</div>
                            <div class="issuer"><b>{{$original_post->name}}</b></div>
                        </div>
                        <div class="col-md-4">
                            <div class="title" style="font-size: 10px;color: #777;">Issuer</div>
                            <b>
                                <div class="date text-success">{{$original_post->user->name}}</div>
                            </b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Description</div>
                            <div class="issuer"><b>{{$original_post->description}}</b></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Location</div>
                            <b><div class="location">{{$original_post->location}}</div></b>
                            <div class="map" id="locationMap" style="height: 300px;width: 100%;border-radius: 4px;">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="title" style="font-size: 10px;color: #777;">Volunteer Hours</div>
                            <div class="issuer"><b>{{$original_post->volunteer_hours}}</b></div>
                        </div>
                        <div class="col-md-4">
                            <div class="title" style="font-size: 10px;color: #777;">Date</div>
                            <b>
                                <div class="date text-success">{{$original_post->date}}</div>
                            </b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Other Relevant Information</div>
                            <div class="issuer"><b>{{$original_post->other_relevant_information}}</b></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                    @if($original_post->has_already_applied === "yes") <span>You have already applied</span> @else <a href="{{route("user.apply_for_job", ['uuid' => $original_post->id])}}" class="btn btn-outline-success">Apply</a> @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        const coords = '{{$original_post->coords}}'
        console.log(coords)
    </script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{env("GOOGLE_MAPS_KEY")}}&libraries=places&callback=mountMap"
        async defer></script>
    <script>
        function mountMap() {
            var mapOptions = {
                center: {
                    lat: parseFloat(coords.split(', ')[0]),
                    lng: parseFloat(coords.split(', ')[1])
                },
                zoom: 14,
                componentRestrictions: {country: "gh"}
            };
            var map = new google.maps.Map(document.getElementById("locationMap"), mapOptions);

            /**
             * show marker of user's current location
             * @type {google.maps.Marker}
             */
            const _latlng = new google.maps.LatLng(parseFloat(coords.split(', ')[0]), parseFloat(coords.split(', ')[1]));
            map.panTo(_latlng)
            const marker = new google.maps.Marker({
                position: _latlng,
                map: map,
            });
        }
    </script>
@endsection
