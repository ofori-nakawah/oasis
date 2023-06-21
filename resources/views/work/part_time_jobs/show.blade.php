@extends("layouts.master")

@section('title')
    Work
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title " style="font-weight: 900"> Fixed term opportunities <br> <span
                        class="text-muted" style="font-size: 20px;">Discover fixed term opportunities near you and all over the country</span>
                </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    </p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{URL::previous()}}"
                   class="btn btn-outline-light"><span>Back</span></a>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

    <div class="row">
        <div class="col-md-4 .d-none .d-sm-none">
            <div id="fixedTermOpportunityShimmerLoader"></div>
            <div id="fixedTermOpportunitySnapshot"></div>

            <div class="row mb-2 mt-2" >
                <div class="col-md-12">
                    <p class="text-dark" style="display: flex;"><span style="flex: 1;">Other opportunities</span> <a href="{{route("user.work.jobs", ["type_of_user" => "seeker", "type_of_work" => "fixed-term"])}}" style="text-decoration: underline">See more</a></p>
                </div>
            </div>

            <div id="otherFixedTermOpportunitiesShimmerLoader"></div>
            <div id="otherFixedTermOpportunitiesListing"></div>

        </div>
        <div class="col-md-8">
            <div id="fixedTermOpportunityDetailsShimmerLoader"></div>
            <div id="fixedTermOpportunityDetails"></div>
        </div>

    </div>

    <div class="modal fade zoom" tabindex="-1" id="shareOpportunity" style="border-radius: 16px;">
        <div class="modal-dialog" role="document" style="border-radius: 16px;">
            <div class="modal-content" style="border-radius: 16px;">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header" style="border-bottom: none !important;">
                    <h4 class="modal-title"><b>Share Post</b></h4>
                </div>
                <div class="modal-body">
                    <hr style=" margin-top: -25px;">
                    <div class="row">
                        <div class="col-md-12">
                            <p><em class="icon ni ni-bulb"></em> You can copy and share post with your family and friends on all platforms.</p>
                            <p class="alert alert-lighter bg-lighter text-primary no-border" style="padding: 10px;border-radius: 4px;margin-bottom: 15px;border: none !important;"><b><span id="shareableLink"></span></b>
                            </p>
                            <div class="btn btn-outline-lighter copyLinkButton bold" style="float: right !important;" onclick="copyLinkToClipboard()"> <em class="icon ni ni-copy"></em> Copy link
                            </div>
                            <span class="copyStatus"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("scripts")
    <script>
        /**
         * setup selected fixed term opportunities
         * shimmer loader
         */
        const fixedTermOpportunityShimmerLoader = `<div class="card card-bordered s-fixed-term-card" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 4px;
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;transform: scale(1);
animation: pulse 2s infinite;">
            <div class="card-header text bg-lighter" style="border-radius: 4px; margin:5px;display: flex; flex-direction: row;padding-left: 15px;padding-right: 15px;">
                <div style="flex: 1;" class="role-line"> <br> <div class="date-line" style="font-size: 10px;"> </div></div> <div class="user-avatar bg-secondary-dim sm internship-line" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
                </div> </div>
            <div class="card-body">
                <div class="company-line mt-3"></div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="company-line"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="company-line" style="height: 100px;"></div>
                    </div>
                </div>
                <div class="company-line mt-3"></div>
            </div>
        </div>`

        /**
         * fixed term opportunity details
         * shiller loader
         */

         const fixedTermOpportunityDetailsShimmerLoader = `<div class="card card-bordered s-fixed-term-card" style="/* From https://css.glass */
        background: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
        /*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
        backdrop-filter: blur(5px);
        margin-bottom: 15px;
        -webkit-backdrop-filter: blur(5px);
        border: 1px solid #dbdfea;transform: scale(1);
        animation: pulse 2s infinite;">
        <div class="card-header text bg-lighter" style="border-radius: 4px; margin:5px;display: flex; flex-direction: row;padding-left: 15px;padding-right: 15px;">
            <div style="flex: 1;" class="role-line"> <br> <div class="date-line" style="font-size: 10px;"> </div></div> <div class="user-avatar bg-secondary-dim sm internship-line" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
            </div> </div>
        <div class="card-body">
            <div class="company-line mt-3"></div>
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="company-line"></div>
            </div>
            <div class="col-md-6">
                <div class="company-line" style="height: 100px;"></div>
            </div>
        </div>
        <div class="company-line mt-3" style="height: 150px;"></div>
        <div class="company-line mt-3" style="height: 150px;"></div>

        <div class="company-line mt-3"></div>

        <div class="company-line mt-3"></div>
        </div>
         <div class="card-header text-dark bg-lighter"
                 style="border-radius: 4px; margin:5px; display: flex; flex-direction: row;justify-content: space-between">
                <div class="company-line" style="width: 45px;height:45px;"></div>
                <div class="company-line" style="width: 45px;height:45px;"></div>
                <div class="company-line" style="width: 65px;height:45px;"></div>
            </div>
        </div>`

        /**
         * other fixed term opportunities shimmer loader
         * @param isLoading
         */
        const otherFixedTermOpportunitiesShimmerLoader = `
<div class="row mt-1">
    <div class="col-md-12">
        <div class="card card-bordered s-fixed-term-card" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 4px;
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;transform: scale(1);
animation: pulse 2s infinite;">
            <div class="card-header text bg-lighter" style="border-radius: 4px; margin:5px;display: flex; flex-direction: row;padding-left: 15px;padding-right: 15px;">
                <div style="flex: 1;" class="role-line"> <br> <div class="date-line" style="font-size: 10px;"> </div></div> <div class="user-avatar bg-secondary-dim sm internship-line" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
                </div> </div>
            <div class="card-body">
                <div class="company-line mt-3"></div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="company-line"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="company-line" style="height: 100px;"></div>
                    </div>
                </div>
                <div class="company-line mt-3"></div>
            </div>
            <div class="card-header text-dark bg-lighter"
                 style="border-radius: 4px; margin:5px; display: flex; flex-direction: row;justify-content: space-between">
                <div class="company-line" style="width: 45px;height:45px;"></div>
                <div class="company-line" style="width: 45px;height:45px;"></div>
                <div class="company-line" style="width: 45px;height:45px;"></div>
            </div>
        </div>
        <div class="card card-bordered s-fixed-term-card" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 4px;
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;transform: scale(1);
animation: pulse 2s infinite;">
            <div class="card-header text bg-lighter" style="border-radius: 4px; margin:5px;display: flex; flex-direction: row;padding-left: 15px;padding-right: 15px;">
                <div style="flex: 1;" class="role-line"> <br> <div class="date-line" style="font-size: 10px;"> </div></div> <div class="user-avatar bg-secondary-dim sm internship-line" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
                </div> </div>
            <div class="card-body">
                <div class="company-line mt-3"></div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="company-line"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="company-line" style="height: 100px;"></div>
                    </div>
                </div>
                <div class="company-line mt-3"></div>
            </div>
            <div class="card-header text-dark bg-lighter"
                 style="border-radius: 4px; margin:5px; display: flex; flex-direction: row;justify-content: space-between">
                <div class="company-line" style="width: 45px;height:45px;"></div>
                <div class="company-line" style="width: 45px;height:45px;"></div>
                <div class="company-line" style="width: 45px;height:45px;"></div>
            </div>
        </div>
        <div class="card card-bordered s-fixed-term-card" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 4px;
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;transform: scale(1);
animation: pulse 2s infinite;">
            <div class="card-header text bg-lighter" style="border-radius: 4px; margin:5px;display: flex; flex-direction: row;padding-left: 15px;padding-right: 15px;">
                <div style="flex: 1;" class="role-line"> <br> <div class="date-line" style="font-size: 10px;"> </div></div> <div class="user-avatar bg-secondary-dim sm internship-line" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
                </div> </div>
            <div class="card-body">
                <div class="company-line mt-3"></div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="company-line"></div>
                    </div>
                    <div class="col-md-6">
                        <div class="company-line" style="height: 100px;"></div>
                    </div>
                </div>
                <div class="company-line mt-3"></div>
            </div>
            <div class="card-header text-dark bg-lighter"
                 style="border-radius: 4px; margin:5px; display: flex; flex-direction: row;justify-content: space-between">
                <div class="company-line" style="width: 45px;height:45px;"></div>
                <div class="company-line" style="width: 45px;height:45px;"></div>
                <div class="company-line" style="width: 45px;height:45px;"></div>
            </div>
        </div>
    </div>
</div>`

        const isLoadingFixedTermOpportunity = (isLoading) => {
            if (isLoading) {
                /**
                 * loaders
                 */
                $("#fixedTermOpportunityShimmerLoader").html(fixedTermOpportunityShimmerLoader)
                $("#fixedTermOpportunityDetailsShimmerLoader").html(fixedTermOpportunityDetailsShimmerLoader)
                $("#otherFixedTermOpportunitiesShimmerLoader").html(otherFixedTermOpportunitiesShimmerLoader)
                $("#fixedTermOpportunityShimmerLoader").show()
                $("#otherFixedTermOpportunitiesShimmerLoader").show()
                $("#fixedTermOpportunityDetailsShimmerLoader").show()

                /**
                 * contnet
                 */
            } else {
                /**
                 * loaders
                 */
                $("#fixedTermOpportunityShimmerLoader").hide()
                $("#otherFixedTermOpportunitiesShimmerLoader").hide()
                $("#fixedTermOpportunityDetailsShimmerLoader").hide()

                /**
                 * content
                 */
            }
        }

        const ComponentFixedTermOpportunitiesFetchError = () => {
            return `
                <p class="text-center">Oops...something went wrong</p>
            `
        }

        const ComponentFixedTermOpportunity = (post) => {
            let isInternship = ``;
            if (post.is_internship === "yes") {
                isInternship += `<div class="user-avatar bg-secondary-dim sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
                            <em class="icon ni ni-book-read"></em>
                        </div> `
            }

            return `
            <div
               style="text-decoration: none !important;" class="">
                <div class="card card-bordered " style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 4px;
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;">
                    <div class="card-header text bg-lighter" style="border-radius: 4px; margin:5px;display: flex; flex-direction: row;padding-left: 15px;padding-right: 15px;">
                        <div style="flex: 1;"><b>${post.title}</b> <br> <span style="font-size: 10px;"> <em class="icon ni ni-clock"></em> ${post.createdOn}</span></div> ${isInternship}</div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="title" style="font-size: 10px;color: #777;">Company</div>
                                <div class="issuer text-danger"><em
                                            class="icon ni ni-building"></em> ${post.employer}
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="title" style="font-size: 10px;color: #777;">Budget
                                    (GHS/month)
                                </div>
                                <div class="issuer text-success"><em
                                            class="icon ni ni-coins"></em> ${post.min_budget}
                                        - ${post.max_budget}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="title" style="font-size: 10px;color: #777;">Duration</div>
                                <div class="issuer card bg-lighter text-center" style="height: 100px;">
                                    <div style="font-size: 22px;margin-top:20px;">
                                        <b>${post.duration}</b></div>
                                    <div>month(s)</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                <div class="issuer text"><em
                                            class="icon ni ni-map-pin"></em> ${post.location} (
            ${post.distance}
            km)</div>
                </div>
                </div>
                </div>
</div></div>
            `
        }

        const ComponentFixedTermOpportunityDetails = (post) => {
            let isInternship = ``;
            if (post.is_internship === "yes") {
                isInternship += `<div class="user-avatar bg-secondary-dim sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
                            <em class="icon ni ni-book-read"></em>
                        </div> `
            }

            const applicationURL = `{{env("BACKEND_URL")}}/posts/${post.id}/apply`

            let applicationConfirmation = `<a data-bs-toggle="tooltip" data-bs-placement="right" title="Apply" href="${applicationURL}" onclick="return confirm('Are your sure?')" class="btn btn-primary"> <b>Apply</b></a>`;
            if (post.has_already_applied === "yes") {
                applicationConfirmation = `<div class="btn btn-outline-lighter"> <b>Application sent!</b></div>`
            }

            return `
               <div class="card card-bordered" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 4px;
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;">
                <div class="card-header text bg-lighter" style="border-radius: 4px; margin:5px;display: flex; flex-direction: row;padding-left: 15px;padding-right: 15px;">
                    <div style="flex: 1;"><b>${post.title}</b> <br> <span style="font-size: 10px;"> <em class="icon ni ni-clock"></em> ${post.postedOn}</span></div> ${isInternship}</div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Company</div>
                            <div class="issuer text-danger"><em
                                    class="icon ni ni-building"></em> ${post.employer}
                            </div>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Budget
                                (GHS/month)
                            </div>
                            <div class="issuer text-success"><em
                                    class="icon ni ni-coins"></em> ${post.min_budget}
                                - ${post.max_budget}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="title" style="font-size: 10px;color: #777;">Duration</div>
                            <div class="issuer card bg-lighter text-center" style="height: 100px;">
                                <div style="font-size: 22px;margin-top:20px;">
                                    <b>${post.duration}</b></div>
                                <div>month(s)</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Job Description</div>
                            <div class="issuer text">${post.description}</div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Qualifications</div>
                            <div class="issuer text">
                                ${post.qualifications}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Location</div>
                            <div class="issuer text"><em
                                    class="icon ni ni-map-pin"></em> ${post.location}
                                (${post.distance})</div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Deadline</div>
                            <div class="issuer text text-danger"><em
                                    class="icon ni ni-calendar "></em> ${post.date} ${post.time}</div>
                        </div>
                    </div>
                </div>
                <div class="card-header text-dark bg-lighter"
                     style="border-radius: 4px; margin:5px; display: flex; flex-direction: row;justify-content: space-between;">
                    <a data-bs-toggle="tooltip" data-bs-placement="right" title="Save post for later" onclick="saveFixedTermOpportunity('${post.id}')"  href="javascript:void(0)" class="btn btn-outline-light bg-white"><em class="icon ni ni-bookmark saveFixedTermOpportunityIcon" id="saveFixedTermOpportunityIcon${post.id}"></em> <em class="icon ni ni-bookmark-fill text-primary savedFixedTermOpportunityIcon" id="savedFixedTermOpportunityIcon${post.id}"></em>
                        <div class="saveFixedTermOpportunityLoader" id="saveFixedTermOpportunityLoader${post.id}">
                            <div class="spinner-border" role="status"></div>
                        </div></a>


                    <a type="button" data-toggle="modal" data-target="#shareOpportunity" onclick="setupShareableLink('${post.type}', '${post.id}')" href="javascript:void(0)" class="btn btn-outline-light bg-white"><em
                            class="icon ni ni-share" data-toggle="tooltip" data-bs-placement="right" title="Share with family and friends"></em></a>


                    ${applicationConfirmation}

                </div>

            </div>
            `
        }

        const ComponentOtherFixedTermOpportunities = (post) => {
            let isInternship = ``;
            if (post.is_internship === "yes") {
                isInternship += `<div class="user-avatar bg-secondary-dim sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
                            <em class="icon ni ni-book-read"></em>
                        </div> `
            }

            const route = `{{env("BACKEND_URL")}}/part-time-jobs/${post.id}`

            return `
        <div class="col-md-12">
            <a href="${route}"
               style="text-decoration: none !important;" class="cardContainer">
                <div class="card card-bordered cardContainer" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
border-radius: 4px;
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;">
                    <div class="card-header text bg-lighter" style="border-radius: 4px; margin:5px;display: flex; flex-direction: row;padding-left: 15px;padding-right: 15px;">
                        <div style="flex: 1;"><b>${post.title}</b> <br> <span style="font-size: 10px;"> <em class="icon ni ni-clock"></em> ${post.createdOn}</span></div> ${isInternship}</div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="title" style="font-size: 10px;color: #777;">Company</div>
                                <div class="issuer text-danger"><em
                                            class="icon ni ni-building"></em> ${post.employer}
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <div class="title" style="font-size: 10px;color: #777;">Budget
                                    (GHS/month)
                                </div>
                                <div class="issuer text-success"><em
                                            class="icon ni ni-coins"></em> ${post.min_budget}
                                        - ${post.max_budget}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="title" style="font-size: 10px;color: #777;">Duration</div>
                                <div class="issuer card bg-lighter text-center" style="height: 100px;">
                                    <div style="font-size: 22px;margin-top:20px;">
                                        <b>${post.duration}</b></div>
                                    <div>month(s)</div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                <div class="issuer text"><em
                                            class="icon ni ni-map-pin"></em> ${post.location} (
            ${post.distance}
            km)</div>
                </div>
                </div>
                </div>
                <div class="card-header text-dark bg-lighter"
                style="border-radius: 4px; margin:5px; display: flex; flex-direction: row;justify-content: space-between;">
                <a data-bs-toggle="tooltip" data-bs-placement="right" title="Save post for later" onclick="saveFixedTermOpportunity('${post.id}')"  href="javascript:void(0)" class="btn btn-outline-light bg-white"><em class="icon ni ni-bookmark saveFixedTermOpportunityIcon" id="saveFixedTermOpportunityIcon${post.id}"></em> <em class="icon ni ni-bookmark-fill text-primary savedFixedTermOpportunityIcon" id="savedFixedTermOpportunityIcon${post.id}"></em>
<div class="saveFixedTermOpportunityLoader" id="saveFixedTermOpportunityLoader${post.id}">
                        <div class="spinner-border" role="status"></div>
                </div></a>


                <a type="button" data-toggle="modal" onclick="setupShareableLink('${post.type}', '${post.id}')" data-target="#shareOpportunity" href="javascript:void(0)" class="btn btn-outline-light bg-white"><em
                    class="icon ni ni-share" data-toggle="tooltip" data-bs-placement="right" title="Share with family and friends"></em></a>


                <a data-bs-toggle="tooltip" data-bs-placement="right" title="See more details" href="#" class="btn btn-outline-light bg-white"><em
                    class="icon ni ni-list-round"></em></a>

                </div>

                </div>
                </a>
                </div>
                `
        }


        const getFixedTermOpportunity = () => {

            isLoadingFixedTermOpportunity(true)

            /**
             * make api call
             */
            $.ajax({
                url: "{{env("BACKEND_URL")}}/getFixedTermOpportunityDetails/{{$uuid}}",
                method: "GET",
                dataType: "json",
                contentType: "application/json",
                data: {},
                crossDomain: true,
                success: function (_data) {
                    const data = _data.data
                    $("#otherFixedTermOpportunitiesListing").html("")
                    $("#fixedTermOpportunitySnapshot").html("")
                    $("#fixedTermOpportunityDetails").html("")

                    $("#fixedTermOpportunitySnapshot").html(ComponentFixedTermOpportunity(data.opportunity))
                    $("#fixedTermOpportunityDetails").html(ComponentFixedTermOpportunityDetails(data.opportunity))

                    let otherFixedTermOpportunitiesList = `<div class="row">`
                    $.each(data.otherOpportunities, function (key, opportunity) {
                        otherFixedTermOpportunitiesList += ComponentOtherFixedTermOpportunities(opportunity)
                    })
                    $("#otherFixedTermOpportunitiesListing").html(otherFixedTermOpportunitiesList)
                    otherFixedTermOpportunitiesList += `</div>`
                    $(".savedFixedTermOpportunityIcon").hide()
                    $(".saveFixedTermOpportunityLoader").hide()
                    isLoadingFixedTermOpportunity(false)
                },
                error: function (e) {
                    $("#fixedTermOpportunitiesListing").html(ComponentFixedTermOpportunitiesFetchError())
                    isLoadingFixedTermOpportunity(false)
                }
            })
        }

        getFixedTermOpportunity()

    </script>
@endsection
