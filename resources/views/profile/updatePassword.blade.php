@extends("layouts.master")

@section('title')
    Update Password
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-lock-alt"></em> Change My Password </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">{{auth()->user()->name}}</a></li>
                            <li class="breadcrumb-item"><a href="#">Update password</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{URL::previous()}}"
                   class="btn btn-outline-primary"><span>Back</span></a></li>
            </div><!-- .nk-block-head-content -->
        </div>
    </div>

    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-4">
            <form action="{{route('user.updateProfileInformation', ['module' => 'update-password'])}}" method="POST">
                {{csrf_field()}}
                <div class="">
                    <div class="mb-3">
                        <h2><b>Update your password</b></h2>
                    </div>
                    <div class="card-body1">
                        <div class="input-group1 mb-3">
                            <input type="password" class="form-control form-control-l @error('old_password') is-invalid @enderror" placeholder="Enter your old password" name="old_password">
                            @error('old_password')
                            <span class="invalid-feedback " role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="input-group1 mb-3">
                            <input type="password" class="form-control form-control-l @error('password') is-invalid @enderror" placeholder="Enter a new password" name="password">
                            @error('password')
                            <span class="invalid-feedback " role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="input-group1 mb-3">
                            <input type="password" class="form-control form-control-l @error('password_confirmation') is-invalid @enderror" placeholder="Confirm your new password" name="password_confirmation">
                            @error('password_confirmation')
                            <span class="invalid-feedback " role="alert">
                                <strong class="text-danger">{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <div class="text-right">
                            <a href="{{URL::previous()}}" class="btn btn-outline-secondary btn-l"><b>Cancel</b></a>
                            <button class="btn btn-success btn-l"><b>Confirm Changes</b></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
