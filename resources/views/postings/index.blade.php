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
@if(count($posts) <= 0)
    <div class="row">
    <div class="col-md-12 text-center">
        <img src="{{asset('assets/html-template/src/images/nd.svg')}}" style="height: 250px; width: 250px;"
            alt="">
        <p style="color: #777;">You haven't published any post yet.</p>
    </div>
    </div>
    @else
    <div class="row">
        <div class="col-md-5">
            @foreach($posts as $post)
            <div class="card card-bordered">
                <div class="card-header bg-white" style="border-bottom: 1px solid #dbdfea;"><b>{{$post->type}}
                        <span style="float: right">{{$post->created_at}}</span></b></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title"
                                style="font-size: 10px;color: #777;">
                                @if($post->type === "VOLUNTEER")
                                Activity Name
                                @endif

                                @if($post->type === "QUICK_JOB" || $post->type === "P2P")
                                Category
                                @endif

                                @if($post->type === "FIXED_TERM_JOB" || $post->type === "PERMANENT_JOB")
                                Title
                                @endif
                            </div>
                            <div class="issuer">
                                <b>
                                    @if($post->type === "VOLUNTEER")
                                    {{$post->name}}
                                    @endif

                                    @if($post->type === "QUICK_JOB" || $post->type === "P2P")
                                    {{$post->category}}
                                    @endif

                                    @if($post->type === "FIXED_TERM_JOB" || $post->type === "PERMANENT_JOB")
                                    {{$post->title}}
                                    @endif
                                </b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right bg-white" style="border-top: 1px solid #dbdfea;">
                    @if($post->status !== "closed")
                    <a href="#" onclick="setupShareableLink('{{$post->type}}', '{{$post->id}}')" style="float: left !important;margin-top: 5px;" data-toggle="modal"
                        data-target="#sharePostbModal"><em class="icon ni ni-link" style="font-size: 24px;"></em> </a>
                    @endif
                    <div class="modal modal-lg fade" tabindex="-1" id="sharePostbModal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                    <em class="icon ni ni-cross"></em>
                                </a>
                                <div class="modal-header">
                                    <h5 class="modal-title"><b>Share Post</b></h5>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <p><em class="icon ni ni-bulb"></em> You can copy and share post with your family and
                                                friends on all platforms.</p>
                                            <p class="alert alert-lighter bg-lighter text-primary no-border"
                                                style="padding: 10px;border-radius: 4px;margin-bottom: 15px;border: none !important;"><b><span
                                                        id="shareableLink"></span></b>
                                            </p>
                                            <div class="btn btn-outline-primary copyLinkButton bold" style="float: right !important;"
                                                onclick="copyLinkToClipboard()"><em class="icon ni ni-copy"></em> Copy link
                                            </div>
                                            <span class="copyStatus text-success"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($post->status === "closed")
                    <span>Post Closed</span>
                    @else
                    <a href="{{route('user.posts.edit', ['uuid' => $post->id])}}"
                        class="btn btn-outline-warning">Edit</a>
                    @endif
                    <a href="{{route('user.posts.show', ['uuid' => $post->id])}}"
                        class="btn btn-outline-secondary">Status</a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="col-md-7 d-none d-md-block">
            <div class="text-center" style="margin-top: 120px;">
                <img src="{{asset('assets/html-template/src/images/details.svg')}}" alt=""
                    style="height: 250px; width: 250px;">
                <p style="color: #777;">Select an activity to view more details.</p>
            </div>
        </div>
    </div>
    @endif
    @endsection


    @section("scripts")
    <script>
        function shareLink() {
            $(".copyStatus").hide()
            $(".copyStatus").html("")
            $(`.copyLinkButton`).show();
        }

        function copyLinkToClipboard(uuid) {
            var copyText = document.getElementById(`shareLink-${uuid}`);

            // Select the text field
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(copyText.value);

            $(`.copyLinkButton`).hide();
            const successMessage = `<div class="text-success"><em class="icon ni ni-copy"></em> Copied to clipboard</div>`
            $(".copyStatus").append(successMessage);
            $(".copyStatus").show();
        }
    </script>
    @endsection