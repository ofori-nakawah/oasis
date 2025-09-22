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
                                                <li class="breadcrumb-item"><a href="#">Search vorkers to hire</a></li>
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

                <div class="">
                        <div class="mb-3">
                                <h2><b>Search vorkers to hire</b></h2>
                        </div>
                        <div class="card-body1">
                                <form action="{{route('p2p.searchVorkers')}}" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" name="module" value="NAME_SEARCH">
                                        <div class="input-group1 mb-3">
                                                <label for="name"><b>Search by name</b></label>
                                                <input type="text"
                                                        class="form-control date form-control-l @error('target') is-invalid @enderror"
                                                        placeholder="Enter name to search" name="target" value="{{ old('target') }}">


                                                <span class="invalid-feedback1 " role="alert">
                                                        <i class="icon ni ni-bulb"></i> <strong class="text">Hit enter to search</strong>
                                                </span>
                                        </div>
                                </form>
                                <form action="{{route('p2p.searchVorkers')}}" method="POST">
                                        {{csrf_field()}}
                                        <input type="hidden" name="module" value="CATEGORY_SEARCH">
                                        <div style="margin-top: 15px;">
                                                <label for="name"><b>Search by skills and interest</b></label>
                                                <div class="card card-bordered" style="padding: 15px;">
                                                        <div>
                                                                @foreach($skills_and_interest as $skills)
                                                                <div class="custom-control custom-checkbox" style="margin-right: 15px;margin-bottom: 15px;margin-top: 15px;">
                                                                        <input type="checkbox" class="custom-control-input" name="target" id="{{$skills->name}}" value="{{$skills->name}}">
                                                                        <label class="custom-control-label" for="{{$skills->name}}"><b>{{$skills->name}}</b></label>
                                                                </div>
                                                                @endforeach
                                                        </div>
                                                </div>
                                        </div>


                                </form>
                        </div>
                </div>
        </div>
        @endsection

        @section("scripts")
        <script>
                document.addEventListener('DOMContentLoaded', function() {
                        const checkboxes = document.querySelectorAll('input[name="target"]');

                        checkboxes.forEach(checkbox => {
                                checkbox.addEventListener('change', function() {
                                        if (this.checked) {
                                                this.closest('form').submit();
                                        }
                                });
                        });
                });
        </script>
        </script>
        @endsection