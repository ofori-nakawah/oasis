<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Resume - {{ $user->name ?? $name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 5px 0;
        }

        h4 {
            margin: 15px 0 10px 0;
            font-size: 18px;
        }

        hr {
            border: none;
            height: 3px;
            background: #000;
            margin: 20px 0;
        }

        .d-flex {
            display: flex;
        }

        .flex-row {
            flex-direction: row;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-8 {
            gap: 8px;
        }

        .flex-wrap {
            flex-wrap: wrap;
        }
    </style>
</head>

<body style="font-family: Rockwell !important; max-width: 800px; margin: 0 auto; padding: 20px 20px 40px 20px;">
    <table style="width: 100%;border-bottom-width: 0px;margin-bottom: 20px;">
        <tr style="width: 100%">
            <td style="vertical-align:top !important;">
                <div style="font-size: 32px;margin-bottom: 0;margin-top: 0;word-spacing: 2px; line-height: 2;font-family: Rockwell;font-weight: 800;">{!! $user->name ?? $name !!}</div>
            </td>
            <td>
                <span style="float: right;font-family: Rockwell !important;">
                    @if(isset($user->location_name) || isset($location))
                    <div style="font-family: Rockwell !important;">Address: {{ $user->location_name ?? $location ?? 'N/A' }}</div>
                    @endif
                    @if(isset($user->phone) || isset($phoneNumber))
                    <div style="font-family: Rockwell !important;">Tel: {{ $user->phone ?? $phoneNumber ?? 'N/A' }}</div>
                    @endif
                    @if(isset($user->email) || isset($email))
                    <div style="font-family: Rockwell !important;">Email: {{ $user->email ?? $email ?? 'N/A' }}</div>
                    @endif
                </span>
            </td>
        </tr>
    </table>

    <div style="margin-top: 80px;margin-bottom: 40px">
        <div style="font-family: Rockwell;">An accomplished professional with diversified experience in defining and delivering successful projects in
            the Software. I am highly analytical, versatile and a self-starter with proven ability to manage
            multiple projects while collaborating and supporting cross-functional teams
        </div>
    </div>

    <hr>

    <div>
        <h4 style="font-family: Rockwell">Core Competencies</h4>
        <div class="d-flex flex-row justify-between gap-8 flex-wrap">
            @if (!empty($user->competencies) && (is_array($user->competencies) || $user->competencies instanceof Countable))
            @foreach($user->competencies as $competency)
            @if(is_array($competency) && isset($competency['name']))
            <span style="font-family: Rockwell !important;"> &#183; {{ $competency['name'] }} </span>
            @elseif(is_object($competency) && isset($competency->name))
            <span style="font-family: Rockwell !important;"> &#183; {{ $competency->name }} </span>
            @else
            <span style="font-family: Rockwell !important;"> &#183; {{ $competency }} </span>
            @endif
            @endforeach
            @endif
        </div>
    </div>

    @if (!empty($user->outsideVorkJobs) && (is_array($user->outsideVorkJobs) || $user->outsideVorkJobs instanceof Countable))
    <hr>
    <div>
        <h4 style="font-family: Rockwell">Job Experience</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @php
            $sortedJobs = is_array($user->outsideVorkJobs)
            ? collect($user->outsideVorkJobs)->sortByDesc(function($job) {
            return is_array($job) ? ($job['start_date'] ?? '') : ($job->start_date ?? '');
            })
            : $user->outsideVorkJobs->sortByDesc('start_date');
            @endphp

            @foreach($sortedJobs as $job)
            @php
            $job = (array) $job; // Convert object to array for consistent access
            $startDate = $job['start_date'] ?? null;
            $endDate = $job['end_date'] ?? null;
            $employer = $job['employer'] ?? 'N/A';
            $role = $job['role'] ?? 'N/A';
            $responsibilities = $job['responsibilities'] ?? null;
            $achievements = $job['achievements'] ?? null;
            @endphp

            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;">
                    <div style="margin-top: 20px;margin-bottom: 20px;">
                        @if($startDate)
                        <div> [{{ strtoupper(date("M Y", strtotime($startDate))) }}
                            - {{ $endDate ? strtoupper(date("M Y", strtotime($endDate))) : "Ongoing" }}]</div>
                        @endif
                        <div style="font-weight: 800;font-family: Rockwell">{{ $employer }}</div>
                    </div>
                </td>
                <td>
                    <div style="margin-top: 20px;margin-bottom: 20px;">
                        <div style="font-weight: bold;font-size: 18px;margin-bottom: 15px;font-family: Rockwell;color: #353299">
                            {{ $role }}
                        </div>

                        @if($responsibilities)
                        <div style="font-weight: 800;font-family: Rockwell; text-decoration: underline;">Responsibilities</div>
                        <div style="font-family: Rockwell !important;">{!! $responsibilities !!}</div>
                        @endif

                        @if($achievements)
                        <div style="font-weight: 800;font-family: Rockwell; text-decoration: underline;margin-top: 5px;">Achievements</div>
                        <div style="font-family: Rockwell !important;">{!! $achievements !!}</div>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if (!empty($user->educationHistories) && (is_array($user->educationHistories) || $user->educationHistories instanceof Countable))
    <hr>
    <div>
        <h4 style="font-family: Rockwell">Education</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @php
            $sortedEducation = is_array($user->educationHistories)
            ? collect($user->educationHistories)->sortByDesc(function($edu) {
            return is_array($edu) ? ($edu['start_date'] ?? '') : ($edu->start_date ?? '');
            })
            : $user->educationHistories->sortByDesc('start_date');
            @endphp

            @foreach($sortedEducation as $edu)
            @php
            $edu = (array) $edu; // Convert object to array for consistent access
            $startDate = $edu['start_date'] ?? null;
            $endDate = $edu['end_date'] ?? null;
            $programme = $edu['programme'] ?? 'N/A';
            $institution = $edu['institution'] ?? 'N/A';
            @endphp

            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    @if($startDate)
                    <div style="font-family: Rockwell"> [{{ strtoupper(date("M Y", strtotime($startDate))) }}
                        - {{ $endDate ? strtoupper(date("M Y", strtotime($endDate))) : "Ongoing" }}]</div>
                    @endif
                </td>
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell">{{ $programme }}</div>
                </td>
                <td style="vertical-align:top !important;">
                    <div style="font-family: Rockwell">{{ $institution }}</div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if (!empty($user->certificationsAndTrainings) && (is_array($user->certificationsAndTrainings) || $user->certificationsAndTrainings instanceof Countable))
    <hr>
    <div>
        <h4 style="font-family: Rockwell">Certification & Training</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @php
            $sortedCerts = is_array($user->certificationsAndTrainings)
            ? collect($user->certificationsAndTrainings)->sortByDesc(function($cert) {
            return is_array($cert) ? ($cert['start_date'] ?? '') : ($cert->start_date ?? '');
            })
            : $user->certificationsAndTrainings->sortByDesc('start_date');
            @endphp

            @foreach($sortedCerts as $cert)
            @php
            $cert = (array) $cert; // Convert object to array for consistent access
            $startDate = $cert['start_date'] ?? null;
            $endDate = $cert['end_date'] ?? null;
            $programme = $cert['programme'] ?? 'N/A';
            $institution = $cert['institution'] ?? 'N/A';
            @endphp

            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    @if($startDate)
                    <div style="font-family: Rockwell"> [{{ strtoupper(date("M Y", strtotime($startDate))) }}
                        - {{ $endDate ? strtoupper(date("M Y", strtotime($endDate))) : "Ongoing" }}]</div>
                    @endif
                </td>
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell">{{ $programme }}</div>
                </td>
                <td style="vertical-align:top !important;">
                    <div style="font-family: Rockwell">{{ $institution }}</div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if (!empty($user->volunteerHistory) && (is_array($user->volunteerHistory) || $user->volunteerHistory instanceof Countable))
    <hr>
    <div>
        <h4 style="font-family: Rockwell">Volunteer Experience</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @php
            $sortedVolunteer = is_array($user->volunteerHistory)
            ? collect($user->volunteerHistory)->sortByDesc(function($vol) {
            return is_array($vol) ? ($vol['start_date'] ?? '') : ($vol->start_date ?? '');
            })
            : $user->volunteerHistory->sortByDesc('start_date');
            @endphp

            @foreach($sortedVolunteer as $volunteer)
            @php
            $volunteer = (array) $volunteer; // Convert object to array for consistent access
            $startDate = $volunteer['start_date'] ?? null;
            $endDate = $volunteer['end_date'] ?? $volunteer['date'] ?? null;
            $title = $volunteer['title'] ?? 'N/A';
            $organization = $volunteer['organization'] ?? $volunteer['name'] ?? 'N/A';
            $hours = $volunteer['volunteer_hours'] ?? $volunteer['volunteer_hours_awarded'] ?? null;
            @endphp

            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    @if($startDate)
                    <div style="font-family: Rockwell">[{{ strtoupper(date("M Y", strtotime($startDate))) }}
                        - {{ $endDate ? strtoupper(date("M Y", strtotime($endDate))) : "Ongoing" }}]</div>
                    @endif
                </td>
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell">{{ $title }}</div>
                </td>
                <td style="vertical-align:top !important;">
                    <div style="font-family: Rockwell">{{ $organization }}
                        @if($hours)
                        <div>({{ $hours }} hours)</div>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    @if (!empty($user->ratings) && (is_array($user->ratings) || $user->ratings instanceof Countable))
    <hr>
    <div>
        <h4 style="font-family: Rockwell">VORK Rating</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @php
            $ratingCategories = [
            'expertise' => 'Expertise',
            'work_ethic' => 'Work Ethic',
            'professionalism' => 'Professionalism',
            'customer_service' => 'Customer Service'
            ];
            @endphp

            @foreach($ratingCategories as $key => $label)
            @php
            $ratingValue = $user->ratings[$key] ?? 0;
            @endphp
            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell">{{ $label }}</div>
                </td>
                <td style="vertical-align:top !important;">
                    <div style="font-family: Rockwell">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <=$ratingValue)
                            ★
                            @else
                            ☆
                            @endif
                            @endfor
                            ({{ number_format($ratingValue, 1) }})
                            </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
</body>

</html>