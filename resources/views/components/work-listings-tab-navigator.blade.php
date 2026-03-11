@php
$defaultTabClasses = "padding: 10px 15px; margin: 3px; border-radius: 8px; font-weight: 500; text-decoration: none; color: #364a63; transition: all 0.3s ease; white-space: nowrap;font-size: 12px;";

$activeTabClasses = "padding: 10px 15px; margin: 3px; border-radius: 8px; font-weight: 600; text-decoration: none; border-bottom: 2px solid #353299; color: #353299; transition: all 0.3s ease; white-space: nowrap;font-size: 12px;";

// Use public routes for guests, auth routes for logged-in users
if (auth()->check()) {
    $routes = [
        "casual" => route("user.work.jobs", ["type_of_user" => "seeker", "type_of_work" => "quick-job"]),
        "fixed-term" => route("user.work.jobs", ["type_of_user" => "seeker", "type_of_work" => "fixed-term"]),
        "permanent" => route("user.work.jobs", ["type_of_user" => "seeker", "type_of_work" => "permanent"]),
    ];
    $isActiveTab = request()->routeIs('user.work.jobs');
} else {
    $routes = [
        "casual" => route("public.jobs.type", ["type" => "quick-job"]),
        "fixed-term" => route("public.jobs.type", ["type" => "fixed-term"]),
        "permanent" => route("public.jobs.type", ["type" => "permanent"]),
    ];
    $isActiveTab = request()->routeIs('public.jobs.type') || request()->routeIs('public.jobs.index');
}
@endphp

<div class="row">
        <div class="col-md-12 col-sm-12" style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <div class="job-tabs-container" style="background-color: #f5f6fa; border-radius: 8px; padding: 5px; display: flex; flex-wrap: wrap; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 10px;">
                        <a href="{{$routes['casual']}}" class="job-tab" style="{{ $isActiveTab && (request('type_of_work') == 'quick-job' || request('type') == 'quick-job') ? $activeTabClasses : $defaultTabClasses }}">
                                CASUAL
                        </a>
                        <a href="{{$routes['fixed-term']}}" class="job-tab" style="{{ $isActiveTab && (request('type_of_work') == 'fixed-term' || request('type') == 'fixed-term') ? $activeTabClasses : $defaultTabClasses }}">
                                FIXED TERM
                        </a>
                        <a href="{{$routes['permanent']}}" class="job-tab" style="{{ $isActiveTab && (request('type_of_work') == 'permanent' || request('type') == 'permanent') ? $activeTabClasses : $defaultTabClasses }}">
                                PERMANENT
                        </a>
                </div>

                @auth
                <a href="{{route("user.work.jobs", ["type_of_user" => "employer", "type_of_work" => request('type_of_work') ?? request('type') ?? 'quick-job'])}}"
                        class="btn btn-primary add-job-btn job-tab-active" style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: auto;margin:3px;">
                        <em class="icon ni ni-plus"></em>
                </a>
                @endauth

                @guest
                <a href="{{route("user.work.jobs", ["type_of_user" => "employer", "type_of_work" => request('type') ?? 'quick-job'])}}"
                        class="btn btn-primary add-job-btn job-tab-active" style="width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: auto;margin:3px;">
                        <em class="icon ni ni-plus"></em>
                </a>
                @endguest

                <style>
                        .job-tab:not(.active):hover {
                                border-bottom: 2px solid #e6e9f2;
                        }

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