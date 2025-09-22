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

@include('partials.shared.listing-details-container')

@yield("other-related-listings")
@endsection

<!-- extend the scripts -->
@section('scripts')
@yield("scripts")
@endsection