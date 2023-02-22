@extends("layouts.master")

@section('title')
    Postings
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-edit-alt"></em> Postings </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">My Posts</a></li>
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
        <div class="col-md-5">
            @foreach($posts as $post)
                <div class="card card-bordered">
                    <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$post->type}} <span style="float: right">{{$post->created_at}}</span></b></div>                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title" style="font-size: 10px;color: #777;">{{($post->type !== 'VOLUNTEER') ? 'Category' : 'Activity Name'}}</div>
                                <div class="issuer"><b>{{($post->type !== 'VOLUNTEER') ? $post->category : $post->name}}</b></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                        <a href="{{route('user.posts.edit', ['uuid' => $post->id])}}" class="btn btn-outline-warning">Edit</a>
                        <a href="{{route('user.posts.show', ['uuid' => $post->id])}}" class="btn btn-outline-secondary">View Details</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="col-md-7 d-none d-md-block">
            <div class="text-center">
                <img src="{{asset('assets/html-template/src/images/details.svg')}}" alt="" style="height: 250px; width: 250px;">
                <p style="color: #777;">Select an activity to view more details.</p>
            </div>
        </div>
    </div>
@endsection
