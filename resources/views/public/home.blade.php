@extends('layouts.master')

@section('title', 'Find Work, Hire Talent, Grow Together')

@section('styles')
<style>
/* ============================================================
   VORK HOME PAGE — Design System
   ============================================================ */

/* Full-width breakout from DashLite col-md-12 container */
.vork-fullwidth {
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    width: 100vw;
}

/* Guest: remove sidebar left-offset on the wrap */
body.has-sidebar .nk-wrap {
    margin-left: 0 !important;
}

/* Hide header search on public home page */
.nk-header-news {
    display: none !important;
}

/* Remove all padding between header and hero */
.nk-content,
.nk-content .container-fluid,
.nk-content .nk-content-inner,
.nk-content .nk-content-body,
.nk-content .nk-content-body > .row,
.nk-content .nk-content-body > .row > .col-md-12 {
    padding-top: 0 !important;
    padding-bottom: 0 !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
}

/* Inner container — max-width with horizontal padding */
.vork-inner {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

/* Section base */
.vork-section {
    padding: 80px 0;
}
.vork-section--grey {
    background: #f9fafb;
}
.vork-section__header {
    text-align: center;
    margin-bottom: 48px;
}
.vork-section__title {
    font-family: 'Sen', sans-serif;
    font-size: 32px;
    font-weight: 800;
    color: #1a1a2e;
    margin-bottom: 10px;
    line-height: 1.2;
}
.vork-section__subtitle {
    font-size: 16px;
    color: #8094ae;
    margin: 0;
}
.vork-section__more {
    text-align: center;
    margin-top: 40px;
}

/* ============================================================
   HERO
   ============================================================ */
.vork-hero {
    background: linear-gradient(135deg, #353299 0%, #5e5ce6 55%, #7b79f0 100%);
    padding: 96px 0 80px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}
.vork-hero::before {
    content: '';
    position: absolute;
    top: -80px; right: -80px;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.04);
    pointer-events: none;
}
.vork-hero::after {
    content: '';
    position: absolute;
    bottom: -100px; left: -100px;
    width: 500px; height: 500px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.03);
    pointer-events: none;
}
.vork-hero__eyebrow {
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 2.5px;
    text-transform: uppercase;
    opacity: 0.72;
    margin-bottom: 20px;
}
.vork-hero__title {
    font-family: 'Sen', sans-serif;
    font-size: 56px;
    font-weight: 800;
    line-height: 1.15;
    color: white;
    margin-bottom: 20px;
}
.vork-hero__subtitle {
    font-size: 18px;
    opacity: 0.88;
    max-width: 520px;
    margin: 0 auto 40px;
    line-height: 1.65;
}
.vork-search {
    display: flex;
    max-width: 680px;
    margin: 0 auto 32px;
    background: white;
    border-radius: 12px;
    padding: 6px 6px 6px 20px;
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.22);
    gap: 8px;
    align-items: center;
}
.vork-search__input {
    flex: 1;
    border: none;
    outline: none;
    font-size: 16px;
    color: #1a1a2e;
    background: transparent;
    min-width: 0;
    padding: 10px 0;
}
.vork-search__input::placeholder {
    color: #aab0be;
}
.vork-search__btn {
    background: #353299;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: background 0.2s, transform 0.15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.vork-search__btn:hover {
    background: #2a2677;
    transform: translateY(-1px);
}
.vork-hero__tags {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 8px;
}
.vork-hero__tags-label {
    font-size: 13px;
    opacity: 0.72;
    margin-right: 2px;
}
.vork-tag {
    background: rgba(255, 255, 255, 0.15);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.28);
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: background 0.2s;
}
.vork-tag:hover {
    background: rgba(255, 255, 255, 0.28);
    color: white;
    text-decoration: none;
}

/* ============================================================
   STATS BAR
   ============================================================ */
.vork-stats {
    background: white;
    border-bottom: 1px solid #e5e9f2;
    box-shadow: 0 4px 16px rgba(53, 50, 153, 0.06);
    padding: 32px 0;
}
.vork-stats__grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
}
.vork-stats__item {
    text-align: center;
    padding: 12px 24px;
    border-right: 1px solid #e5e9f2;
}
.vork-stats__item:last-child {
    border-right: none;
}
.vork-stats__number {
    font-family: 'Sen', sans-serif;
    font-size: 30px;
    font-weight: 800;
    color: #353299;
    line-height: 1.1;
}
.vork-stats__label {
    font-size: 13px;
    color: #8094ae;
    margin-top: 4px;
    font-weight: 500;
}

