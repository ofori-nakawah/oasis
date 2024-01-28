@extends("layouts.onboarding")

@section("title")
    Resume
@endsection

@section("content")
    <div style="font-family: Rockwell !important;, sans-serif">
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

        <hr style="height: 3px;background: #000">

        <div>
            <h4  style="font-family: Rockwell">Job Experience</h4>
            <table style="width: 100%;border-bottom-width: 0px;">
                <tr  style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;">
                       <div style="margin-top: 20px;margin-bottom: 20px;">
                           <div> [JULY 2016 – DECEMBER 2017]</div>
                           <div style="font-weight: 800;font-family: Rockwell">Company B</div>
                       </div>
                    </td>
                    <td>
                       <div style="margin-top: 20px;margin-bottom: 20px;">
                           <div style="font-weight: bold;font-size: 18px;margin-bottom: 15px;font-family: Rockwell"> Customer Service</div>

                           <div style="font-weight: 800;font-family: Rockwell">Responsibilities</div>
                           <p style="font-family: Rockwell !important;">Scheduling and managing a team of developers
                               Responsible for leading a stabilization exercise t</p>

                           <div style="font-weight: 800;font-family: Rockwell">Achievements</div>
                           <p style="font-family: Rockwell !important;">Coordinated all operations, drew and managed project schedules and successfully completed the delivery of subsea equipment worth $850MM to clients.
                           </p>
                       </div>
                    </td>
                </tr>
                <tr  style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="margin-top: 20px;margin-bottom: 20px;">
                            <div> [JULY 2016 – DECEMBER 2017]</div>
                            <div style="font-weight: 800;font-family: Rockwell">Company B</div>
                        </div>
                    </td>
                    <td style="margin-bottom: 20px;">
                        <div style="margin-top: 20px;margin-bottom: 20px;">
                            <div style="font-weight: bold;font-size: 18px;margin-bottom: 15px;font-family: Rockwell"> Customer Service</div>

                            <div style="font-weight: 800;font-family: Rockwell">Responsibilities</div>
                            <p style="font-family: Rockwell !important;">Scheduling and managing a team of developers
                                Responsible for leading a stabilization exercise t</p>

                            <div style="font-weight: 800;font-family: Rockwell">Achievements</div>
                            <p style="font-family: Rockwell !important;">Coordinated all operations, drew and managed project schedules and successfully completed the delivery of subsea equipment worth $850MM to clients.
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <hr style="height: 3px;background: #000">

        <div>
            <h4  style="font-family: Rockwell">Education</h4>
            <table style="width: 100%;border-bottom-width: 0px;">
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> [JULY 2016 – DECEMBER 2017]</div>
                    </td>
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell">Sociology, BSc</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Kwame Nkrumah University
                        </div>
                    </td>
                </tr>
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> [JULY 2016 – DECEMBER 2017]</div>
                    </td>
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell">Sociology, BSc</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Kwame Nkrumah University
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <hr style="height: 3px;background: #000">

        <div>
            <h4  style="font-family: Rockwell">Certification & Training</h4>
            <table style="width: 100%;border-bottom-width: 0px;">
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> [JULY 2016 – DECEMBER 2017]</div>
                    </td>
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell">Sociology, BSc</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Kwame Nkrumah University
                        </div>
                    </td>
                </tr>
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> [JULY 2016 – DECEMBER 2017]</div>
                    </td>
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell">Sociology, BSc</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Kwame Nkrumah University
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <hr style="height: 3px;background: #000">

        <div>
            <h4  style="font-family: Rockwell">Volunteerism</h4>
            <table style="width: 100%;border-bottom-width: 0px;">
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> [JULY 2016 – DECEMBER 2017]</div>
                    </td>
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell">Sociology, BSc</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Kwame Nkrumah University
                        </div>
                    </td>
                </tr>
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> [JULY 2016 – DECEMBER 2017]</div>
                    </td>
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell">Sociology, BSc</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Kwame Nkrumah University
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <hr style="height: 3px;background: #000">

        <div>
            <h4  style="font-family: Rockwell">VORK Rating</h4>
            <table style="width: 100%;border-bottom-width: 0px;">
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> Expertise: </div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Work Ethic:
                        </div>
                    </td>
                </tr>
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> Professionalism:</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Customer Service:
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <hr style="height: 3px;background: #000">

        <div>
            <h4  style="font-family: Rockwell">References</h4>
            <table style="width: 100%;border-bottom-width: 0px;">
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> [JULY 2016 – DECEMBER 2017]</div>
                    </td>
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell">Sociology, BSc</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Kwame Nkrumah University
                        </div>
                    </td>
                </tr>
                <tr style="width: 100%;">
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell"> [JULY 2016 – DECEMBER 2017]</div>
                    </td>
                    <td style="width: 35%;vertical-align:top !important;margin-bottom: 20px;">
                        <div style="font-family: Rockwell">Sociology, BSc</div>
                    </td>
                    <td>
                        <div style="font-family: Rockwell">Kwame Nkrumah University
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
@endsection
