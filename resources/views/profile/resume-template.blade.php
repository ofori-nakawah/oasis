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

    

     
    <div style="font-family: Rockwell !important;, sans-serif;  max-width: 800px; margin: 0 auto;padding:10px; border-radius: 4px;" >
        <div style="display: flex; flex-direction: row; justify-content: space-between;">
            <span style="width: 500px">
            </span>

        </div>

        <table style="width: 100%;border-bottom-width: 0px;">
            <tr  style="width: 100%">
                <td style="vertical-align:top !important;">
                    <div style="font-size: 32px;margin-bottom: 0;margin-top: 0;word-spacing: 2px; line-height: 2;font-family: Rockwell;font-weight: 800;">{!!$name!!}</div>
                </td>
                <td>
                    <span style="float: right;font-family: Rockwell !important;" >
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
            <div class="ul">
                @foreach($competencies as $competency)
                    <li style="font-family: Rockwell !important;"> {{$competency}} </li>
                @endforeach
            </div>
        </div>

        @if ($outsideVorkJobs->count() > 0)
            <hr style="height: 3px;background: #000">

            <div>
                <h4  style="font-family: Rockwell">Job Experience</h4>
                <table style="width: 100%;border-bottom-width: 0px;margint-top: -20px;">
                    @foreach($outsideVorkJobs->sortByDesc("start_date") as $outsideVorkJob)
                        <tr  style="width: 100%;">
                            <td style="width: 35%;vertical-align:top !important;">
                                <div style="margin-top: 20px;margin-bottom: 20px;">
                                    <div> [{{strtoupper(date("F Y", strtotime($outsideVorkJob->start_date)))}}
                                        - {{strtoupper($outsideVorkJob->end_date ? date("F Y", strtotime($outsideVorkJob->end_date)) : "Ongoing")}}]</div>
                                    <div style="font-weight: 800;font-family: Rockwell">{{$outsideVorkJob->employer}}</div>
                                </div>
                            </td>
                            <td>
                                <div style="margin-top: 20px;margin-bottom: 20px;">
                                    <div style="font-weight: bold;font-size: 18px;margin-bottom: 15px;font-family: Rockwell">
                                        {{$outsideVorkJob->role}}</div>

                                    <div style="font-weight: 800;font-family: Rockwell">Responsibilities</div>
                                    <p style="font-family: Rockwell !important;">{!! $outsideVorkJob->responsibilities !!}</p>

                                    <div style="font-weight: 800;font-family: Rockwell">Achievements</div>
                                    <p style="font-family: Rockwell !important;"> {!! $outsideVorkJob->achievements !!}</p>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

        <hr style="height: 3px;background: #000">

        <div>
            <h4  style="font-family: Rockwell">Education</h4>
            <table style="width: 100%;border-bottom-width: 0px;">
                @foreach($educationHistories->sortByDesc("start_date") as $educationHistory)
                    <tr style="width: 100%;">
                        <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                            <div style="font-family: Rockwell"> [{{strtoupper(date("F Y", strtotime($educationHistory->start_date)))}}
                                - {{strtoupper($educationHistory->end_date ? date("F Y", strtotime($educationHistory->end_date)) : "Ongoing")}}]</div>
                        </td>
                        <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                            <div style="font-family: Rockwell">{{$educationHistory->programme}}</div>
                        </td>
                        <td>
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
                <h4  style="font-family: Rockwell; ">Certification & Training</h4>
                <table style="width: 100%;border-bottom-width: 0px;">
                    @foreach($certificationsAndTrainings->sortByDesc("start_date") as $certificationsAndTraining)
                        <tr style="width: 100%;">
                            <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                                <div style="font-family: Rockwell"> [{{strtoupper(date("F Y", strtotime($certificationsAndTraining->start_date)))}}
                                    - {{strtoupper($certificationsAndTraining->end_date ? date("F Y", strtotime($certificationsAndTraining->end_date)) : "Ongoing")}}]</div>
                            </td>
                            <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                                <div style="font-family: Rockwell">{{$certificationsAndTraining->programme}}</div>
                            </td>
                            <td>
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
                <h4  style="font-family: Rockwell; ">Volunteerism</h4>
                <table style="width: 100%;border-bottom-width: 0px;">
                    @foreach($volunteerHistory as $volunteer)
                        <tr style="width: 100%;">
                            <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                                <div style="font-family: Rockwell"> {{strtoupper(date("F Y", strtotime($volunteer["date"])))}}</div>
                            </td>
                            <td style="width: 55%;vertical-align:top !important;margin-bottom: 20px;">
                                <div style="font-family: Rockwell">{{$volunteer["name"]}}</div>
                            </td>
                            <td>
                                <div style="font-family: Rockwell">{{$volunteer["volunteer_hours_awarded"]}}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

       <hr style="height: 3px;background: #000">

       <div>           
        <h4  style="font-family: Rockwell">VORK Rating</h4>
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
               <h4  style="font-family: Rockwell; ">References</h4>
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