/* ============================================================
   BROWSE BY TYPE
   ============================================================ */
.vork-types__grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
}
.vork-type-card {
    background: white;
    border: 2px solid #e5e9f2;
    border-radius: 16px;
    padding: 28px 20px 24px;
    text-align: center;
    text-decoration: none;
    color: #364a63;
    transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
    display: block;
}
.vork-type-card:hover {
    border-color: #353299;
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(53, 50, 153, 0.12);
    color: #353299;
    text-decoration: none;
}
.vork-type-card__icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 24px;
}
.vork-type-card__icon--quick   { background: #fff3cd; color: #856404; }
.vork-type-card__icon--fixed   { background: #cff4fc; color: #055160; }
.vork-type-card__icon--perm    { background: #d1e7dd; color: #0f5132; }
.vork-type-card__icon--p2p     { background: #e8d5ff; color: #491fa6; }
.vork-type-card__icon--vol     { background: #fde8d8; color: #8f3b00; }
.vork-type-card__count {
    font-size: 28px;
    font-weight: 800;
    font-family: 'Sen', sans-serif;
    color: #353299;
    line-height: 1;
    margin-bottom: 6px;
}
.vork-type-card__name {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 6px;
}
.vork-type-card__desc {
    font-size: 12px;
    color: #8094ae;
    line-height: 1.4;
}

/* ============================================================
   FEATURED LISTINGS
   ============================================================ */
.vork-listings__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}
.vork-listing-card {
    background: white;
    border: 1px solid #e5e9f2;
    border-radius: 16px;
    padding: 24px;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
    transition: border-color 0.2s, box-shadow 0.2s, transform 0.15s;
}
.vork-listing-card:hover {
    border-color: #353299;
    box-shadow: 0 8px 28px rgba(53, 50, 153, 0.1);
    transform: translateY(-3px);
    text-decoration: none;
    color: inherit;
}
.vork-listing-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
}
.vork-badge {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.badge--quick { background: #fff3cd; color: #856404; }
.badge--fixed { background: #cff4fc; color: #055160; }
.badge--perm  { background: #d1e7dd; color: #0f5132; }
.badge--p2p   { background: #e8d5ff; color: #491fa6; }
.badge--vol   { background: #fde8d8; color: #8f3b00; }
.vork-listing-card__time {
    font-size: 12px;
    color: #8094ae;
}
.vork-listing-card__title {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 10px;
    line-height: 1.4;
}
.vork-listing-card__employer {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #526484;
    margin-bottom: 14px;
}
.vork-listing-card__meta {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 16px;
    flex: 1;
}
.vork-listing-card__meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #8094ae;
}
.vork-listing-card__budget {
    color: #1a8754;
    font-weight: 600;
}
.vork-listing-card__footer {
    border-top: 1px solid #f0f2f5;
    padding-top: 14px;
    margin-top: auto;
}
.vork-listing-card__cta {
    font-size: 13px;
    font-weight: 600;
    color: #353299;
    display: flex;
    align-items: center;
    gap: 4px;
}
.vork-empty-state {
    text-align: center;
    padding: 48px;
    color: #8094ae;
}
.vork-empty-state p {
    margin-top: 12px;
    font-size: 15px;
}

/* ============================================================
   SKILL CATEGORIES
   ============================================================ */
.vork-skills__grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 16px;
    margin-bottom: 40px;
}
.vork-skill-card {
    background: white;
    border: 1px solid #e5e9f2;
    border-radius: 12px;
    padding: 20px 16px;
    text-align: center;
    text-decoration: none;
    color: #364a63;
    transition: all 0.2s;
}
.vork-skill-card:hover {
    border-color: #353299;
    color: #353299;
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(53, 50, 153, 0.1);
    text-decoration: none;
}
.vork-skill-card__icon {
    font-size: 28px;
    color: #353299;
    margin-bottom: 10px;
    line-height: 1;
}
.vork-skill-card__name {
    font-size: 13px;
    font-weight: 600;
    line-height: 1.3;
}

/* Modal overrides */
#allCategoriesModal .modal-header {
    background: linear-gradient(135deg, #353299 0%, #5e5ce6 100%);
    color: white;
    border-radius: 16px 16px 0 0;
    padding: 20px 28px;
}
#allCategoriesModal .modal-title { color: white !important; }
#allCategoriesModal .close { color: white; opacity: 1; text-shadow: none; }
#allCategoriesModal .close:hover { color: #f0f0f0; }
#categorySearch:focus {
    border-color: #353299;
    box-shadow: 0 0 0 3px rgba(53, 50, 153, 0.1);
    outline: none;
}
#categoriesContainer::-webkit-scrollbar { width: 6px; }
#categoriesContainer::-webkit-scrollbar-track { background: #f5f6fa; border-radius: 4px; }
#categoriesContainer::-webkit-scrollbar-thumb { background: #353299; border-radius: 4px; }

/* ============================================================
   HOW IT WORKS
   ============================================================ */
.vork-steps__grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px;
    position: relative;
}
.vork-steps__grid::before {
    content: '';
    position: absolute;
    top: 66px;
    left: calc(16.66% + 20px);
    right: calc(16.66% + 20px);
    height: 2px;
    background: linear-gradient(90deg, #353299 0%, #5e5ce6 100%);
    opacity: 0.18;
}
.vork-step {
    text-align: center;
    padding: 0 8px;
    position: relative;
}
.vork-step__number {
    font-family: 'Sen', sans-serif;
    font-size: 12px;
    font-weight: 800;
    color: #353299;
    opacity: 0.4;
    letter-spacing: 2px;
    margin-bottom: 12px;
}
.vork-step__icon-wrap {
    width: 72px;
    height: 72px;
    background: white;
    border: 2px solid #353299;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 26px;
    color: #353299;
    position: relative;
    z-index: 1;
}
.vork-step__title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a2e;
    margin-bottom: 12px;
}
.vork-step__desc {
    font-size: 14px;
    color: #8094ae;
    line-height: 1.65;
    margin: 0;
}

/* ============================================================
   CTA SECTION
   ============================================================ */
.vork-cta {
    background: linear-gradient(135deg, #353299 0%, #5e5ce6 100%);
    border-radius: 24px;
    padding: 64px 48px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}
.vork-cta::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 300px; height: 300px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.05);
    pointer-events: none;
}
.vork-cta__eyebrow {
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    opacity: 0.7;
    font-weight: 700;
    margin-bottom: 16px;
}
.vork-cta__title {
    font-family: 'Sen', sans-serif;
    font-size: 36px;
    font-weight: 800;
    color: white;
    margin-bottom: 16px;
}
.vork-cta__subtitle {
    font-size: 17px;
    opacity: 0.88;
    max-width: 520px;
    margin: 0 auto 36px;
    line-height: 1.65;
}
.vork-cta__buttons {
    display: flex;
    gap: 16px;
    justify-content: center;
    flex-wrap: wrap;
}

/* ============================================================
   BUTTONS
   ============================================================ */
.vork-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    border: none;
}
.vork-btn--primary {
    background: #353299;
    color: white;
}
.vork-btn--primary:hover {
    background: #2a2677;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(53, 50, 153, 0.3);
}
.vork-btn--outline {
    border: 2px solid #353299;
    color: #353299;
    background: transparent;
}
.vork-btn--outline:hover {
    background: #353299;
    color: white;
    text-decoration: none;
}
.vork-btn--outline-white {
    border: 2px solid rgba(255, 255, 255, 0.6);
    color: white;
    background: transparent;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 14px 32px;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
}
.vork-btn--outline-white:hover {
    background: rgba(255, 255, 255, 0.14);
    color: white;
    text-decoration: none;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 992px) {
    .vork-types__grid    { grid-template-columns: repeat(3, 1fr); }
    .vork-listings__grid { grid-template-columns: repeat(2, 1fr); }
    .vork-skills__grid   { grid-template-columns: repeat(4, 1fr); }
    .vork-hero__title    { font-size: 44px; }
}
@media (max-width: 768px) {
    .vork-hero           { padding: 64px 0 56px; }
    .vork-hero__title    { font-size: 32px; }
    .vork-hero__subtitle { font-size: 15px; }
    .vork-search         { flex-direction: column; padding: 12px; }
    .vork-search__btn    { width: 100%; justify-content: center; }
    .vork-section        { padding: 56px 0; }
    .vork-section__title { font-size: 26px; }
    .vork-stats__grid    { grid-template-columns: repeat(2, 1fr); }
    .vork-stats__item:nth-child(2) { border-right: none; }
    .vork-types__grid    { grid-template-columns: repeat(2, 1fr); }
    .vork-listings__grid { grid-template-columns: 1fr; }
    .vork-skills__grid   { grid-template-columns: repeat(3, 1fr); }
    .vork-steps__grid    { grid-template-columns: 1fr; gap: 32px; }
    .vork-steps__grid::before { display: none; }
    .vork-cta            { padding: 48px 24px; }
    .vork-cta__title     { font-size: 26px; }
}
@media (max-width: 480px) {
    .vork-skills__grid   { grid-template-columns: repeat(2, 1fr); }
}
</style>
@endsection

@section('content')

{{-- ============================================================
     HERO
     ============================================================ --}}
<div class="vork-hero vork-fullwidth">
    <div class="vork-inner">
        <div class="vork-hero__eyebrow">Ghana's Work Marketplace</div>
        <h1 class="vork-hero__title">
            Find Work.<br>
            Hire Talent.<br>
            Grow Together.
        </h1>
        <p class="vork-hero__subtitle">
            Browse quick gigs, fixed contracts, permanent roles, and direct hire — all in one place across Ghana.
        </p>

        <div class="vork-search">
            <input type="text" id="jobSearch" class="vork-search__input"
                   placeholder="Search by job title, skill, or location..." />
            <button class="vork-search__btn" onclick="searchJobs()">
                <em class="icon ni ni-search"></em> Search
            </button>
        </div>

        <div class="vork-hero__tags">
            <span class="vork-hero__tags-label">Popular:</span>
            <a href="{{ route('public.jobs.index') }}?search=Developer" class="vork-tag">Developer</a>
            <a href="{{ route('public.jobs.index') }}?search=Chef" class="vork-tag">Chef</a>
            <a href="{{ route('public.jobs.index') }}?search=Driver" class="vork-tag">Driver</a>
            <a href="{{ route('public.jobs.index') }}?search=Nurse" class="vork-tag">Nurse</a>
            <a href="{{ route('public.jobs.index') }}?search=Accra" class="vork-tag">Accra</a>
            <a href="{{ route('public.jobs.index') }}?search=Kumasi" class="vork-tag">Kumasi</a>
            <a href="{{ route('public.jobs.index') }}?search=Lawyer" class="vork-tag">Lawyer</a>
        </div>
    </div>
</div>

{{-- ============================================================
     STATS BAR
     ============================================================ --}}
<div class="vork-stats vork-fullwidth">
    <div class="vork-inner">
        <div class="vork-stats__grid">
            <div class="vork-stats__item">
                <div class="vork-stats__number">{{ number_format($totalJobs) }}+</div>
                <div class="vork-stats__label">Active Opportunities</div>
            </div>
            <div class="vork-stats__item">
                <div class="vork-stats__number">{{ number_format($totalUsers) }}+</div>
                <div class="vork-stats__label">Registered Professionals</div>
            </div>
            <div class="vork-stats__item">
                <div class="vork-stats__number">50+</div>
                <div class="vork-stats__label">Skill Categories</div>
            </div>
            <div class="vork-stats__item">
                <div class="vork-stats__number">5</div>
                <div class="vork-stats__label">Ways to Work</div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     BROWSE BY TYPE
     ============================================================ --}}
<div class="vork-section">
    <div class="vork-inner">
        <div class="vork-section__header">
            <h2 class="vork-section__title">Browse by work type</h2>
            <p class="vork-section__subtitle">Choose the type of opportunity that fits your goals</p>
        </div>
        <div class="vork-types__grid">

            <a href="{{ route('public.jobs.type', ['type' => 'quick-job']) }}" class="vork-type-card">
                <div class="vork-type-card__icon vork-type-card__icon--quick">
                    <em class="icon ni ni-clock"></em>
                </div>
                <div class="vork-type-card__count">{{ $quickJobCount }}</div>
                <div class="vork-type-card__name">Quick Job</div>
                <div class="vork-type-card__desc">Short-term gigs, up to 1 month</div>
            </a>

            <a href="{{ route('public.jobs.type', ['type' => 'fixed-term']) }}" class="vork-type-card">
                <div class="vork-type-card__icon vork-type-card__icon--fixed">
                    <em class="icon ni ni-briefcase"></em>
                </div>
                <div class="vork-type-card__count">{{ $fixedTermCount }}</div>
                <div class="vork-type-card__name">Fixed Term</div>
                <div class="vork-type-card__desc">Contracts from 1 month to 1 year</div>
            </a>

            <a href="{{ route('public.jobs.type', ['type' => 'permanent']) }}" class="vork-type-card">
                <div class="vork-type-card__icon vork-type-card__icon--perm">
                    <em class="icon ni ni-building"></em>
                </div>
                <div class="vork-type-card__count">{{ $permanentCount }}</div>
                <div class="vork-type-card__name">Permanent</div>
                <div class="vork-type-card__desc">Full-time career positions</div>
            </a>

            <a href="{{ route('public.jobs.type', ['type' => 'p2p']) }}" class="vork-type-card">
                <div class="vork-type-card__icon vork-type-card__icon--p2p">
                    <em class="icon ni ni-users"></em>
                </div>
                <div class="vork-type-card__count">{{ $p2pCount }}</div>
                <div class="vork-type-card__name">Direct Hire</div>
                <div class="vork-type-card__desc">Hire someone directly, peer-to-peer</div>
            </a>

            @auth
            <a href="{{ route('user.volunteerism.list') }}" class="vork-type-card">
            @else
            <a href="{{ route('onboarding.register') }}" class="vork-type-card">
            @endauth
                <div class="vork-type-card__icon vork-type-card__icon--vol">
                    <em class="icon ni ni-heart"></em>
                </div>
                <div class="vork-type-card__count">{{ $volunteerCount }}</div>
                <div class="vork-type-card__name">Volunteer</div>
                <div class="vork-type-card__desc">Give back to your community</div>
            </a>

        </div>
    </div>
</div>

{{-- ============================================================
     FEATURED LISTINGS
     ============================================================ --}}
<div class="vork-section vork-section--grey">
    <div class="vork-inner">
        <div class="vork-section__header">
            <h2 class="vork-section__title">Latest opportunities</h2>
            <p class="vork-section__subtitle">Fresh posts added recently across all work types</p>
        </div>

        @if(isset($recentPosts) && $recentPosts->count() > 0)
        <div class="vork-listings__grid">
            @foreach($recentPosts as $post)
            @php
                $badgeClass = match($post->type) {
                    'QUICK_JOB'      => 'badge--quick',
                    'FIXED_TERM_JOB' => 'badge--fixed',
                    'PERMANENT_JOB'  => 'badge--perm',
                    'P2P'            => 'badge--p2p',
                    default          => 'badge--quick',
                };
                $typeLabel = match($post->type) {
                    'QUICK_JOB'      => 'Quick Job',
                    'FIXED_TERM_JOB' => 'Fixed Term',
                    'PERMANENT_JOB'  => 'Permanent',
                    'P2P'            => 'Direct Hire',
                    default          => $post->type,
                };
                $typeSlug = match($post->type) {
                    'QUICK_JOB'      => 'quick-job',
                    'FIXED_TERM_JOB' => 'fixed-term',
                    'PERMANENT_JOB'  => 'permanent',
                    'P2P'            => 'p2p',
                    default          => 'quick-job',
                };
                $employerName = in_array($post->type, ['FIXED_TERM_JOB', 'PERMANENT_JOB'])
                    ? ($post->employer ?? $post->user->name ?? 'Unknown')
                    : ($post->user->name ?? 'Unknown');
            @endphp
            <a href="{{ route('public.jobs.show', ['type' => $typeSlug, 'uuid' => $post->id]) }}"
               class="vork-listing-card">
                <div class="vork-listing-card__header">
                    <span class="vork-badge {{ $badgeClass }}">{{ $typeLabel }}</span>
                    <span class="vork-listing-card__time">{{ $post['createdOn'] }}</span>
                </div>
                <h3 class="vork-listing-card__title">{{ Str::limit($post['display_title'], 52) }}</h3>
                <div class="vork-listing-card__employer">
                    <em class="icon ni ni-building"></em>
                    <span>{{ Str::limit($employerName, 36) }}</span>
                </div>
                <div class="vork-listing-card__meta">
                    @if($post->location)
                    <div class="vork-listing-card__meta-item">
                        <em class="icon ni ni-map-pin"></em>
                        <span>{{ Str::limit($post->location, 32) }}</span>
                    </div>
                    @endif
                    @if($post->min_budget && $post->max_budget)
                    <div class="vork-listing-card__meta-item vork-listing-card__budget">
                        <em class="icon ni ni-coins"></em>
                        <span>GHS {{ number_format($post->min_budget) }} – {{ number_format($post->max_budget) }}</span>
                    </div>
                    @endif
                    @if($post['industry_name'])
                    <div class="vork-listing-card__meta-item">
                        <em class="icon ni ni-tag"></em>
                        <span>{{ $post['industry_name'] }}</span>
                    </div>
                    @endif
                </div>
                <div class="vork-listing-card__footer">
                    <span class="vork-listing-card__cta">
                        View Details <em class="icon ni ni-arrow-right"></em>
                    </span>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="vork-empty-state">
            <em class="icon ni ni-briefcase" style="font-size:48px; color:#e5e9f2;"></em>
            <p>No opportunities posted yet. Check back soon!</p>
        </div>
        @endif

        <div class="vork-section__more">
            <a href="{{ route('public.jobs.type', ['type' => 'quick-job']) }}" class="vork-btn vork-btn--outline">
                <em class="icon ni ni-grid"></em> Browse All Opportunities
            </a>
        </div>
    </div>
</div>

{{-- ============================================================
     BROWSE BY CATEGORY (SKILLS)
     ============================================================ --}}
<div class="vork-section">
    <div class="vork-inner">
        <div class="vork-section__header">
            <h2 class="vork-section__title">Browse by skill category</h2>
            <p class="vork-section__subtitle">Find opportunities matching your expertise</p>
        </div>

        @php
        $skillIconMap = [
            'Actor'              => 'ni-star',
            'App Developer'      => 'ni-code',
            'Architect'          => 'ni-home',
            'Audio Engineer'     => 'ni-mic',
            'Baker'              => 'ni-tag',
            'Barber'             => 'ni-scissors',
            'Camera Operator'    => 'ni-camera',
            'Carer'              => 'ni-heart',
            'Carpenter'          => 'ni-tool',
            'Chef'               => 'ni-coffee',
            'Cleaner'            => 'ni-spark',
            'Consultant'         => 'ni-bulb',
            'Designer'           => 'ni-pen',
            'Doctor'             => 'ni-activity',
            'Driver'             => 'ni-truck',
            'Electrician'        => 'ni-light-on',
            'Engineer'           => 'ni-setting',
            'Farmer'             => 'ni-growth',
            'Graphic Designer'   => 'ni-pen',
            'Guard'              => 'ni-shield',
            'Lawyer'             => 'ni-book',
            'Mechanic'           => 'ni-setting-alt',
            'Nurse'              => 'ni-activity',
            'Photographer'       => 'ni-camera',
            'Plumber'            => 'ni-wrench',
            'Teacher'            => 'ni-book-read',
            'Welder'             => 'ni-spark',
            'Writer'             => 'ni-pen2',
            'Accountant'         => 'ni-calc',
            'HR'                 => 'ni-users',
            'Marketing'          => 'ni-chart-up',
            'Sales'              => 'ni-trend-up',
            'Security'           => 'ni-shield-check',
            'Tailor'             => 'ni-tag-alt',
            'Waiter'             => 'ni-coffee',
        ];
        $defaultSkillIcon = 'ni-briefcase';
        @endphp

        <div class="vork-skills__grid">
            @if(isset($topSkills) && $topSkills->count() > 0)
                @foreach($topSkills->take(12) as $skill)
                @php $skillIcon = $skillIconMap[$skill->name] ?? $defaultSkillIcon; @endphp
                <a href="{{ route('public.jobs.index') }}?search={{ urlencode($skill->name) }}" class="vork-skill-card">
                    <div class="vork-skill-card__icon">
                        <em class="icon ni {{ $skillIcon }}"></em>
                    </div>
                    <div class="vork-skill-card__name">{{ $skill->name }}</div>
                </a>
                @endforeach
            @endif
        </div>

        @if(isset($allSkills) && $allSkills->count() > 12)
        <div class="vork-section__more">
            <button class="vork-btn vork-btn--outline" data-toggle="modal" data-target="#allCategoriesModal">
                <em class="icon ni ni-grid"></em> See All {{ $allSkills->count() }} Categories
            </button>
        </div>
        @endif
    </div>
</div>

{{-- All Categories Modal --}}
<div class="modal fade" id="allCategoriesModal" tabindex="-1" role="dialog" aria-labelledby="allCategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-header" style="border-bottom: 1px solid #e5e9f2;">
                <h3 class="modal-title" id="allCategoriesModalLabel">
                    <em class="icon ni ni-grid"></em> All Skill Categories
                </h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 32px;">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <input type="text" id="categorySearch" class="form-control"
                               placeholder="Search categories..."
                               style="border-radius: 8px; padding: 12px 20px; border: 1px solid #e5e9f2;">
                    </div>
                </div>
                <div id="categoriesContainer"
                     style="display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; max-height: 480px; overflow-y: auto;">
                    @if(isset($allSkills) && $allSkills->count() > 0)
                        @foreach($allSkills as $skill)
                        <a href="{{ route('public.jobs.index') }}?search={{ urlencode($skill->name) }}"
                           class="vork-skill-card category-item"
                           data-category="{{ strtolower($skill->name) }}"
                           style="margin: 0;">
                            <div class="vork-skill-card__icon">
                                <em class="icon ni {{ $skillIconMap[$skill->name] ?? $defaultSkillIcon }}"></em>
                            </div>
                            <div class="vork-skill-card__name">{{ $skill->name }}</div>
                        </a>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     HOW IT WORKS
     ============================================================ --}}
