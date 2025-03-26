@extends('layouts.master')

<!-- extend the styles -->
@section('styles')
@yield("styles")
@endsection

@section('title')
{{ $title }}
@endsection

@section('content')
@yield("header")
@yield("navigation")

<div class="row" style="margin-top: -45px;">
        <div class="col-md-4">
                <div class="row " style="position: -webkit-sticky;
  position: sticky;top: 0">
                        <div class="col-md-12" style="margin-top: 45px;">
                                @yield("filters")
                        </div>
                </div>
        </div>
        <div class="col-md-8" style="margin-top: 50px;">
                @yield("listings")
        </div>
</div>

@yield("filter-modals")
@endsection



<!-- extend the scripts -->
@section('scripts')
@yield("scripts")
@endsection