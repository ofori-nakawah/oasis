<div class="text-dark mb-3" style="display: flex; flex-direction: row;">
        <div style="flex:1">{{$count}} listings</div>
        <div style="display:flex; gap: 20px;border-bottom: 1px solid #dbdfea;">
                <span class="text-primary" style="border-bottom: 3px solid #353299;"><b>All</b></span>
                {{-- <a href="#" class="text-dark">Saved</a>--}}
        </div>
</div>

<div class="row" style="margin-top: -5px;">
        @if($posts->count() > 0)
        @foreach($posts as $post)
        <div class="col-md-6">
                @include('partials.shared.listing-container')
        </div>
        @endforeach
        @else
        <div class="col-md-12">
                <p class="text-muted">No opportunities found</p>
        </div>
        @endif

        <!-- Pagination Links -->
        <div class="col-md-12">
                <div class="d-flex justify-content-center mt-4">
                        {{ $posts->onEachSide(1)->links('pagination::bootstrap-4', ['class' => 'custom-pagination']) }}
                </div>
        </div>

        <style>
                .pagination {
                        margin-bottom: 0;
                }

                .page-item.active .page-link {
                        background-color: #353299;
                        border-color: #353299;
                }

                .page-link {
                        color: #353299;
                }

                .page-link:hover {
                        color: #353299;
                }
        </style>
</div>