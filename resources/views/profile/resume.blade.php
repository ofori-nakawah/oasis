@extends("layouts.master")

@section("title")
Resume
@endsection

@section("content")
@php
$userId = $data['userId'];
$name = $data['name'];
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

<div class="nk-block-head nk-block-head-sm">
    <div class="nk-block-between">
        <div class="nk-block-head-content">
            <h3 class="nk-block-title page-title"><em class="icon ni ni-file-pdf"></em> CV Generator</h3>
            <div class="nk-block-des text-soft">
                <p class="hide-mb-sm hide-mb-xs md">
                <nav>
                    <ul class="breadcrumb breadcrumb-arrow">
                        <li class="breadcrumb-item"><a href="#">VORK Resume Builder</a></li>
                    </ul>
                </nav>
                </p>
            </div>
        </div>
        <div class="nk-block-head-conte nt">
            {{-- <a href="#"
                   class="btn btn-outline-primary"><span> Enhance with AI</span></a> --}}
            <a href="#" onclick="exportResume()" id="export-resume-btn"
                class="btn btn-primary"><span>Download</span></a>
            <a href="{{URL::previous()}}"
                class="btn btn-outline-primary"><span>Back</span></a>
        </div><!-- .nk-block-head-content -->
    </div>
</div>


<style>
    @media (max-width: 768px) {
        #resume-content {
            transform: scale(0.6);
            transform-origin: top left;
            width: 166.67%;
            max-width: 166.67%;
            overflow-x: hidden;
        }
        
        body {
            overflow-x: hidden;
        }
    }
    
    @media print {
        #resume-content {
            transform: none !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }
    
    @media (max-width: 576px) {
        .volunteer-name {
            width: 45% !important;
            padding-right: 5px !important;
            padding-left: 25px !important;
        }
    }
</style>

