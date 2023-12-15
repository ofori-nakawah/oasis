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
                            <li class="breadcrumb-item"><a href="#">Work</a></li>
                            <li class="breadcrumb-item"><a href="#">Fixed Term Jobs</a></li>
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
        @foreach($posts as $post)
            <!-- <div class="card card-bordered" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
/*border-radius: 16px;*/
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;">
                <div class="card-header bg-gray-100" style="border: 1px solid #dbdfea;border-radius: 16px; margin:5px;">
                    <b>{{$post->title}}</b></div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Issuer</div>
                            <div class="issuer"><b>{{$post->user->name}}</b></div>
                        </div>
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Date & Time</div>
                            <b>
                                <div class="date text-danger">{{$post->date}} {{$post->time}}</div>
                            </b>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Location</div>
                            <div class="issuer"><b>{{$post->location}} ({{$post->distance}}km)</b></div>
                        </div>
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Budget (GHS)</div>
                            <b>
                                <div class="date text-success">{{$post->min_budget}}
            - {{$post->max_budget}}</div>
                            </b>
                        </div>
                    </div>
</div>
            </div> -->

            <div class="col-md-4">
                <div class="card card-bordered" style="border-radius: 16px;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                image
                            </div>
                            <div class="col-md-10">
                                <div style="font-weight: 800">{{$post->title}}</div>
                                <div>Employer</div>
                                <div>Location</div>
                                <div>2 days ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
