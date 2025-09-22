<div class="modal fade" tabindex="-1" id="shareOpportunity-{{ $post->id }}" style="border-radius: 4px;">
        <div class="modal-dialog" role="document" style="border-radius: 4px;">
                <div class="modal-content" style="border-radius: 18px;">
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <em class="icon ni ni-cross"></em>
                        </a>
                        <div class="modal-header" style="border-bottom: none !important;">
                                <h4 class="modal-title"><b>Share Opportunity</b></h4>
                        </div>
                        <div class="modal-body">
                                <div class="row">
                                        <div class="col-md-12">

                                                <div class="card card-bordered mb-3" style="border-radius: 18px;margin-top: -20px;">
                                                        <div class="card-body">
                                                                @include('partials.shared.listing-data', ['isShare' => true])
                                                        </div>
                                                </div>

                                                <div class="input-group mb-3 justify-content-center ">
                                                        <input type="text" class="form-control" value="{{ route('work.show', ['uuid' => $post->id]) }}" id="share-url-{{ $post->id }}" readonly>
                                                        <button class="btn btn-primary btn-sm ml-1" type="button" onclick="copyShareUrl('{{ $post->id }}')"><em class="icon ni ni-copy"></em> Copy</button>
                                                </div>

                                                <div class="d-flex justify-content-center gap-5 mt-1 mb-1 ">
                                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('user.quick_job.show', ['uuid' => $post->id])) }}" target="_blank" class="btn btn-outline-primary p-2 m-1">
                                                                <em class="icon ni ni-facebook-f"></em>
                                                        </a>
                                                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('user.quick_job.show', ['uuid' => $post->id])) }}&text={{ urlencode('Check out this job: ' . $post->title) }}" target="_blank" class="btn btn-outline-info p-2 m-1">
                                                                <em class="icon ni ni-twitter"></em>
                                                        </a>
                                                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('user.quick_job.show', ['uuid' => $post->id])) }}" target="_blank" class="btn btn-outline-secondary p-2 m-1">
                                                                <em class="icon ni ni-linkedin"></em>
                                                        </a>
                                                        <a href="mailto:?subject={{ urlencode('Job Opportunity: ' . $post->title) }}&body={{ urlencode('Check out this job opportunity: ' . route('user.quick_job.show', ['uuid' => $post->id])) }}" class="btn btn-outline-danger p-2 m-1">
                                                                <em class="icon ni ni-mail"></em>
                                                        </a>
                                                </div>


                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>

<script>
        function copyShareUrl(uuid) {
                var copyText = document.getElementById(`share-url-${uuid}`);

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