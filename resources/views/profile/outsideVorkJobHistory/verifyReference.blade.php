@extends("layouts.master")

@section("title") Request reference approval @endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-user"></em> Profile </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">{{$user->name}}</a></li>
                            <li class="breadcrumb-item">Request reference approval outside vork job experience</li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a href="{{URL::previous()}}"
                   class="btn btn-outline-light"><span>Back</span></a>
            </div><!-- .nk-block-head-content -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <form action="{{route('user.outsideVorkJobHistory.getReferenceVerification', ["id" => $outsideVorkJob->id])}}" method="POST">
                {{csrf_field()}}
                <div class="mb-3">
                    <h2><b>Request reference approval ({{$outsideVorkJob->role}} at {{$outsideVorkJob->employer}})</b></h2>
                </div>

                <div class="input-group1 mb-3">
                    <label for="role"><b>Reference</b></label>
                    <div class="row">
                        <div class="col-md-9 col-sm-8">
                            <input type="text" disabled class="form-control form-control-l @error('role') is-invalid @enderror" placeholder="Enter role title" name="role" value="{{ json_decode($outsideVorkJob->reference)->name}}">
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <a href="{{route("user.outsideVorkJobHistory.edit", ["id" => $outsideVorkJob->id])}}" class="btn btn-outline-primary btn-block"><b>Change reference</b></a>
                        </div>
                    </div>

                    @error('role')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="input-group1 mb-3">
                    <label for="email"><b>Email</b></label>
                    <input type="email" class="form-control form-control-l @error('email') is-invalid @enderror" placeholder="Enter reference email" name="email" value="{{ old("email") }}">

                    @error('email')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="input-group1 mb-3">
                    <label for="phone_number"><b>Phone Number</b></label>
                    <input type="tel" class="form-control form-control-l @error('phone_number') is-invalid @enderror" placeholder="Enter reference phone number" name="phone_number" value="{{ old("phone_number") }}">

                    @error('phone_number')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>



                <div class="text-right mb-3" id="publishBtn">
                    <button class="btn btn-success btn-l" type="button" onclick="confirmPublish()"><b>Request job experience approval</b></button>
                </div>
                <div class="alert alert-primary mt-3" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(5px);
-webkit-backdrop-filter: blur(5px);
border: 1px solid rgba(255, 255, 255, 0.3);" id="publishConfirmationBox">
                    <h4>Are you sure?</h4>
                    <p>Confirm all the entered information above are accurate before proceeding to request job experience approval.</p>

                    <p><em class="ni ni-bulb"></em> <b>An email will be sent to your reference for approval.</b></p>
                    <div class="text-right" >
                        <button class="btn btn-outline-secondary btn-l" type="button" onclick="cancelPublish()"><b>Cancel</b></button>
                        <button class="btn btn-success btn-l" type="submit"><b>Yes, request job experience approval!</b></button>
                    </div>
                </div>
            </form>

        </div>
        <div class="col-md-3"></div>
    </div>
@endsection

@section("scripts")
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <script>
        $("#publishConfirmationBox").hide()

        $(document).ready(function() {
            $('.summernote').summernote({
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['fontsize', ['fontsize']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']]
                ],
                height: 160,
            });
        });

        function confirmPublish() {
            $("#publishBtn").hide()
            $("#publishConfirmationBox").show("slow")
            $("#publishConfirmationBox").addClass("mb-3");
            $('html, body').animate({ scrollTop:  $("#publishConfirmationBox").offset().top - 50 }, 'slow');
        }

        function cancelPublish() {
            $("#publishBtn").show()
            $("#publishConfirmationBox").hide("slow")
        }
    </script>
@endsection
