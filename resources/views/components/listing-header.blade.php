
@php
        $route = '';
        switch ($type){
                case 'Quick Jobs':
                        $route = route('user.work.jobs', ['type_of_user' => 'seeker', 'type_of_work' => 'quick-job']);
                        break;
                case 'Part Time Jobs':
                        $route = route('user.work.jobs', ['type_of_user' => 'seeker', 'type_of_work' => 'fixed-term']);
                        break;
                case 'Permanent Jobs':
                        $route = route('user.work.jobs', ['type_of_user' => 'seeker', 'type_of_work' => 'permanent']);
                        break;
                default:
                        $route = route('user.work.jobs', ['type_of_user' => 'seeker', 'type_of_work' => 'quick-job']);
                        break;
        }
@endphp
<div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
                <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title"><em class="ni ni-{{ $icon }}"></em> {{ $module }} </h3>
                        <div class="nk-block-des text-soft">
                                <p class="hide-mb-sm hide-mb-xs md">
                                <nav>
                                        <ul class="breadcrumb breadcrumb-arrow">
                                                <li class="breadcrumb-item"><a href="#">{{ $type }}</a></li>
                                                <li class="breadcrumb-item"><a href="#">{{$description}}</a></li>
                                        </ul>
                                </nav>
                                </p>
                        </div>
                </div>
                <div class="nk-block-head-content">
                        <a href="{{$route}}"
                                class="btn btn-outline-lighter"><span>Back</span></a>
                </div>
        </div>
</div>