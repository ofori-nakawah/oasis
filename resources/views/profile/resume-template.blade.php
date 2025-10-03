@php
$name = $data['name'];
$nameParts = explode(' ', $name, 2);
$nameString = $data['nameString'];
$bio = $data['bio'];
$email = $data['email'];
$phoneNumber = $data['phoneNumber'];
$location = $data['location'];
$competencies = $data['competencies'];
$outsideVorkJobs = $data['outsideVorkJobs'];
$educationHistories = $data['educationHistories'];
$certificationsAndTrainings = $data['certificationsAndTrainings'];
$references = $data['references'];
$volunteerHistory = $data['volunteerHistory'];
$ratings = $data['ratings'];
@endphp

<style>
    @font-face {
        font-family: 'Rockwell';
        src: url({{ public_path('rockwell.ttf') }}) format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    body { color: #656565; font-family: 'Rockwell', serif; font-weight: normal; }
    ul, ol { margin: 0 0 0 15px; padding: 0; }
    li { margin: 0; padding: 0; line-height: 1.3; font-size: 12px; }
    p { margin: 0; padding: 0; font-size: 12px; }
    table { border-collapse: collapse; }
    tr { page-break-inside: avoid; }
    td { padding: 0; font-size: 12px; }
    .content-text { font-size: 12px; }
</style>

<div style="font-family: Rockwell, sans-serif; max-width: 800px; margin: 0 auto; padding: 10px; border-radius: 4px;">
    <div style="display: flex; flex-direction: row; justify-content: space-between;">
        <span style="width: 500px"></span>
    </div>

    <table style="width: 100%; border-bottom-width: 0px;">
        <tr style="width: 100%; ">
            <td style="vertical-align: top !important;">
                <div style="font-size: 32px; margin-bottom: 0; margin-top: -10px; word-spacing: 2px; font-family: Rockwell; font-weight: 800;color: #000;">{{ $nameParts[0] }}<br>{{ $nameParts[1] ?? '' }}</div>
            </td>
            <td>
                <span style="float: right; font-family: Rockwell !important; font-weight: normal;">
                    <div style="font-family: Rockwell !important; font-weight: normal; font-size: 12px;">Address: {{ $location }}</div>
                    <div style="font-family: Rockwell !important; font-weight: normal; font-size: 12px;">Tel: {{ $phoneNumber }}</div>
                    <div style="font-family: Rockwell !important; font-weight: normal; font-size: 12px;">Email: {{ $email }}</div>
                </span>
            </td>
        </tr>
    </table>

    <div style="margin-top: 40px; margin-bottom: 40px">
        <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $bio }}</div>
    </div>

    <hr style="height: 0.5px; background: #000">

    <div style="margin-bottom: 10px;">
        <div style="font-family: Rockwell; font-size: 18px;margin-bottom: 10px;font-weight: 800; color: #262626;">Core Competencies</div>
        <div style="font-family: Rockwell !important; font-weight: normal; line-height: 1.5; font-size: 12px;">
            @foreach($competencies as $index => $competency)
                {{ $competency }}@if($index < count($competencies) - 1) • @endif
            @endforeach
        </div>
    </div>

    @if ($outsideVorkJobs->count() > 0)
    <hr style="height:0.51px; background: #000">

    <div>
        <div style="font-family: Rockwell; font-size: 18px;margin-bottom: 20px;font-weight: 800; color: #262626;">Job Experience</div>
        <table style="width: 100%; border-collapse: collapse; border-spacing: 0; border-bottom-width: 0px; margin-top: -20px;">
            @foreach($outsideVorkJobs->sortByDesc("start_date") as $outsideVorkJob)
            <tr style="width: 100%;">
                <td style="width: 35%; vertical-align: top !important; padding: 0;">
                    <div style="margin-top: 10px; margin-bottom: 5px;">
                        <div style="font-size: 12px;">[{{ strtoupper(date("M Y", strtotime($outsideVorkJob->start_date))) }} - {{ strtoupper($outsideVorkJob->end_date ? date("M Y", strtotime($outsideVorkJob->end_date)) : "Ongoing") }}]</div>
                        <div style="font-weight: 800; font-family: Rockwell; font-size: 12px;color: #000;">{{ $outsideVorkJob->employer }}</div>
                    </div>
                </td>
                <td style="padding: 0;">
                    <div style="margin-top: 10px; margin-bottom: 5px;">
                        <div style="font-weight: bold; font-size: 18px; margin-bottom: 8px; font-family: Rockwell; color: #353299">{{ $outsideVorkJob->role }}</div>

                        <div style="font-weight: 800; font-family: Rockwell; margin-bottom: 3px;text-decoration: underline; font-size: 12px;color: #000;">Responsibilities</div>
                        <div style="font-family: Rockwell !important; font-weight: normal; margin: 0 0 5px 0; line-height: 1.2; font-size: 12px;">{!! $outsideVorkJob->responsibilities !!}</div>

                        <div style="font-weight: 800; font-family: Rockwell; margin-bottom: 3px;text-decoration: underline; font-size: 12px;color: #000;">Achievements</div>
                        <div style="font-family: Rockwell !important; font-weight: normal; margin: 0; line-height: 1.2; font-size: 12px;">{!! $outsideVorkJob->achievements !!}</div>
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <hr style="height: 0.5px; background: #000">

    <div>
        <div style="font-family: Rockwell; font-size: 18px;margin-bottom: 10px;font-weight: 800; color: #262626;">Education</div>
        <table style="width: 100%; border-bottom-width: 0px;">
            @foreach($educationHistories->sortByDesc("start_date") as $educationHistory)
            <tr style="width: 100%;">
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">[{{ strtoupper(date("M Y", strtotime($educationHistory->start_date))) }} - {{ strtoupper($educationHistory->end_date ? date("M Y", strtotime($educationHistory->end_date)) : "Ongoing") }}]</div>
                </td>
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $educationHistory->programme }}</div>
                </td>
                <td style="vertical-align: top !important;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $educationHistory->institution }}</div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    @if ($certificationsAndTrainings->count() > 0)
    <hr style="height: 0.5px; background: #000">

    <div>
        <div style="font-family: Rockwell; font-size: 18px;margin-bottom: 10px;font-weight: 800; color: #262626;">Certification & Training</div>
        <table style="width: 100%; border-bottom-width: 0px;">
            @foreach($certificationsAndTrainings->sortByDesc("start_date") as $certificationsAndTraining)
            <tr style="width: 100%;">
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">[{{ strtoupper(date("M Y", strtotime($certificationsAndTraining->start_date))) }} - {{ strtoupper($certificationsAndTraining->end_date ? date("M Y", strtotime($certificationsAndTraining->end_date)) : "Ongoing") }}]</div>
                </td>
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $certificationsAndTraining->programme }}</div>
                </td>
                <td style="vertical-align: top !important;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $certificationsAndTraining->institution }}</div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if (count($volunteerHistory) > 0)
    <hr style="height: 0.5px; background: #000">

    <div>
        <div style="font-family: Rockwell; font-size: 18px;margin-bottom: 10px;font-weight: 800; color: #262626;">Volunteerism</div>
        <table style="width: 100%; border-bottom-width: 0px;">
            @foreach($volunteerHistory as $volunteer)
            <tr style="width: 100%;">
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ strtoupper(date("M Y", strtotime($volunteer["date"]))) }}</div>
                </td>
                <td style="width: 55%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $volunteer["name"] }}</div>
                </td>
                <td style="vertical-align: top !important;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $volunteer["volunteer_hours_awarded"] }} hours</div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <hr style="height: 0.5px; background: #000">

    <div>
        <div style="font-family: Rockwell; font-size: 18px;margin-bottom: 10px;font-weight: 800; color: #262626;">VORK Rating</div>
        <table style="width: 100%; border-bottom-width: 0px;">
            <tr style="width: 100%;">
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">Expertise: ⭐ {{ $ratings["expertise"] }}</div>
                </td>
                <td>
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">Work Ethic: ⭐ {{ $ratings["work_ethic"] }}</div>
                </td>
            </tr>
            <tr style="width: 100%;">
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">Professionalism: ⭐ {{ $ratings["professionalism"] }}</div>
                </td>
                <td>
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">Customer Service: ⭐ {{ $ratings["customer_service"] }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if (count($references) > 0)
    <hr style="height: 0.5px; background: #000">

    <div>
        <div style="font-family: Rockwell; font-size: 18px;font-weight: 800;margin-bottom: 10px; color: #262626;">References</div>
        <table style="width: 100%; border-bottom-width: 0px;">
            @foreach($references as $reference)
            <tr style="width: 100%;">
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $reference['name'] }}</div>
                </td>
                <td style="width: 35%; vertical-align: top !important; margin-bottom: 20px;">
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $reference['company'] }}</div>
                </td>
                <td>
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $reference['email'] }}</div>
                </td>
                <td>
                    <div style="font-family: Rockwell; font-weight: normal; font-size: 12px;">{{ $reference['phone_number'] }}</div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
</div>

