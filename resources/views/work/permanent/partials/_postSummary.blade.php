<div class="row">
                <div class="col-md-12">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-light text-dark me-2">{{ $post->status }}</span>
                        <div></div>
                    </div>
                    <h5 class="card-title">{{ $post->title }}</h5>
                    <p class="text-muted mb-1 title" style="margin-top: -15px;font-size: 10px;">{{ $post->createdOn }}</p>
                    <div class="d-flex flex-wrap mt-2 mb-2">
                        @php
                            $tags = json_decode($post->tags, true);
                        @endphp
                        @if($tags)
                            @foreach($tags as $key => $tag)
                                @if($key < 3)
                                    <span class="badge bg-light text-dark me-2">{{ $tag }}</span>
                                @endif
                            @endforeach
                            @if(count($tags) > 3)
                                <span class="badge bg-light text-dark me-2">+{{ count($tags) - 3 }} more</span>
                            @endif
                        @endif                       
                    </div>
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="title" style="font-size: 10px;color: #777;">Company</div>
                    <div class="issuer"><em
                                class="icon ni ni-building"></em> {{ $post->employer }}
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
                <div class="col-md-8">
                    <div class="title" style="font-size: 10px;color: #777;">Industry</div>
                    <div class="issuer " >
                        {{ $post->industry }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="title" style="font-size: 10px;color: #777;">Location</div>
                    <div class="issuer text"><em
                                            class="icon ni ni-map-pin"></em> {{ $post->location }} ({{ $post->distance }} km away)</div>
                </div>
            </div>

            @if(!$isShare)
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mt-3">
                            <a href="javascript:void(0)" type="button" data-toggle="modal" data-target="#shareOpportunity-{{ $post->id }}" class="btn btn-outline-secondary d-flex align-items-center justify-content-center" style="width: 40px;height: 40px; border-radius: 50%">
                                <em class="icon ni ni-share"></em>
                            </a>
                        </div>
                        <div class="mt-3">
                            @if($post->has_already_applied == "yes")
                                <button class="btn btn-outline-primary" disabled>Applied</button>
                            @else
                                <a href="{{route('user.quick_job.show', ['uuid' => $post->id])}}" class="btn btn-primary" >View listing details</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif