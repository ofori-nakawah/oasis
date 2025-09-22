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
                                                <li class="breadcrumb-item"><a href="#">Update P2P Job</a></li>
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
                <form action="{{route('p2p.updateQuoteRequest', ['uuid' => $post->id])}}" method="POST" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="">
                                <div class="mb-3">
                                        <h2><b>Update P2P job request</b></h2>
                                </div>
                                <input type="hidden" name="vorkers" value="{{ json_encode([['userId'=> $workerId]]) }}">
                                <div class="card-body1">
                                        <div class="input-group1 mb-3">
                                                <label for="name"><b>Select Category</b></label>
                                                <select type="text"
                                                        class="form-control form-control-l @error('category') is-invalid @enderror"
                                                        name="category">
                                                        <option value="{{$post->category}}">{{$post->category}}</option>
                                                        @foreach($categories as $category)
                                                        @if($category != $post->category)
                                                        <option value="{{$category}}">{{$category}}</option>
                                                        @endif
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
                                                <textarea class="form-control form-control-l @error('description') is-invalid @enderror"
                                                        placeholder="Enter brief description of the project"
                                                        name="description">{{ $post->description }}</textarea>

                                                @error('description')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                                                @enderror
                                        </div>

                                        <div class="card card-bordered"
                                                style="height: 180px;margin-bottom: 15px;border-style: dotted;cursor: pointer;">
                                                <div id="imageInputTrigger" class="text-center" style="padding: 15px;"
                                                        onclick="document.getElementById('image_attachment').click();">
                                                        <img src="{{asset('assets/html-template/src/images/photo.svg')}}" style="height: 120px;"
                                                                alt="">
                                                        <p class="text-muted">Add image attachment</p>
                                                </div>
                                                <img id="imageAttachment" style="height: 180px;" @if($post->post_image_link) src="{{$post->post_image_link}}" @endif></img>
                                                <input type="file" accept="image/png, image/gif, image/jpeg, image/jpg" hidden
                                                        name="post_image" id="image_attachment"
                                                        onchange="displayImageAttachment(event)">
                                        </div>
                                </div>
                                <div class="text-right">
                                        <a href="{{route('p2p.removeQuoteRequest', ['uuid' => $post->id])}}" onclick="return confirm('Are you sure you want to remove this job request?')" class="btn btn-outline-danger" style="float: left !important;"><b>Remove Job</b></a>
                                        <button class="btn btn-success btn-l"><b>Save Changes</b></button>
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
        function displayImageAttachment(event) {
                $("#imageInputTrigger").hide()
                $("#imageAttachment").show()
                var output = document.getElementById('imageAttachment');
                output.src = URL.createObjectURL(event.target.files[0]);
                output.onload = function() {
                        URL.revokeObjectURL(output.src) // free memory
                }
        }

        $(document).ready(function() {
                if ($("#imageAttachment").attr("src")) {
                        $("#imageInputTrigger").hide();
                        $("#imageAttachment").show();
                } else {
                        $("#imageAttachment").hide();
                        $("#imageInputTrigger").show();
                }
        });
</script>
@endsection