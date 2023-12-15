@extends("layouts.master")

@section('title')
    Work
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title page-title"><em class="icon ni ni-briefcase"></em> Work </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    <nav>
                        <ul class="breadcrumb breadcrumb-arrow">
                            <li class="breadcrumb-item"><a href="#">Fixed Term Jobs</a></li>
                            <li class="breadcrumb-item"><a href="#">Discover fixed term opportunities near you and all
                                    over the country</a></li>
                        </ul>
                    </nav>
                    </p>
                </div>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content">
                <a href="{{URL::previous()}}"
                   class="btn btn-outline-primary"><span>Back</span></a></li>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->
    <div class="row">
        <div class="col-md-8">
            <div id="fixedTermOpportunityDetailsShimmerLoader"></div>
            <div id="fixedTermOpportunityDetails"></div>
        </div>
        <div class="col-md-4 ">
            <div class="card card-outline card-bordered">
                <div class="card-body">
                    <div style="color: #1c2b46 !important;">Notifications</div>
                    {{--                icon--}}
                    <div style="margin-top: 20px;margin-bottom: 20px;" class="text-center">
                        <img src="{{asset('assets/html-template/src/images/notify.svg')}}"
                             height="160px; width: 160px;"/>
                    </div>
                    {{--                description--}}
                    <div>Want to receive notifications for similar opportunities?</div>
                    <p class="text-muted" style="margin-top: 10px;">You'll receive email/sms notifications when ever a
                        new opportunity with similar tags, title, description or qualification is posted. Try now!</p>
                    {{--                Button--}}
                    <button class="btn btn-primary btn-block btn-lg">Notify me!</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2 mt-2">
        <div class="col-md-12">
            <p class="text-dark" style="display: flex;"><span style="flex: 1;">Other opportunities</span></p>

            <div id="otherFixedTermOpportunitiesShimmerLoader"></div>
            <div id="otherFixedTermOpportunitiesListing"></div>
        </div>
    </div>


    <div class="modal fade zoom" tabindex="-1" id="shareOpportunity" style="border-radius: 4px;">
        <div class="modal-dialog" role="document" style="border-radius: 4px;">
            <div class="modal-content" style="border-radius: 4px;">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross text-danger"></em>
                </a>
                <div class="modal-header" style="border-bottom: none !important;">
                    <h4 class="modal-title"><b>Share Post</b></h4>
                </div>
                <div class="modal-body">
                    <hr style=" margin-top: -25px;">
                    <div class="row">
                        <div class="col-md-12">
                            <p><em class="icon ni ni-bulb"></em> You can copy and share post with your family and
                                friends on all platforms.</p>
                            <p class="alert alert-lighter bg-lighter text-primary no-border"
                               style="padding: 10px;border-radius: 4px;margin-bottom: 15px;border: none !important;"><b><span
                                        id="shareableLink"></span></b>
                            </p>
                            <div class="btn btn-outline-primary copyLinkButton bold" style="float: right !important;"
                                 onclick="copyLinkToClipboard()"><em class="icon ni ni-copy"></em> Copy link
                            </div>
                            <span class="copyStatus text-success"></span>
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
        const fixedTermOpportunityShimmerLoader = `<div>Loading...</div>`

        /**
         * fixed term opportunity details
         * shiller loader
         */

        const fixedTermOpportunityDetailsShimmerLoader = `<div>Loading...</div>`

        /**
         * other fixed term opportunities shimmer loader
         * @param isLoading
         */
        const otherFixedTermOpportunitiesShimmerLoader = `<div>Loading...</div>`

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
                isInternship += `<span style="border: 1px solid #364a63; padding: 5px;word-wrap: break-word;
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;">Internship</span>`
            }

            let categories = ``
            const tags = JSON.parse(post.tags)
            tags.forEach((tag) => {
                categories += `<span style="border: 1px solid #364a63; padding: 5px;word-wrap: break-word;
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;">${tag}</span>`
            })


            const route = `{{env("BACKEND_URL")}}/part-time-jobs/${post.id}`

            return `
        <div class="col-md-6">
<!--             <a href="${route}"-->
<!--               style="text-decoration: none !important;" class="cardContainer">-->
                <div class="card card-bordered cardContainer" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
padding: 10px;
/*border-radius: 4px;*/
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;">

                    <div style="display: flex; flex-direction: row; padding: 10px;">
                        <div style="display: flex; flex-direction: row; gap: 10px;flex: 1;">
<!--                           <div style="border: 1px solid #364a63; padding: 5px; height: 80px; width: 80px !important;border-radius: 4px;">Image</div>-->
                           <div style="display: flex; justify-content: center;flex-direction: column;">
                              <div style="font-size: 18px;color: #1c2b46 !important">${post.title}</div>
                              <div>${post.employer}</div>
                              <div class="text-muted"><small>Posted ${post.createdOn} by ${post.user?.name}</small></div>
                           </div>
                        </div>
<!--                        <div>Save</div>-->
                    </div>

                    <div style="padding: 10px;display: flex; flex-direction: row; gap: 10px;overflow-x: scroll;">${isInternship} ${categories}</div>

                    <div style="display: flex; flex-direction: row;gap: 10px; padding: 5px;">
                        <div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-coins"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text"> <small class="text-muted">Budget</small>  <br>
 ${post.min_budget}GHS - ${post.max_budget}GHS budget</div>
                            </div>
                        </div>
                        <div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-calendar-alt"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text">
                                           <small class="text-muted">Duration</small>  <br>
${post.duration} months
                                </div>
                            </div>
                        </div>
                    </div>


                    <div style="padding: 5px;"><div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-map-pin"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text"><small class="text-muted">Location</small>  <br> ${post.location}</div>
                            </div>
                        </div>
                    </div>



                <hr style="border-color: #dbdfea;">

                <div class="row text-center" style="padding: 10px;">
                    <div class="col-md-6 col-xs-6 col-sm-6 " style="margin-top: 5px;">
                        <a type="button" style="float: left;justify-content: center;align-items: center"  onclick="setupShareableLink('${post.type}', '${post.id}')" data-toggle="modal" data-target="#shareOpportunity" href="javascript:void(0)" >
                            <div style="display: flex; flex-direction: row; gap: 5px;justify-content: center;">
                                <em class="icon ni ni-share-alt" style="font-size: 24px;" data-toggle="tooltip" data-bs-placement="right" title="Share with family and friends"></em>
                                <div style="margin-bottom: 10px;">Share opportunity</div>
                            </div>
                        </a>
                    </div>
                        <div class="col-md-6 col-xs-6 col-sm-6 text-center" style="flex: 1"><a data-bs-toggle="tooltip" data-bs-placement="right" title="See more details" href="${route}" class="btn btn-outline-primary btn-block" style="height: 40px;margin-top: 0px;">View Details</a></div>
                    </div>
                </div>

<!--                </div>-->
<!--                </a>-->
                </div>
                `
        }

        const ComponentFixedTermOpportunityDetails = (post) => {
            let isInternship = ``;
            if (post.is_internship === "yes") {
                isInternship += `<span style="border: 1px solid #364a63; padding: 5px;word-wrap: break-word;
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;">Internship</span>`
            }

            let categories = ``
            const tags = JSON.parse(post.tags)
            tags.forEach((tag) => {
                categories += `<span style="border: 1px solid #364a63; padding: 5px;word-wrap: break-word;
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;">${tag}</span>`
            })

            const applicationURL = `{{env("BACKEND_URL")}}/posts/${post.id}/apply`

            let applicationConfirmation = `<a data-bs-toggle="tooltip" data-bs-placement="right" title="Apply" href="${applicationURL}" onclick="return confirm('Are your sure?')" class="btn btn-outline-primary"> Apply</a>`;
            if (post.has_already_applied === "yes") {
                applicationConfirmation = `<div class="text-success">Application has already been sent!</div>`
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
                <div class="card-body">
                    <div style="display: flex; flex-direction: row;">
                        <div style="display: flex; flex-direction: row; gap: 10px;flex: 1;">
<!--                           <div style="border: 1px solid #364a63; padding: 5px; height: 80px; width: 80px !important;border-radius: 4px;">Image</div>-->
                           <div style="display: flex; justify-content: center;flex-direction: column;">
                              <div style="font-size: 18px;color: #1c2b46 !important">${post.title}</div>
                              <div>${post.employer}</div>
                              <div class="text-muted"><small>Posted ${post.createdOn} by ${post.user?.name}</small></div>
                           </div>
                        </div>
<!--                        <div>Save</div>-->
                    </div>

                    <div style="display: flex; flex-direction: row; gap: 10px;flex-wrap: wrap;margin-top: 15px;">${isInternship} ${categories}</div>

                    <div style="display: flex; flex-direction: row;gap: 10px;">
                        <div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-coins"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text"> <small class="text-muted">Budget</small>  <br>
 ${post.min_budget}GHS - ${post.max_budget}GHS budget</div>
                            </div>
                        </div>
                        <div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-calendar-alt"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text">
                                           <small class="text-muted">Duration</small>  <br>
${post.duration} months
                                </div>
                            </div>
                        </div>

                        <div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-calendar-alt"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text">
                                           <small class="text-muted">Application Deadline</small>  <br>
                                    ${post.date} ${post.time}
                                </div>
                            </div>
                        </div>

                    </div>

                        <div class="user-toggle mb-2">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-map-pin"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text"><small class="text-muted">Location</small>  <br> ${post.location}</div>
                            </div>
                        </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-muted"><small>Description</small></div>
                            <div class="issuer text">${post.description}</div>
                        </div>
                    </div>

                    <div class="row mt-2 mb-2">
                        <div class="col-md-12">
                            <div class="text-muted"><small>Qualifications</small></div>
                            <div class="issuer text">
                                ${post.qualifications}
                            </div>
                        </div>
                    </div>




                <hr style="border-color: #dbdfea;">

                <div class="row text-center" style="padding: 10px;">
                    <div class="col-md-6 col-xs-6 col-sm-6 " style="margin-top: 5px;">
                        <a type="button" style="float: left;justify-content: center;align-items: center"  onclick="setupShareableLink('${post.type}', '${post.id}')" data-toggle="modal" data-target="#shareOpportunity" href="javascript:void(0)" >
                            <div style="display: flex; flex-direction: row; gap: 5px;justify-content: center;">
                                <em class="icon ni ni-share-alt" style="font-size: 24px;" data-toggle="tooltip" data-bs-placement="right" title="Share with family and friends"></em>
                                <div style="margin-bottom: 10px;">Share opportunity</div>
                            </div>
                        </a>
                    </div>
                        <div class="col-md-6 col-xs-6 col-sm-6 text-right" style="flex: 1"> ${applicationConfirmation}</div>
                    </div>
                </div>

                </div>


            </div>
            `
        }

        const ComponentOtherFixedTermOpportunities = (post) => {
            let isInternship = ``;
            if (post.is_internship === "yes") {
                isInternship += `<span style="border: 1px solid #364a63; padding: 5px;word-wrap: break-word;
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;">Internship</span>`
            }

            let categories = ``
            const tags = JSON.parse(post.tags)
            tags.forEach((tag) => {
                categories += `<span style="border: 1px solid #364a63; padding: 5px;word-wrap: break-word;
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;">${tag}</span>`
            })

            const route = `{{env("BACKEND_URL")}}/part-time-jobs/${post.id}`

            return `
        <div class="col-md-4">
<!--             <a href="${route}"-->
<!--               style="text-decoration: none !important;" class="cardContainer">-->
                <div class="card card-bordered cardContainer" style="/* From https://css.glass */
background: rgba(255, 255, 255, 0.2);
padding: 10px;
/*border-radius: 4px;*/
/*box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);*/
backdrop-filter: blur(5px);
margin-bottom: 15px;
-webkit-backdrop-filter: blur(5px);
border: 1px solid #dbdfea;">

                    <div style="display: flex; flex-direction: row; padding: 10px;">
                        <div style="display: flex; flex-direction: row; gap: 10px;flex: 1;">
<!--                           <div style="border: 1px solid #364a63; padding: 5px; height: 80px; width: 80px !important;border-radius: 4px;">Image</div>-->
                           <div style="display: flex; justify-content: center;flex-direction: column;">
                              <div style="font-size: 18px;color: #1c2b46 !important">${post.title}</div>
                              <div>${post.employer}</div>
                              <div class="text-muted"><small>Posted ${post.createdOn} by ${post.user?.name}</small></div>
                           </div>
                        </div>
<!--                        <div>Save</div>-->
                    </div>

                    <div style="padding: 10px;display: flex; flex-direction: row; gap: 10px;overflow-x: scroll;">${isInternship} ${categories}</div>

                    <div style="display: flex; flex-direction: row;gap: 10px; padding: 5px;">
                        <div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-coins"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text"> <small class="text-muted">Budget</small>  <br>
 ${post.min_budget}GHS - ${post.max_budget}GHS budget</div>
                            </div>
                        </div>
                        <div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-calendar-alt"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text">
                                           <small class="text-muted">Duration</small>  <br>
${post.duration} months
                                </div>
                            </div>
                        </div>
                    </div>


                    <div style="padding: 5px;"><div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-map-pin"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text"><small class="text-muted">Location</small>  <br> ${post.location}</div>
                            </div>
                        </div>
                    </div>



                <hr style="border-color: #dbdfea;">

                <div class="row text-center" style="padding: 10px;">
                    <div class="col-md-6 col-xs-6 col-sm-6 " style="margin-top: 5px;">
                        <a type="button" style="float: left;justify-content: center;align-items: center"  onclick="setupShareableLink('${post.type}', '${post.id}')" data-toggle="modal" data-target="#shareOpportunity" href="javascript:void(0)" >
                            <div style="display: flex; flex-direction: row; gap: 5px;justify-content: center;">
                                <em class="icon ni ni-share-alt" style="font-size: 24px;" data-toggle="tooltip" data-bs-placement="right" title="Share with family and friends"></em>
                                <div style="margin-bottom: 10px;">Share opportunity</div>
                            </div>
                        </a>
                    </div>
                        <div class="col-md-6 col-xs-6 col-sm-6 text-center" style="flex: 1"><a data-bs-toggle="tooltip" data-bs-placement="right" title="See more details" href="${route}" class="btn btn-outline-primary btn-block" style="height: 40px;margin-top: 0px;">View Details</a></div>
                    </div>
                </div>

<!--                </div>-->
<!--                </a>-->
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
