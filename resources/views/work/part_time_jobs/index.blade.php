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
                            <li class="breadcrumb-item"><a href="#">fixed term opportunities</a></li>
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

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-10">
                    <div class="form-control-wrap" style="margin-bottom: 15px;">
                        <div class="form-icon form-icon-left">
                            <em class="icon ni ni-search"></em>
                        </div>
                        <input type="text" class="form-control form-control-lg" name="search" id="default-01" placeholder="Search posts">
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="#" class="btn btn-outline-primary" style="float: right;"><em class="icon ni ni-filter"></em></a>
                </div>
            </div>
        </div>
    </div>

    @if(count($posts) <= 0)
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{asset('assets/html-template/src/images/nd.svg')}}" style="height: 250px; width: 250px;" alt="">
                <p style="color: #777;">There are no jobs available at the moment. Come back later.</p>
            </div>
        </div>
    @else
        <div class="row">
                @foreach($posts as $post)
                <div class="col-md-4">
                    <a href="{{route('user.show_fixed_term_job_details.show', ["uuid" => $post->id])}}" style="text-decoration: none !important;">
                        <div class="card card-bordered mb-3" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 16px;
box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
backdrop-filter: blur(5px);
-webkit-backdrop-filter: blur(5px);
border: 1px solid rgba(255, 255, 255, 0.3);">
                            <div class="card-header bg-white text-primary" style="border-bottom: 1px solid #dbdfea;">
                                <b>{{$post->title}}</b> @if($post->is_internship === "yes") <span class="badge badge-secondary">Internship</span> @endif</div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <div class="title" style="font-size: 10px;color: #777;">Company</div>
                                        <div class="issuer text-danger"><b>{{$post->employer}}</b></div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <div class="title" style="font-size: 10px;color: #777;">Budget (GHS/month)</div>
                                        <div class="issuer text-success"><b>{{$post->min_budget}} - {{$post->max_budget}}</b></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="title" style="font-size: 10px;color: #777;">Duration</div>
                                        <div class="issuer "><b>{{$post->duration}} month(s)</b></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                        <div class="issuer text-dark"><b>{{$post->location}} ({{$post->distance}}km)</b></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            @endforeach
        </div>
    @endif
@endsection