<div class="vork-section vork-section--grey">
    <div class="vork-inner">
        <div class="vork-section__header">
            <h2 class="vork-section__title">How Vork works</h2>
            <p class="vork-section__subtitle">Get started in three simple steps</p>
        </div>
        <div class="vork-steps__grid">
            <div class="vork-step">
                <div class="vork-step__number">01</div>
                <div class="vork-step__icon-wrap">
                    <em class="icon ni ni-user-add"></em>
                </div>
                <h3 class="vork-step__title">Create your profile</h3>
                <p class="vork-step__desc">Sign up in minutes. Add your skills, experience, and what kind of work you are looking for.</p>
            </div>
            <div class="vork-step">
                <div class="vork-step__number">02</div>
                <div class="vork-step__icon-wrap">
                    <em class="icon ni ni-search"></em>
                </div>
                <h3 class="vork-step__title">Discover opportunities</h3>
                <p class="vork-step__desc">Browse quick gigs, contracts, or permanent roles across Ghana. Filter by location, budget, and skill.</p>
            </div>
            <div class="vork-step">
                <div class="vork-step__number">03</div>
                <div class="vork-step__icon-wrap">
                    <em class="icon ni ni-check-circle"></em>
                </div>
                <h3 class="vork-step__title">Apply and get hired</h3>
                <p class="vork-step__desc">Apply with one tap. Employers review your profile and connect with you directly through Vork.</p>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     CTA — GUEST ONLY
     ============================================================ --}}
