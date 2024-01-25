@extends("layouts.master")

@section('title')
    Work
@endsection

@section("content")
    <div class="nk-block-head nk-block-head-sm">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h3 class="nk-block-title " style="font-weight: 900"> Permanent opportunities <br> <span
                        class="text-muted" style="font-size: 20px;">Discover permanent opportunities near you and all over the country</span>
                </h3>
                <div class="nk-block-des text-soft">
                    <p class="hide-mb-sm hide-mb-xs md">
                    </p>
                </div>
            </div>
            <div class="nk-block-head-content">
                <a href="{{route("user.work.jobs", ["type_of_user" => "seeker", "type_of_work" => "permanent"])}}"
                   class="btn btn-outline-light"><span>Back</span></a>
            </div><!-- .nk-block-head-content -->
        </div><!-- .nk-block-between -->
    </div><!-- .nk-block-head -->

    <div class="row">
        <div class="col-md-4">
            <div id="fixedTermOpportunityShimmerLoader"></div>
            <div id="fixedTermOpportunitySnapshot"></div>

            <div class="row mb-2 mt-2" >
                <div class="col-md-12">
                    <p class="text-dark" style="display: flex;"><span style="flex: 1;">Other opportunities</span> <a href="{{route("user.work.jobs", ["type_of_user" => "seeker", "type_of_work" => "permanent"])}}" style="text-decoration: underline">See more</a></p>
                </div>
            </div>



        </div>
        <div class="col-md-8">
            <div id="fixedTermOpportunityDetailsShimmerLoader"></div>
            <div id="fixedTermOpportunityDetails"></div>
        </div>

    </div>

@endsection

@section("scripts")
    <script>
        /**
         * setup selected fixed term opportunities
         * shimmer loader
         */
        const fixedTermOpportunityShimmerLoader = `Loading...`

        /**
         * fixed term opportunity details
         * shiller loader
         */

        const fixedTermOpportunityDetailsShimmerLoader = `Loading...`

        /**
         * other fixed term opportunities shimmer loader
         * @param isLoading
         */
        const otherFixedTermOpportunitiesShimmerLoader = `Loading`

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
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;" class="bg-primary text-white">Internship</span>`
            }

            return `
            <div
               style="text-decoration: none !important;" class="">
                <div class="card card-bordered" style="/* From https://css.glass */
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
                                <div class="issuer "><em
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
                                <div class="issuer card bg-lighter text-center flex justify-center align-center" style="height: 100px;">
                                    <div style="font-size: 22px;">
                                        <b>Permanent</b></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                <div class="issuer text"><em
                                            class="icon ni ni-map-pin"></em> ${post.location} (
            ${post.distance}
            km away)</div>
                </div>
                </div>
                </div>
</div></div>
            `
        }

        const ComponentFixedTermOpportunityDetails = (post) => {
            let isInternship = ``;
            if (post.is_internship === "yes") {
                isInternship += `<span style="border: 1px solid #364a63; padding: 5px;word-wrap: break-word;
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;" class="bg-primary text-white">Internship</span>`
            }

            const route = `{{env("BACKEND_URL")}}/posts/${post.id}/apply`

            console.log(post)

            let applyButton;
            if ('{{auth()->id()}}' != post.user.id) {
                if (post.has_already_applied !== "yes") {
                    applyButton = `<a data-bs-toggle="tooltip" data-bs-placement="right" title="See more details" href="${route}" class="btn btn-outline-primary"> <b>Apply</b></a>`
                } else {
                    applyButton =  `<p>Job application sent!</p>`
                }
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
                            <div class="issuer "><em
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
                            <div class="issuer card bg-lighter text-center flex justify-center align-center" style="height: 100px;">
                                <div style="font-size: 22px;">
                                    <b>Permanent</b></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Job Description</div>
                            <div class="issuer text summernote-description">${post.description}</div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Qualifications</div>
                            <div class="issuer text summernote-qualifications">
                                ${post.qualifications}
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Location</div>
                            <div class="issuer text"><em
                                    class="icon ni ni-map-pin"></em> ${post.location}
                                (${post.distance}km away)</div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Deadline</div>
                            <div class="issuer text-danger" ><em
                                    class="icon ni ni-calendar "></em> ${post.date} ${post.time}</div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <div class="title" style="font-size: 10px;color: #777;">Other relevant information</div>
                            <div class="issuer text" >${post.other_relevant_information}</div>
                            <ul class="mt-3">
                                <li>Budget Negotiable: ${post.is_negotiable === "yes" ? "Yes" : 'No'}</li>
                                <li>Term Renewable: ${post.is_renewable === "yes" ? "Yes" : 'No'}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-header text-dark bg-lighter"
                     style="border-radius: 4px; margin:5px; display: flex; flex-direction: row;justify-content: space-between;align-items: center">


                    <a type="button" data-toggle="modal" data-target="#shareOpportunity" href="javascript:void(0)" class="btn btn-outline-light bg-white"><em
                            class="icon ni ni-share" data-toggle="tooltip" data-bs-placement="right" title="Share with family and friends"></em></a>


                    ${applyButton}

                </div>

            </div>
            `
        }

        const ComponentOtherFixedTermOpportunities = (post) => {
            let isInternship = ``;
            if (post.is_internship === "yes") {
                isInternship += `<span style="border: 1px solid #364a63; padding: 5px;word-wrap: break-word;
  white-space: nowrap;border-radius: 4px;margin-bottom: 10px;" class="bg-primary text-white">Internship</span>`
            }

            const route = `{{env("BACKEND_URL")}}/permanent-jobs/${post.id}`

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
                                <div class="issuer text"><em
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
                                <div class="issuer card bg-lighter text-center flex justify-center align-center" style="height: 100px;">
                                    <div style="font-size: 22px;">
                                        <b>Permanent</b></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title" style="font-size: 10px;color: #777;">Location</div>
                                <div class="issuer text"><em
                                            class="icon ni ni-map-pin"></em> ${post.location} (
            ${post.distance}
            km away)</div>
                </div>
                </div>
                </div>
                <div class="card-header text-dark bg-lighter"
                style="border-radius: 4px; margin:5px; display: flex; flex-direction: row;justify-content: space-between;">



                <a type="button" data-toggle="modal" data-target="#shareOpportunity" href="javascript:void(0)" class="btn btn-outline-light bg-white"><em
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
                url: "{{env("BACKEND_URL")}}/getPermanentOpportunityDetails/{{$uuid}}",
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

