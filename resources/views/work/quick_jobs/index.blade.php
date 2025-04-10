@extends('layouts.listings', ['title' => 'Work'])

@section('header')
@component('components.listing-header', ['module' => 'Work', 'icon' => 'ni ni-briefcase', 'type' => 'Quick Jobs', 'description' => 'Explore a world of endless opportunities around you'])@endcomponent
@endsection

@section('navigation')
@component('components.work-listings-tab-navigator')@endcomponent
@endsection

@section('filters')
@component('components.listing-filters', ['module' => 'Work', 'type' => 'casual'])@endcomponent
@endsection

@section('listings')
@include('partials.shared.listings')
@endsection

@section('filter-modals')
@include('partials.listing-filters.distance-filter-modal')
@include('partials.listing-filters.categories-filter-modal')
@include('partials.listing-filters.budget-filter-modal')
@endsection