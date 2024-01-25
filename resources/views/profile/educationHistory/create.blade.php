@extends("layouts.master")

@section("title") Education  @endsection

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
                            <li class="breadcrumb-item">Add education </li>
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
            <form action="{{route('user.educationHistory.store')}}" method="POST" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="mb-3">
                    <h2><b>Add a new education </b></h2>
                </div>

                <input type="hidden" name="user_id" value="{{auth()->id()}}">

                <div class="input-group1 mb-3">
                    <label for="programme"><b>Programme</b></label>
                    <input type="text" class="form-control form-control-l @error('programme') is-invalid @enderror" placeholder="Enter programme" name="programme" value="{{ old('programme') }}">

                    @error('programme')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="input-group1 mb-3">
                    <label for="specialty"><b>Major/Specialty</b></label>
                    <input type="text" class="form-control form-control-l @error('specialty') is-invalid @enderror" placeholder="Enter specialty" name="specialty" value="{{ old('specialty') }}">

                    @error('specialty')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="input-group1 mb-3">
                    <label for="institution"><b>Institution</b></label>
                    <input type="text" class="form-control form-control-l @error('institution') is-invalid @enderror" placeholder="Enter name of institution" name="institution" value="{{ old('institution') }}">

                    @error('institution')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group1 mb-3">
                            <label for="start_date"><b>Start Date</b></label>
                            <input type="date" class="form-control form-control-l @error('start_date') is-invalid @enderror" placeholder="Enter start date" name="start_date" value="{{ old('start_date') }}">

                            @error('start_date')
                            <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group1 mb-3">
                            <label for="end_date"><b>End Date</b></label>
                            <input type="date" class="form-control form-control-l @error('end_date') is-invalid @enderror" placeholder="Enter end date" name="end_date" value="{{ old('end_date') }}">

                            @error('end_date')
                            <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-control custom-control-lg custom-checkbox"
                                     style="margin-bottom: 15px;">
                                    <input type="checkbox" class="custom-control-input" name="is_ongoing"
                                           id="is_ongoing">
                                    <label class="custom-control-label" for="is_ongoing">Ongoing?</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="input-group1 mb-3">
                    <label for="certificate_link"><b>Certificate Link</b></label>
                    <input type="file" class="form-control form-control-l @error('certificate_link') is-invalid @enderror" name="certificate_link" value="{{ old('certificate_link') }}">

                    @error('certificate_link')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="text-right mb-3" id="publishBtn">
                    <button class="btn btn-success btn-l" type="button" onclick="confirmPublish()"><b>Add education </b></button>
                </div>
                <div class="alert alert-primary mt-3" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(5px);
-webkit-backdrop-filter: blur(5px);
border: 1px solid rgba(255, 255, 255, 0.3);" id="publishConfirmationBox">
                    <h4>Are you sure?</h4>
                    <p>Confirm all the entered information above are accurate before proceeding to add education .</p>
                    <div class="text-right" >
                        <button class="btn btn-outline-secondary btn-l" type="button" onclick="cancelPublish()"><b>Cancel</b></button>
                        <button class="btn btn-success btn-l" type="submit"><b>Yes, add education !</b></button>
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