<div style="font-family: Rockwell !important; max-width: 800px; margin: 0 auto; padding: 20px 20px 40px 20px; border-radius: 4px;" id="resume-content">
    <div style="display: flex; flex-direction: row; justify-content: space-between; margin-bottom: 20px;">
        <span style="width: 500px">
        </span>
        <div>
            <a href="{{ route('user.profile.downloadResume', ['id' => $userId]) }}" class="btn btn-primary">
                <i class="fas fa-download"></i> Download PDF
            </a>
        </div>
    </div>

    <table style="width: 100%;border-bottom-width: 0px;">
        <tr style="width: 100%">
            <td style="vertical-align:top !important;">
                <div style="font-size: 32px;margin-bottom: 0;margin-top: 0;word-spacing: 2px; line-height: 2;font-family: Rockwell;font-weight: 800;">{{$name}}</div>
            </td>
            <td>
                <span style="float: right;font-family: Rockwell !important;">
                    <div style="font-family: Rockwell !important;">Address: {{$location}}</div>
                    <div style="font-family: Rockwell !important;">Tel: {{$phoneNumber}}</div>
                    <div style="font-family: Rockwell !important;">Email: {{$email}}</div>
                </span>
            </td>
        </tr>
    </table>

    <div style="margin-top: 40px;margin-bottom: 40px">
        <div style="font-family: Rockwell;">An accomplished professional diversified experience in defining and delivering successful projects in
            the Software. I am highly analytical, versatile and a self-starter with proven ability to manage
            multiple projects while collaborating and supporting cross-functional teams
        </div>
    </div>

    <hr style="height: 3px;background: #000">

    <div>
        <h4 style="font-family: Rockwell">Core Competencies</h4>
        <div class="d-flex flex-row justify-between gap-8">
            @foreach($competencies as $competency)
            <span style="font-family: Rockwell !important;"> &#183; {{$competency}} </span>
            @endforeach
        </div>
    </div>

    @if ($outsideVorkJobs->count() > 0)
    <hr style="height: 3px;background: #000">

    <div>
        <h4 style="font-family: Rockwell">Job Experience</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @foreach($outsideVorkJobs->sortByDesc("start_date") as $outsideVorkJob)
            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;">
                    <div style="margin-top: 20px;margin-bottom: 20px;">
                        <div> [{{strtoupper(date("M Y", strtotime($outsideVorkJob->start_date)))}}
                            - {{strtoupper($outsideVorkJob->end_date ? date("M Y", strtotime($outsideVorkJob->end_date)) : "Ongoing")}}]</div>
                        <div style="font-weight: 800;font-family: Rockwell">{{$outsideVorkJob->employer}}</div>
                    </div>
                </td>
                <td>
                    <div style="margin-top: 20px;margin-bottom: 20px;">
                        <div style="font-weight: bold;font-size: 18px;margin-bottom: 15px;font-family: Rockwell;color: #353299">
                            {{$outsideVorkJob->role}}
                        </div>

                        <div style="font-weight: 800;font-family: Rockwell; text-decoration: underline;">Responsibilities</div>
                        <div style="font-family: Rockwell !important;">{!! $outsideVorkJob->responsibilities !!}</div>

                        <div style="font-weight: 800;font-family: Rockwell; text-decoration: underline;margin-top: 5px;">Achievements</div>
                        <div style="font-family: Rockwell !important;"> {!! $outsideVorkJob->achievements !!}</div>
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <hr style="height: 3px;background: #000">

    <div>
        <h4 style="font-family: Rockwell">Education</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @foreach($educationHistories->sortByDesc('start_date') as $educationHistory)
            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell"> [{{strtoupper(date("M Y", strtotime($educationHistory->start_date)))}}
                        - {{strtoupper($educationHistory->end_date ? date("M Y", strtotime($educationHistory->end_date)) : "Ongoing")}}]</div>
                </td>
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell">{{$educationHistory->programme}}</div>
                </td>
                <td style="vertical-align:top !important;">
                    <div style="font-family: Rockwell">{{$educationHistory->institution}}
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    @if ($certificationsAndTrainings->count() > 0)
    <hr style="height: 3px;background: #000">

    <div>
        <h4 style="font-family: Rockwell; ">Certification & Training</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @foreach($certificationsAndTrainings->sortByDesc('start_date') as $certificationsAndTraining)
            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell"> [{{strtoupper(date("M Y", strtotime($certificationsAndTraining->start_date)))}}
                        - {{strtoupper($certificationsAndTraining->end_date ? date("M Y", strtotime($certificationsAndTraining->end_date)) : "Ongoing")}}]</div>
                </td>
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell">{{$certificationsAndTraining->programme}}</div>
                </td>
                <td style="vertical-align:top !important;">
                    <div style="font-family: Rockwell">{{$certificationsAndTraining->institution}}
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif


    @if (count($volunteerHistory) > 0)
    <hr style="height: 3px;background: #000">

    <div>
        <h4 style="font-family: Rockwell;">Volunteerism</h4>
        <table style="width: 100%;border-bottom-width: 0px; table-layout: fixed;">
            @php
                $sortedVolunteerHistory = collect($volunteerHistory)->sortByDesc(function($item) {
                    return strtotime($item['date']);
                });
            @endphp
            @foreach($sortedVolunteerHistory as $volunteer)
            <tr style="width: 100%;">
                <td style="width: 28%; vertical-align: top !important; margin-bottom: 10px; padding-right: 5px;">
                    <div style="font-family: Rockwell; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{strtoupper(date("M Y", strtotime($volunteer["date"])))}}</div>
                </td>
                <td style="width: 55%; vertical-align: top !important; padding-right: 10px; padding-left: 65px;" class="volunteer-name">
                    <div style="font-family: Rockwell; word-wrap: break-word;">{{$volunteer["name"]}}</div>
                </td>
                <td style="width: 20%; vertical-align: top !important; padding-left: 10px; white-space: nowrap;">
                    <div style="font-family: Rockwell; text-align: right;">{{$volunteer["volunteer_hours_awarded"]}} hours</div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <hr style="height: 3px;background: #000">

    <div>
        <h4 style="font-family: Rockwell">VORK Rating</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell"> Expertise: <em class="ni ni-star" style="color: yellow"></em> {{ $ratings["expertise"] }}</div>
                </td>
                <td>
                    <div style="font-family: Rockwell">Work Ethic: <em class="ni ni-star" style="color: yellow"></em> {{ $ratings["work_ethic"] }}</div>
                </td>
            </tr>
            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell"> Professionalism: <em class="ni ni-star" style="color: yellow"></em> {{ $ratings["professionalism"] }}</div>
                </td>
                <td>
                    <div style="font-family: Rockwell">Customer Service: <em class="ni ni-star" style="color: yellow"></em> {{ $ratings["customer_service"] }}</div>
                </td>
            </tr>
        </table>
    </div>


    @if (count($references) > 0)
    <hr style="height: 3px;background: #000">

    <div>
        <h4 style="font-family: Rockwell; ">References</h4>
        <table style="width: 100%;border-bottom-width: 0px;">
            @foreach($references as $reference)
            <tr style="width: 100%;">
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell">{{$reference['name']}}</div>
                </td>
                <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                    <div style="font-family: Rockwell">{{$reference['company']}}</div>
                </td>
                <td>
                    <div style="font-family: Rockwell">{{$reference['email']}}
                    </div>
                </td>
                <td>
                    <div style="font-family: Rockwell">{{$reference['phone_number']}}
                    </div>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
    @endif
