@extends("layouts.master")

@section("title") Add outside VORK job experience @endsection

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
                            <li class="breadcrumb-item">Add outside vork job experience</li>
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
            <form action="{{route('user.outsideVorkJobHistory.store')}}" method="POST">
                {{csrf_field()}}
                <div class="mb-3">
                    <h2><b>Add a new outside VORK job experience</b></h2>
                </div>

                <input type="hidden" name="user_id" value="{{auth()->id()}}">

                <div class="input-group1 mb-3">
                    <label for="role"><b>Role</b></label>
                    <input type="text" class="form-control form-control-l @error('role') is-invalid @enderror" placeholder="Enter role title" name="role" value="{{ old('role') }}">

                    @error('role')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="input-group1 mb-3">
                    <label for="employer"><b>Employer</b></label>
                    <input type="text" class="form-control form-control-l @error('employer') is-invalid @enderror" placeholder="Enter name of employer" name="employer" value="{{ old('employer') }}">

                    @error('employer')
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
                    <label for="responsibilities"><b>Responsibilities</b></label>
                    <textarea class="form-control form-control-l summernote @error('responsibilities') is-invalid @enderror"
                              placeholder="Enter your responsibilities"
                              name="responsibilities">{{ old('responsibilities') }}</textarea>

                    @error('responsibilities')
                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="input-group1 mb-3">
                    <label for="qualifications"><b>Achievements</b></label>
                    <textarea class="form-control summernote form-control-l @error('achievements') is-invalid @enderror"
                              placeholder="Enter achievements"
                              name="achievements">{{ old('achievements') }}</textarea>

                    @error('achievements')
                    <span class="invalid-feedback" role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="input-group1 mb-3">
                    <label for="reference"><b>Reference</b></label>
                    <input type="text" class="form-control form-control-l @error('reference') is-invalid @enderror" placeholder="Enter role title" name="reference" value="{{ old('reference') }}">

                    @error('reference')
                    <span class="invalid-feedback " role="alert">
                                                    <strong class="text-danger">{{ $message }}</strong>
                                                </span>
                    @enderror
                </div>

                <div class="text-right mb-3" id="publishBtn">
                    <button class="btn btn-success btn-l" type="button" onclick="confirmPublish()"><b>Add job experience</b></button>
                </div>
                <div class="alert alert-primary mt-3" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(5px);
-webkit-backdrop-filter: blur(5px);
border: 1px solid rgba(255, 255, 255, 0.3);" id="publishConfirmationBox">
                    <h4>Are you sure?</h4>
                    <p>Confirm all the entered information above are accurate before proceeding to add job experience.</p>
                    <div class="text-right" >
                        <button class="btn btn-outline-secondary btn-l" type="button" onclick="cancelPublish()"><b>Cancel</b></button>
                        <button class="btn btn-success btn-l" type="submit"><b>Yes, add job experience!</b></button>
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
