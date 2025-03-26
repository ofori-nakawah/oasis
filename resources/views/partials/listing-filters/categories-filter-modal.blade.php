<div class="modal fade zoom" tabindex="-1" id="skillsModal" style="border-radius: 16px;">
        <div class="modal-dialog" role="document" style="border-radius: 16px;">
                <div class="modal-content" style="border-radius: 16px;">
                        <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                                <em class="icon ni ni-cross"></em>
                        </a>
                        <div class="modal-header" style="border-bottom: none !important;">
                                <h4 class="modal-title"><b>Filter by Category</b></h4>
                        </div>
                        <div class="modal-body">
                                <div class="row">
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                        <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="skill-search" placeholder="Search skills...">
                                                        </div>
                                                </div>

                                                <div class="skill-list" style="max-height: 350px; overflow-y: auto; margin-bottom: 20px;">
                                                        @foreach($skills as $skill)
                                                        <div class="custom-control custom-checkbox skill-item mb-2">
                                                                <input type="checkbox" class="custom-control-input skill-checkbox" id="skill-{{ $skill->id }}" name="skills[]" value="{{ $skill->id }}">
                                                                <label class="custom-control-label" for="skill-{{ $skill->id }}">{{ $skill->name }}</label>
                                                        </div>
                                                        @endforeach
                                                </div>

                                                <div class="d-flex justify-content-between">
                                                        <button class="btn btn-dim btn-outline-light" onclick="clearSkills()">Clear All</button>
                                                        <button class="btn btn-primary" onclick="applySkillsFilter()">Apply Filter</button>
                                                </div>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>

<script>
        // Initialize when document is ready
        document.addEventListener('DOMContentLoaded', function() {
                // Search functionality
                document.getElementById('skill-search').addEventListener('keyup', function() {
                        var value = this.value.toLowerCase();
                        document.querySelectorAll('.skill-item').forEach(function(item) {
                                if (item.textContent.toLowerCase().indexOf(value) > -1) {
                                        item.style.display = '';
                                } else {
                                        item.style.display = 'none';
                                }
                        });
                });

                // Pre-select skills from URL parameters
                var urlParams = new URLSearchParams(window.location.search);
                var selectedSkills = urlParams.getAll('skills[]');

                selectedSkills.forEach(function(skill) {
                        var checkbox = document.getElementById('skill-' + skill);
                        if (checkbox) {
                                checkbox.checked = true;
                        }
                });
        });

        // Clear all checkboxes function
        function clearSkills() {
                // Uncheck all checkboxes
                var checkboxes = document.querySelectorAll('.skill-checkbox');
                for (var i = 0; i < checkboxes.length; i++) {
                        checkboxes[i].checked = false;
                }

                // Clear the selected skills display
                var skillsBox = document.getElementById('selectedSkillsBox');
                if (skillsBox) {
                        skillsBox.innerHTML = 'Eg. Barber, Fashion Designer';
                }

                // Clear the URL parameters and redirect
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('skills[]');
                window.location.href = currentUrl.toString();
        }

        // Apply filter function
        function applySkillsFilter() {
                var selectedSkills = [];
                var selectedSkillNames = [];
                var checkboxes = document.querySelectorAll('.skill-checkbox:checked');

                for (var i = 0; i < checkboxes.length; i++) {
                        selectedSkills.push(checkboxes[i].value);
                        selectedSkillNames.push(checkboxes[i].nextElementSibling.textContent.trim());
                }

                // Update the skills display in the filter box
                var skillsBox = document.getElementById('selectedSkillsBox');
                if (skillsBox) {
                        if (selectedSkillNames.length > 0) {
                                skillsBox.innerHTML = '';
                                for (var i = 0; i < selectedSkillNames.length; i++) {
                                        var badge = document.createElement('span');
                                        badge.className = 'badge badge-dim badge-primary mr-1';
                                        badge.textContent = selectedSkillNames[i];
                                        skillsBox.appendChild(badge);
                                }
                        } else {
                                skillsBox.innerHTML = 'Select skills to filter';
                        }
                }

                // Update the URL with selected skills
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('skills[]');

                for (var i = 0; i < selectedSkills.length; i++) {
                        currentUrl.searchParams.append('skills[]', selectedSkills[i]);
                }

                window.location.href = currentUrl.toString();
        }
</script>