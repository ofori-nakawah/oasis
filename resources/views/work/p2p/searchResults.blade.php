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
                                                <li class="breadcrumb-item"><a href="#">P2P Hire</a></li>
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
        <div class="col-md-12">
                <div class="">
                        <div class="mb-3">
                                <h2><b>Search results for "{{$target}}"</b></h2>
                        </div>
                </div>
        </div>
</div>

<div class="row">
        @if(count($users) > 0)
        @foreach($users as $user)
        <div class="col-md-4 mb-4">
                <div class="card card-bordered" style="border-radius: 18px;">
                        <div class="card-body" style="min-height: 120px;">
                                <div class="row">
                                        <div class="col-md-3">
                                                <div class="text-center">
                                                        <img src="{{$user->profile_picture ? $user->profile_picture : asset('assets/html-template/src/images/avatar/' . ['a-sm.jpg', 'b-sm.jpg', 'c-sm.jpg', 'd-sm.jpg'][array_rand(['a-sm.jpg', 'b-sm.jpg', 'c-sm.jpg', 'd-sm.jpg'])])}}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 50%;" class="border" alt="">
                                                </div>
                                        </div>
                                        <div class="col-md-9">
                                                <div style="font-weight: 800">{{$user->name}}</div>
                                                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{$user->location_name ?? "Location unavailable"}} ({{$user->distance}}km from you)</div>
                                                <div>
                                                        <em class="icon ni ni-star"></em>
                                                        <span class="ml-1">{{$user->rating ?? "0"}}</span>
                                                </div>
                                                <div class="flex  justify-between items-center">
                                                        <div class="mr-8">
                                                                <a href="{{route('user.profile', ['user_id' => $user->id])}}" class="font-medium">See profile</a>
                                                        </div>
                                                        <div>
                                                                <a href="{{route('user.profile', ['user_id' => $user->id])}}" class="font-medium">Request quote</a>
                                                        </div>
                                                </div>

                                        </div>
                                </div>
                        </div>
                </div>
        </div>
        @endforeach
        @else
        <div class="col-md-3"></div>
        <div class="col-md-6">
                <div class="card card-bordered">
                        <div class="card-body text-center py-5 h-64">
                                <div class="h-64">
                                        <div class="nk-block-empty-icon">
                                                <img src="{{asset('assets/html-template/src/images/nd.svg')}}" alt="" style=" height: 80px; object-fit: cover;" class="mb-3">
                                        </div>
                                        <h5 class="nk-block-empty-title">No results found</h5>
                                        <p class="nk-block-empty-text">No users found matching "{{$target}}". Try using different search terms.</p>
                                </div>
                        </div>
                </div>
        </div>
        @endif
</div>
@endsection