@guest
<div class="vork-section">
    <div class="vork-inner">
        <div class="vork-cta">
            <div class="vork-cta__eyebrow">Join Vork today — it is free</div>
            <h2 class="vork-cta__title">Your next opportunity is waiting</h2>
            <p class="vork-cta__subtitle">
                Join {{ number_format($totalUsers) }}+ professionals already on Vork.
                Find work, hire talent, or volunteer — all in one place.
            </p>
            <div class="vork-cta__buttons">
                <a href="{{ route('onboarding.register') }}" class="vork-btn vork-btn--primary" style="background: white; color: #353299;">
                    <em class="icon ni ni-user-add"></em> Create Free Account
                </a>
                <a href="{{ route('login') }}" class="vork-btn vork-btn--outline-white">
                    <em class="icon ni ni-signin"></em> Sign In
                </a>
            </div>
        </div>
    </div>
</div>
@endguest

@endsection

@section('scripts')
<script>
    function searchJobs() {
        const query = document.getElementById('jobSearch').value.trim();
        const base = "{{ route('public.jobs.index') }}";
        window.location.href = query ? base + '?search=' + encodeURIComponent(query) : base;
    }

    document.getElementById('jobSearch').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') searchJobs();
    });

    // Category modal search filter
    const categorySearchInput = document.getElementById('categorySearch');
    if (categorySearchInput) {
        categorySearchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase();
            document.querySelectorAll('.category-item').forEach(function (item) {
                const name = item.getAttribute('data-category');
                item.style.display = name.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }
</script>
@endsection