</div>

<div id="export-loading" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <span>Generating PDF...</span>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
    async function exportResume() {
        const exportBtn = document.getElementById('export-resume-btn');
        const loadingIndicator = document.getElementById('export-loading');

        // Show loading state
        exportBtn.disabled = true;
        loadingIndicator.style.display = 'flex';

        try {
            const element = document.getElementById('resume-content');
            if (!element) {
                throw new Error('Resume content not found');
            }

            // Create PDF instance
            const {
                jsPDF
            } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');

            // A4 dimensions
            const pageWidth = 210;
            const pageHeight = 290;

            // Capture the element
            const canvas = await html2canvas(element, {
                scale: 2,
                useCORS: true,
                logging: false,
                allowTaint: true
            });

            // Calculate dimensions
            const imgWidth = pageWidth;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            // Define margins (in mm)
            const marginTop = 20;
            const marginBottom = 20;
            const effectivePageHeight = pageHeight - marginTop - marginBottom;
            
            // Add image to PDF (potentially across multiple pages)
            let heightLeft = imgHeight;
            let position = 0;
            let pageNumber = 1;

            // First page
            pdf.addImage(
                canvas.toDataURL('image/jpeg', 1.0),
                'JPEG',
                0,
                marginTop, // Apply top margin
                imgWidth,
                imgHeight
            );
            heightLeft -= effectivePageHeight; // Subtract effective page height

            // Add additional pages if needed
            while (heightLeft > 0) {
                position = -(pageHeight * pageNumber) + marginTop;
                pdf.addPage();
                pdf.addImage(
                    canvas.toDataURL('image/jpeg', 1.0),
                    'JPEG',
                    0,
                    position,
                    imgWidth,
                    imgHeight
                );
                heightLeft -= effectivePageHeight;
                pageNumber++;
            }

            // Save PDF
            // Temporarily remove the scaling for PDF export
            const resumeContent = document.getElementById('resume-content');
            const originalStyle = resumeContent.getAttribute('style');
            resumeContent.style.transform = 'none';
            resumeContent.style.width = '100%';
            resumeContent.style.maxWidth = '100%';
            
            // Force a reflow
            void resumeContent.offsetHeight;
            
            // Save the PDF
            pdf.save('resume.pdf');
            
            // Restore the original styles
            resumeContent.setAttribute('style', originalStyle);
        } catch (error) {
            console.error('PDF export failed:', error);
            alert('Failed to export PDF: ' + error.message);
        } finally {
            // Reset UI state
            exportBtn.disabled = false;
            loadingIndicator.style.display = 'none';
        }
    }
</script>
@endsection