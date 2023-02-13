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
            <form action="" method="POST">
                {{csrf_field()}}
                <div class="">
                    <div class="mb-3">
                        <h2><b>Create your project</b></h2>
                    </div>
                    <div class="card-body1">
                        <div class="input-group1 mb-3">
                            <label for="name"><b>Activity Name</b></label>
                            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Enter name of activity or project" name="name">

                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="description"><b>Description</b></label>
                            <textarea class="form-control form-control-lg @error('description') is-invalid @enderror" placeholder="Enter brief description of the project" name="description"></textarea>

                            @error('description')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="date"><b>Date</b></label>
                                    <input type="date" class="form-control form-control-lg @error('date') is-invalid @enderror" placeholder="Select date of activity" name="date">

                                    @error('date')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="time"><b>Time</b></label>
                                    <input type="time" class="form-control form-control-lg @error('time') is-invalid @enderror" placeholder="Select time of activity" name="time">

                                    @error('time')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="number_of_participants"><b>Number of Participants</b></label>
                                    <input type="number" class="form-control form-control-lg @error('number_of_participants') is-invalid @enderror" placeholder="Specify number of participants" name="number_of_participants">

                                    @error('number_of_participants')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group1 mb-3">
                                    <label for="name"><b>Volunteer Hours</b></label>
                                    <input type="text" class="form-control form-control-lg @error('volunteer_hours') is-invalid @enderror" placeholder="Specify volunteer hours" name="volunteer_hours">

                                    @error('volunteer_hours')
                                    <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="name"><b>Location</b></label>
                            <input type="text" class="form-control form-control-lg @error('location') is-invalid @enderror" placeholder="Provide location of activity" name="location">

                            @error('location')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                        <div class="input-group1 mb-3">
                            <label for="other_relevant_information"><b>Other Relevant Information</b></label>
                            <textarea type="tel" class="form-control form-control-lg @error('other_relevant_information') is-invalid @enderror" placeholder="Specify any other relevant information" name="other_relevant_information"></textarea>

                            @error('other_relevant_information')
                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                            @enderror
                        </div>

                    </div>
                    <div class="text-right">
                        <button class="btn btn-success btn-lg"><b>Create & Publish</b></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
