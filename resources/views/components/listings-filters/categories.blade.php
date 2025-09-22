<div class="mb-2">
        <label class="form-label">Categories</label>
        <div class="card card-bordered pt-2 pl-3 pr-2" id="skillsBox"
                style="min-height: 46px;border-radius: 4px;display: flex;flex-direction: row"
                data-toggle="modal" data-target="#skillsModal">
                <div class="text-muted" style="flex: 1" id="selectedSkillsBox">
                        @if(request()->has('skills'))
                        @php
                        $selectedSkillIds = is_array(request()->skills) ? request()->skills : [request()->skills];
                        $selectedSkillNames = App\Models\Skill::whereIn('id', $selectedSkillIds)->pluck('name')->toArray();
                        @endphp
                        @foreach($selectedSkillNames as $skillName)
                        <span class="badge badge-dim badge-primary">{{ $skillName }}</span>
                        @endforeach
                        @else
                        Eg. Barber, Fashion Designer
                        @endif
                </div>
                <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>
        </div>
</div>