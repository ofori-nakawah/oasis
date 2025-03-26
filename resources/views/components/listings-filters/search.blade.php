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