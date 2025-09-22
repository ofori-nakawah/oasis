<div class="row" style="display: flex; align-items: stretch; margin-top: -45px;">
        <div class="col-md-8" style="margin-top: 50px;">
                @yield("listing-details")
        </div>
        <div class="col-md-4 hide-on-mobile" style="margin-top: 50px;">
                @yield("ads")
        </div>
</div>

<style>
        @media (max-width: 640px) {
                .hide-on-mobile {
                        display: none;
                }
        }
</style>