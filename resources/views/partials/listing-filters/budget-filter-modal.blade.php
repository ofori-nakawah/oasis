<div class="modal fade zoom" tabindex="-1" id="budgetModal" style="border-radius: 16px;">
        <div class="modal-dialog" role="document" style="border-radius: 16px;">
                <div class="modal-content" style="border-radius: 16px;">
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <em class="icon ni ni-cross"></em>
                        </a>
                        <div class="modal-header" style="border-bottom: none !important;">
                                <h4 class="modal-title"><b>Budget Range</b></h4>
                        </div>
                        <div class="modal-body">
                                <div class="row" style="margin-top: -25px;">
                                        <div class="col-md-12">
                                                <div>
                                                        <p><em class="icon ni ni-bulb"></em> Filter job opportunities based on the budget range</p>
                                                </div>
                                                <div class="row">
                                                        <div class="col-md-6">
                                                                <div class="input-group1 mb-3 mt-3">
                                                                        <label for="minBudgetInput">Min budget (GHS)</label>
                                                                        <input type="number" min="0" value="{{ request('min_budget', 0) }}" class="form-control"
                                                                                placeholder="Min budget" name="min_budget" id="minBudgetInput">
                                                                </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                                <div class="input-group1 mb-3 mt-3">
                                                                        <label for="maxBudgetInput">Max budget (GHS)</label>
                                                                        <input type="number" min="0" value="{{ request('max_budget', 5000) }}" class="form-control"
                                                                                placeholder="Max budget" name="max_budget" id="maxBudgetInput">
                                                                </div>
                                                        </div>
                                                </div>
                                                <div style="display: flex; justify-content: space-between; width: 100%;">
                                                        @if(request('min_budget') || request('max_budget'))
                                                        <button type="button" class="btn btn-outline-secondary" onclick="clearBudgetFilter()">Clear filter</button>
                                                        @else
                                                        <div></div>
                                                        @endif
                                                        <button type="button" class="btn btn-primary" onclick="applyBudgetFilter()">Apply filter</button>
                                                </div>

                                                <script>
                                                        function applyBudgetFilter() {
                                                                var minBudget = document.getElementById('minBudgetInput').value;
                                                                var maxBudget = document.getElementById('maxBudgetInput').value;
                                                                var currentUrl = new URL(window.location.href);
                                                                var budgetRangeText = '';

                                                                // Validate inputs if both are provided
                                                                if (minBudget !== '' && maxBudget !== '' && parseInt(minBudget) > parseInt(maxBudget)) {
                                                                        alert('Minimum budget cannot be greater than maximum budget');
                                                                        return;
                                                                }

                                                                // Set budget parameters
                                                                if (minBudget !== '') {
                                                                        currentUrl.searchParams.set('min_budget', minBudget);
                                                                } else {
                                                                        currentUrl.searchParams.delete('min_budget');
                                                                }

                                                                if (maxBudget !== '') {
                                                                        currentUrl.searchParams.set('max_budget', maxBudget);
                                                                } else {
                                                                        currentUrl.searchParams.delete('max_budget');
                                                                }

                                                                // Update budget display text based on what's provided
                                                                if (minBudget !== '' && maxBudget !== '') {
                                                                        budgetRangeText = 'Between GHS' + minBudget + ' and GHS' + maxBudget;
                                                                } else if (minBudget !== '') {
                                                                        budgetRangeText = 'Min GHS' + minBudget + '+';
                                                                } else if (maxBudget !== '') {
                                                                        budgetRangeText = 'Up to GHS' + maxBudget;
                                                                }

                                                                if (budgetRangeText !== '') {
                                                                        document.getElementById('budgetRange').innerHTML = '<span class="text-dark"><b>' + budgetRangeText + '</b></span>';
                                                                        document.getElementById('bugBox').classList.add('borderActive');
                                                                } else {
                                                                        document.getElementById('budgetRange').innerHTML = 'Eg. Between GHS240 and GHS490';
                                                                        document.getElementById('bugBox').classList.remove('borderActive');
                                                                }

                                                                // Close modal
                                                                $('#budgetModal').modal('hide');

                                                                // Navigate to filtered URL
                                                                window.location.href = currentUrl.toString();
                                                        }

                                                        // Clear budget filter function
                                                        function clearBudgetFilter() {
                                                                var currentUrl = new URL(window.location.href);
                                                                currentUrl.searchParams.delete('min_budget');
                                                                currentUrl.searchParams.delete('max_budget');

                                                                // Reset budget display text
                                                                document.getElementById('budgetRange').innerHTML = 'Eg. Between GHS240 and GHS490';

                                                                // Remove active border from budget box
                                                                document.getElementById('bugBox').classList.remove('borderActive');

                                                                // Close modal
                                                                $('#budgetModal').modal('hide');

                                                                // Navigate to filtered URL
                                                                window.location.href = currentUrl.toString();
                                                        }

                                                        // Initialize budget display if values exist in URL
                                                        document.addEventListener('DOMContentLoaded', function() {
                                                                var urlParams = new URLSearchParams(window.location.search);
                                                                var hasMinBudget = urlParams.has('min_budget') && urlParams.get('min_budget') !== '';
                                                                var hasMaxBudget = urlParams.has('max_budget') && urlParams.get('max_budget') !== '';
                                                                var budgetRangeText = '';

                                                                if (hasMinBudget && hasMaxBudget) {
                                                                        var minBudget = urlParams.get('min_budget');
                                                                        var maxBudget = urlParams.get('max_budget');
                                                                        budgetRangeText = 'Between GHS' + minBudget + ' and GHS' + maxBudget;
                                                                } else if (hasMinBudget) {
                                                                        var minBudget = urlParams.get('min_budget');
                                                                        budgetRangeText = 'Min GHS' + minBudget + '+';
                                                                } else if (hasMaxBudget) {
                                                                        var maxBudget = urlParams.get('max_budget');
                                                                        budgetRangeText = 'Up to GHS' + maxBudget;
                                                                }

                                                                if (budgetRangeText !== '') {
                                                                        document.getElementById('budgetRange').innerHTML = '<span class="text-dark"><b>' + budgetRangeText + '</b></span>';
                                                                        document.getElementById('bugBox').classList.add('borderActive');
                                                                }
                                                        });
                                                </script>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>