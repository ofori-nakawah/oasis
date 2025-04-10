@extends("layouts.listing-details", ['title' => 'Work'])

@section('header')
@component('components.listing-header', ['module' => 'Work', 'icon' => 'ni ni-briefcase', 'type' => 'Permanent Jobs', 'description' => 'Explore a world of endless opportunities around you'])@endcomponent
@endsection

@section('listing-details')
@include('utilities.alerts.alerts')
@include('partials.shared.listing-container', ['isShowDetails' => true])
@endsection

@section('ads')
@include('partials.shared.ads-container')
@endsection

@section('other-related-listings')
@include('partials.shared.other-related-listings')
@endsection