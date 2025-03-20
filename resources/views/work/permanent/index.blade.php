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
                            <li class="breadcrumb-item"><a href="#">Permanent Jobs</a></li>
                            <li class="breadcrumb-item"><a href="#">Discover permanent job opportunities near you and all over the country</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a href="{{URL::previous()}}"
                   class="btn btn-outline-lighter"><span>Back</span></a></li>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

    <div class="row">
        <div class="col-md-12 col-sm-12" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div class="job-tabs-container" style="background-color: #f5f6fa; border-radius: 8px; padding: 5px; display: flex; flex-wrap: wrap; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 10px;">
                <a href="{{route("user.work.jobs", ["type_of_user" => "seeker", "type_of_work" => "quick-job"])}}" class="job-tab" style="padding: 10px 15px; margin: 3px; border-radius: 8px; font-weight: 500; text-decoration: none; color: #364a63; transition: all 0.3s ease; white-space: nowrap;font-size: 12px;">
                    CASUAL
                </a>
                <a href="{{route("user.work.jobs", ["type_of_user" => "seeker", "type_of_work" => "fixed-term"])}}" class="job-tab" style="padding: 10px 15px; margin: 3px; border-radius: 8px; font-weight: 500; text-decoration: none; color: #364a63; transition: all 0.3s ease; white-space: nowrap;font-size: 12px;">
                    FIXED TERM
                </a>
                <a href="javascript:void(0)" class="job-tab active" style="padding: 10px 15px; margin: 3px; border-radius: 8px; font-weight: 600; text-decoration: none; border-bottom: 2px solid #353299; color: #353299; transition: all 0.3s ease; white-space: nowrap;font-size: 12px;">
                    PERMANENT
                </a>
            </div>
            
            <a href="{{route("user.work.jobs", ["type_of_user" => "employer", "type_of_work" => "permanent"])}}" 
               class="btn btn-primary add-job-btn job-tab-active" style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: auto;margin:3px;">
                 <em class="icon ni ni-plus"></em>
            </a>
            
            <style>
                .job-tab:not(.active):hover {
                    border-bottom: 2px solid #e6e9f2;
                    /* transform: translateY(-2px); */
                }
                
                /* .job-tab.active {
                    box-shadow: 0 4px 10px rgba(53, 50, 153, 0.3);
                } */
                
                /* Responsive styles */
                @media (max-width: 576px) {
                    .job-tabs-container {
                        width: 100%;
                        justify-content: center;
                        margin-bottom: 15px;
                    }
                    
                    .add-job-btn {
                        position: fixed;
                        bottom: 20px;
                        right: 20px;
                        margin: 0;
                        z-index: 999;
                        width: 60px;
                        height: 60px;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
                    }
                }
            </style>
        </div>
    </div>

    <div class="row" style="margin-top: -45px;">
        <div class="col-md-4">
            <div class="row " style="position: -webkit-sticky;
  position: sticky;top: 0">
                <div class="col-md-12" style="margin-top: 45px;">
                    <p class="text-dark">More filters</p>
                    <div style="display: flex;gap: 10px">
                        <div class="form-control-wrap" style="margin-bottom: 15px;flex: 1">
                            <div class="form-icon form-icon-left">
                                <em class="icon ni ni-search"></em>
                            </div>
                            <input type="text" class="form-control form-control-lg" name="search"
                                   id="searchPermanentJobOpportunities"
                                   value="{{ request('search') }}"
                                   placeholder="Search keywords" style="border-radius: 4px;">
                        </div>
                        
                    </div>
                    <div class="mb-3" style="display: flex; align-items: center; justify-content: space-between;">
                        <p class="mb-0"><em class="icon ni ni-bulb"></em> Hit enter to search</p>
                        @if(request('search'))
                            <a href="javascript:void(0)" onclick="clearSearch()" class="btn btn-sm btn-outline-secondary">Clear search</a>
                        @endif
                    </div>
                    <script>
                        document.getElementById('searchPermanentJobOpportunities').addEventListener('keypress', function(e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                applySearch();
                            }
                        });
                        
                        function applySearch() {
                            var searchValue = document.getElementById('searchPermanentJobOpportunities').value;
                            var currentUrl = new URL(window.location.href);
                            
                            if (searchValue.trim() !== '') {
                                currentUrl.searchParams.set('search', searchValue);
                            } else {
                                currentUrl.searchParams.delete('search');
                            }
                            
                            window.location.href = currentUrl.toString();
                        }
                        
                        function clearSearch() {
                            var currentUrl = new URL(window.location.href);
                            currentUrl.searchParams.delete('search');
                            window.location.href = currentUrl.toString();
                        }
                    </script>
                    <div class="d-none d-sm-block .d-sm-none .d-md-block">
                        <hr>

                        <div class="mb-2">
                            <label class="form-label">Search radius</label>
                            <div id="radBox" class="card card-bordered pt-2 pl-3 pr-2" data-toggle="modal" data-target="#searchRadiusModal" style="height: 46px;border-radius: 4px;display: flex;flex-direction: row">
                                <div class="text-muted" style="flex: 1" id="searchRadiusTrigger">Eg. 11km from me</div>
                                <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Categories</label>
                            <div class="card card-bordered pt-2 pl-3 pr-2" id="skillsBox"
                                 style="min-height: 46px;border-radius: 4px;display: flex;flex-direction: row"
                                 data-toggle="modal" data-target="#skillsModal">
                                <div class="text-muted" style="flex: 1" id="selectedSkillsBox">
                                    @if(request()->has('skills'))
                                        @php
                                            $selectedSkillIds = is_array(request()->skills) ? request()->skills : [request()->skills];
                                            $selectedSkillNames = App\Models\Skill::whereIn('id', $selectedSkillIds)->pluck('name')->toArray();
                                        @endphp
                                        @foreach($selectedSkillNames as $skillName)
                                            <span class="badge badge-dim badge-primary">{{ $skillName }}</span>
                                        @endforeach
                                    @else
                                        Eg. Barber, Fashion Designer
                                    @endif
                                </div>
                                <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Budget range</label>
                            <div class="card card-bordered pt-2 pl-3 pr-2" id="bugBox"
                                 style="height: 46px;border-radius: 4px;display: flex;flex-direction: row" data-toggle="modal" data-target="#budgetModal">
                                <div class="text-muted" style="flex: 1" id="budgetRange">Eg. Between GHS240 and GHS490</div>
                                <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-8" style="margin-top: 50px;">
            <div class="text-dark mb-3" style="display: flex; flex-direction: row;">
                <div style="flex:1">{{$count}} listings</div>
                <div style="display:flex; gap: 20px;border-bottom: 1px solid #dbdfea;">
                    <span class="text-primary" style="border-bottom: 3px solid #353299;"><b>All</b></span>
{{--                    <a href="#" class="text-dark">Saved</a>--}}
                </div>
            </div>

            <div class="row" style="margin-top: -5px;">
                @if($posts->count() > 0)
                    @foreach($posts as $post)
                       <div class="col-md-6">
                        @include('work.permanent.partials._post', ['post' => $post])
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
        </div>
    </div>
    
    <script>
    function copyShareUrl(postId) {
        var copyText = document.getElementById("share-url-" + postId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        // Show feedback
        var button = copyText.nextElementSibling;
        var originalText = button.innerHTML;
        button.innerHTML = "<em class='icon ni ni-check'></em> Copied!";
        setTimeout(function() {
            button.innerHTML = originalText;
        }, 2000);
    }
    </script>

    @include('work.permanent.partials._skills')
    @include('work.permanent.partials._radius')
    @include('work.permanent.partials._budget')

@endsection
