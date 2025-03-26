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

<div class="row" style="margin-top: -45px;">
        <div class="col-md-6" style="margin-top: 50px;">
                @yield("listing-details")
        </div>
        <div class="col-md-4">
                @yield("ads")
        </div>
</div>

@yield("other-related-listings")
@yield("recommended-listings-based-on-profile")
@endsection

<!-- extend the scripts -->
@section('scripts')
@yield("scripts")
@endsection