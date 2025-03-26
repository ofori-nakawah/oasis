@php
$title = $post->title;
switch($post->type) {
case "QUICK_JOB":
case "P2P":
$title = $post->category;
break;
case "VOLUNTEER":
$title = $post->name;
break;
}
@endphp


<div class="row">
        <div class="col-md-12">
                <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-light text-dark me-2">{{ $post->status }}</span>
                        <div></div>
                </div>
                <h5 class="card-title" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $title }}</h5>
                <p class="text-muted mb-1 title" style="margin-top: -15px;font-size: 10px;">{{ $post->createdOn }}</p>
                <div class="d-flex flex-wrap mt-2 mb-2">
                        @php
                        $tags = json_decode($post->tags, true);
                        @endphp
                        @if($tags)
                        @foreach($tags as $key => $tag)
                        @if($key < 2)
                                <span class="badge bg-light text-dark me-2">{{ $tag }}</span>
                                @endif
                                @endforeach
                                @if(count($tags) > 2)
                                <span class="badge bg-light text-dark me-2">+{{ count($tags) - 2 }} more</span>
                                @endif
                                @endif
                </div>
        </div>
</div>

@php
$employerKey = "Issuer";
$employerValue = $post->user->name;

if($post->type == "FIXED_TERM_JOB") {
$employerKey = "Company";
$employerValue = $post->employer;
}

if($post->type == "PERMANENT_JOB") {
$employerKey= "Company";
$employerValue = $post->employer;
}

@endphp

<div class="row mb-2">
        <div class="col-md-12">
                <div class="title" style="font-size: 10px;color: #777;">{{$employerKey}}</div>
                <div class="issuer"><em
                                class="icon ni ni-building"></em> {{ $employerValue }}
                </div>
        </div>
</div>
<div class="row mb-2">
        <div class="col-md-4">
                <div class="title" style="font-size: 10px;color: #777;">Budget
                        (GHS/month)
                </div>
                <div class="issuer text-success"><em
                                class="icon ni ni-coins"></em> {{ $post->min_budget }}
                        - {{ $post->max_budget }}</div>
        </div>

        @php
        $key = "Deadline";
        $value = $post->date;

        if($post->type == "FIXED_TERM_JOB") {
        $key = "Duration";
        $value = $post->duration . " months";
        }

        if($post->type == "PERMANENT_JOB") {
        $key = "Industry";
        $value = $post->industry;
        }
        @endphp

        <div class="col-md-8">
                <div class="title" style="font-size: 10px;color: #777;">{{$key}}</div>
                <div class="issuer ">
                        {{ $value }}
                </div>
        </div>
</div>


@if ($isShowDetails)
<div class="row">
        <div class="col-md-12">
                <div class="title" style="font-size: 10px;color: #777;">Job Description</div>
                <div class="issuer text summernote-description">{{ $post->description }}</div>
        </div>
</div>

@if ($post->type == "FIXED_TERM_JOB" || $post->type == "PERMANENT_JOB")
<div class="row mt-2">
        <div class="col-md-12">
                <div class="title" style="font-size: 10px;color: #777;">Qualifications</div>
                <div class="issuer text summernote-qualifications">
                        {{$post->qualifications}}
                </div>
        </div>
</div>
@endif
@endif

<div class="row mt-2">
        <div class="col-md-12">
                <div class="title" style="font-size: 10px;color: #777;">Location</div>
                <div class="issuer text"><em
                                class="icon ni ni-map-pin"></em> {{ $post->location }} ({{ $post->distance }} km away)</div>
        </div>
</div>

@if ($isShowDetails)
<div class="row mt-2">
        <div class="col-md-12">
                <div class="title" style="font-size: 10px;color: #777;">Other relevant information</div>
                <div class="issuer text"> {{ $post->other_relevant_information ?? 'N/A' }}</div>
        </div>
</div>
@endif

@if(!$isShare)
<div class="row">
        <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                        <div class="mt-3">
                                <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#shareOpportunity-{{ $post->id }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 40px;height: 40px; border-radius: 50%">
                                        <em class="icon ni ni-share"></em>
                                </a>
                        </div>
                        <div class="mt-3 ml-2">
                                @if($post->has_already_applied == "yes")
                                <div class="text-primary text-right">You have already applied for this opportunity</div>
                                @else
                                @php
                                $route = "";
                                switch($post->type) {
                                case "QUICK_JOB":
                                case "P2P":
                                $route = route('user.quick_job.show', ['uuid' => $post->id]);
                                break;
                                case "FIXED_TERM_JOB":
                                $route = route('user.fixed_term_job.show', ['uuid' => $post->id]);
                                break;
                                case "PERMANENT_JOB":
                                $route = route('user.permanent_job.show', ['uuid' => $post->id]);
                                break;
                                }
                                @endphp
                                @if ($isShowDetails)
                                <a href="{{route("user.apply_for_job", ["uuid" => $post->id])}}" class="btn btn-outline-primary">Apply</a>
                                @else
                                <a href="{{$route}}" class="btn btn-outline-primary">View details</a>
                                @endif
                                @endif
                        </div>
                </div>
        </div>
</div>
@endif