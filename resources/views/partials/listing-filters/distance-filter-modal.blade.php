<div class="modal fade zoom" tabindex="-1" id="searchRadiusModal" style="border-radius: 16px;">
        <div class="modal-dialog" role="document" style="border-radius: 16px;">
                <div class="modal-content" style="border-radius: 16px;">
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <em class="icon ni ni-cross"></em>
                        </a>
                        <div class="modal-header" style="border-bottom: none !important;">
                                <h4 class="modal-title"><b>Search Radius</b></h4>
                        </div>
                        <div class="modal-body">
                                <div class="row">
                                        <div class="col-md-12">
                                                <div style="margin-top: -25px;">
                                                        <p><em class="icon ni ni-bulb"></em> Open up job search radius by updating the search
                                                                radius input below. The bigger the value, the wider the search radius.</p>
                                                </div>
                                                <div class="input-group1 mb-3 mt-3">
                                                        <label for="radiusInput" style="margin-bottom: 5px;">Search Radius (km)</label>
                                                        <input type="number" min="2" value="10" max="100" class="form-control"
                                                                placeholder="Number between 2 and 100" name="radiusInput" id="radiusInput">
                                                </div>

                                                <div style="display: flex; justify-content: space-between; width: 100%;">
                                                        @if(request('radius'))
                                                        <button type="button" class="btn btn-outline-secondary" onclick="clearRadiusFilter()">Clear filter</button>
                                                        @else
                                                        <div></div>
                                                        @endif
                                                        <button type="button" class="btn btn-primary" id="applyRadiusBtn">Apply filter</button>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>

<script>
        console.log('Radius filter script loaded');

        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
                // Add event listener to the apply button
                var applyBtn = document.getElementById('applyRadiusBtn');
                if (applyBtn) {
                        console.log('Adding event listener to radius button');
                        applyBtn.addEventListener('click', function() {
                                console.log('Apply button clicked');
                                applyRadiusFilter();
                        });
                } else {
                        console.error('Could not find apply radius button');
                }

                // Initialize radius display if value exists in URL
                var urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('radius')) {
                        var radius = urlParams.get('radius');
                        var radiusText = 'Within ' + radius + ' km';
                        document.getElementById('searchRadiusTrigger').innerHTML = '<span class="text-dark"><b>' + radiusText + '</b></span>';
                        document.getElementById('radBox').classList.add('borderActive');

                        // Set the input value to the current radius
                        document.getElementById('radiusInput').value = radius;
                }
        });

        function applyRadiusFilter() {
                console.log('applyRadiusFilter called');
                var radius = document.getElementById('radiusInput').value;
                var currentUrl = new URL(window.location.href);

                // Validate input
                if (radius === '' || parseInt(radius) < 2 || parseInt(radius) > 100) {
                        alert('Please enter a valid radius between 2 and 100 km');
                        return;
                }

                // Set radius parameter
                currentUrl.searchParams.set('radius', radius);

                // Update radius display text
                var radiusText = 'Within ' + radius + ' km';
                document.getElementById('searchRadiusTrigger').innerHTML = '<span class="text-dark"><b>' + radiusText + '</b></span>';

                // Add active border to radius box
                document.getElementById('radBox').classList.add('borderActive');

                // Close modal
                console.log('Closing modal');
                try {
                        $('#searchRadiusModal').modal('hide');
                } catch (e) {
                        console.error('Error with jQuery modal hide:', e);
                        // Try alternative method
                        try {
                                document.querySelector('[data-dismiss="modal"]').click();
                        } catch (e2) {
                                console.error('Error with alternative modal close:', e2);
                        }
                }

                // Log the URL we're navigating to
                console.log('Navigating to:', currentUrl.toString());

                // Navigate to filtered URL
                window.location.href = currentUrl.toString();
        }

        function clearRadiusFilter() {
                console.log('clearRadiusFilter called');
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('radius');

                // Reset radius display text
                document.getElementById('searchRadiusTrigger').innerHTML = 'Eg. 11km from me';

                // Remove active border from radius box
                document.getElementById('radBox').classList.remove('borderActive');

                // Close modal
                console.log('Closing modal from clear function');
                try {
                        $('#searchRadiusModal').modal('hide');
                } catch (e) {
                        console.error('Error with jQuery modal hide:', e);
                        // Try alternative method
                        try {
                                document.querySelector('[data-dismiss="modal"]').click();
                        } catch (e2) {
                                console.error('Error with alternative modal close:', e2);
                        }
                }

                // Log the URL we're navigating to
                console.log('Navigating to:', currentUrl.toString());

                // Navigate to filtered URL
                window.location.href = currentUrl.toString();
        }
</script>