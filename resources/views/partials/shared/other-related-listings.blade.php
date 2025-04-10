<style>
        .-mt-5 {
                margin-top: -5px;
        }
</style>

<div class="-mt-5">
        <h5>Other related opportunities</h5>
        <div class="-mt-5">Find other related opportunities simillar to the current opportunity below</div>

        <div class="row mt-4">
                @foreach ($filteredPosts as $post )
                <div class="col-md-4">
                        @include('partials.shared.listing-container', ['isShowDetails' => false])
                </div>
                @endforeach
        </div>
</div>