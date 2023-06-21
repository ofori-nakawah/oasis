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
        <div class="col-md-4">
            <div class="row " style="position: -webkit-sticky;
  position: sticky;top: 0">
                <div class="col-md-12" style="margin-top: 45px;">
                    <p class="text-dark">More filters</p>
                    <div style="display: flex;gap: 10px">
                        <div class="form-control-wrap" style="margin-bottom: 15px;flex: 1">
                            <div class="form-icon form-icon-left">
                                <em class="icon ni ni-search"></em>
                            </div>
                            <input type="text" class="form-control form-control-lg" name="search" id="searchFixedTermJobOpportunities"
                                   placeholder="Search keywords" style="border-radius: 4px;height: 60px;">
                        </div>
                        <button class="btn btn-outline-light d-md-none d-lg-none"
                                style="height: 60px;border-radius: 4px;"><em class="icon ni ni-filter"></em></button>
                    </div>
                    <p class="mb-3"><em class="icon ni ni-bulb"></em> Hit enter to search</p>
                    <hr>

                    <div class="mb-2">
                        <label class="form-label">Open to</label>
                        <div style="display: flex;flex-direction: row">
                            <div class="user-toggle" style="flex: 1">
                                <div class="user-avatar bg-secondary-dim sm">
                                    <em class="icon ni ni-book-read"></em>
                                </div>
                                <div class="user-info" style="">
                                    <div class="nk-menu-text text"><b>Internships</b></div>
                                </div>
                            </div>
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input" id="isInternship" onclick="getFixedTermInternshipOpportunities()">
                                <label class="custom-control-label" for="isInternship"></label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <label class="form-label">Categories</label>
                        <div class="card card-bordered pt-2 pl-3 pr-2"
                             style="height: 46px;border-radius: 4px;display: flex;flex-direction: row">
                            <div class="text-muted" style="flex: 1">Eg. Barber, Fashion Designer</div>
                            <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Search radius</label>
                        <div class="card card-bordered pt-2 pl-3 pr-2"
                             style="height: 46px;border-radius: 4px;display: flex;flex-direction: row">
                            <div class="text-muted" style="flex: 1">Eg. 11km from me</div>
                            <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Budget range</label>
                        <div class="card card-bordered pt-2 pl-3 pr-2"
                             style="height: 46px;border-radius: 4px;display: flex;flex-direction: row">
                            <div class="text-muted" style="flex: 1">Eg. Between GHS240 and GHS490</div>
                            <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>
                        </div>
                    </div>

                    <hr>


                    <button class="btn btn-block btn-outline-light btn-lg">Reset filter</button>

                </div>
            </div>
        </div>
        <div class="col-md-8" style="margin-top: 50px;">
                <div class="text-dark mb-3" style="display: flex; flex-direction: row;">
                    <div style="flex:1" id="fixedTermOpportunitiesCountShimmerLoader"></div>
                    <div style="flex:1" id="fixedTermOpportunitiesCount"></div>
                    <div style="display:flex; gap: 20px;border-bottom: 1px solid #dbdfea;">
                        <span class="text-primary" style="border-bottom: 3px solid #353299;"><b>All</b></span>
                        <a href="#" class="text-dark">Saved</a>
                    </div>
                </div>

                <div class="row" style="margin-top: -5px;">
                    <div id="fixedTermOpportunitiesListingLoader" class="col-md-12"></div>
                    <div class="col-md-12">
                        <div id="fixedTermOpportunitiesListing"></div>
                    </div>
                </div>
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
         * setup fixed term opportunities
         * shimmer loader
         */
        const isLoadingFixedTermOpportunities = (isLoading) => {
            const fixedTermOpportunitiesShimmerLoader = `
<div class="row mt-1">
    <div class="col-md-6">
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
    <div class="col-md-6">
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
    </div>
    <div class="row">
    <div class="col-md-6">
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
    <div class="col-md-6">
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
            const fixedTermOpportunitiesCountShimmerLoader = `<div class="s-fixed-term-card" style="width: 130px !important;"><div class="date-line" style="width: 130px !important;"></div></div>`

            if (isLoading) {
                $("#fixedTermOpportunitiesCountShimmerLoader").html(fixedTermOpportunitiesCountShimmerLoader)
                $("#fixedTermOpportunitiesListingLoader").html(fixedTermOpportunitiesShimmerLoader)
                $("#fixedTermOpportunitiesCountShimmerLoader").show()
                $("#fixedTermOpportunitiesListingLoader").show()
            } else {
                $("#fixedTermOpportunitiesCountShimmerLoader").hide()
                $("#fixedTermOpportunitiesListingLoader").hide("slow")
            }
        }

        const ComponentFixedTermOpportunity = (post) => {
            let isInternship = ``;
            if (post.is_internship === "yes") {
                isInternship += `<div class="user-avatar bg-secondary-dim sm" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Offers internship">
                            <em class="icon ni ni-book-read"></em>
                        </div> `
            }

            const route = `{{env("BACKEND_URL")}}/part-time-jobs/${post.id}`

            return `
        <div class="col-md-6">
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


                <a type="button"  onclick="setupShareableLink('${post.type}', '${post.id}')" data-toggle="modal" data-target="#shareOpportunity" href="javascript:void(0)" class="btn btn-outline-light bg-white"><em
                    class="icon ni ni-share" data-toggle="tooltip" data-bs-placement="right" title="Share with family and friends"></em></a>


                <a data-bs-toggle="tooltip" data-bs-placement="right" title="See more details" href="#" class="btn btn-outline-light bg-white"><em
                    class="icon ni ni-list-round"></em></a>

                </div>

                </div>
                </a>
                </div>
                `
        }

        const ComponentFixedTermOpportunitiesFetchError = () => {
            return `
                <p class="text-center">Oops...something went wrong</p>
            `
        }

        /**
         * get fixed term opportunities
         * display fixed term opportunities
         */
        const getFixedTermOpportunities = () => {
            isLoadingFixedTermOpportunities(true)

            /**
             * make api call
             */
            $.ajax({
                url: "{{env("BACKEND_URL")}}/getFixedTermOpportunities",
                method: "GET",
                dataType: "json",
                contentType: "application/json",
                data: {},
                crossDomain: true,
                success: function (_data) {
                    const data = _data.data
                    localStorage.setItem("fixedTermPosts", JSON.stringify(data))
                    $("#fixedTermOpportunitiesListing").html("")
                    let fixedTermOpportunitiesList = `<div class="row">`
                    let counter = 0;
                    $.each(data, function (key, opportunity) {
                        fixedTermOpportunitiesList += ComponentFixedTermOpportunity(opportunity)
                        counter++;
                    })
                    $("#fixedTermOpportunitiesCount").html(`<p>${counter} jobs listed</p>`)
                    $("#fixedTermOpportunitiesListing").html(fixedTermOpportunitiesList)
                    fixedTermOpportunitiesList += `</div>`
                    $(".savedFixedTermOpportunityIcon").hide()
                    $(".saveFixedTermOpportunityLoader").hide()
                    isLoadingFixedTermOpportunities(false)
                },
                error: function (e) {
                    $("#fixedTermOpportunitiesListing").html(ComponentFixedTermOpportunitiesFetchError())
                    isLoadingFixedTermOpportunities(false)
                }
            })
        }

        getFixedTermOpportunities()

        /**
         * save post flow
         * @param post
         */
        const saveFixedTermOpportunity = (postId) => {
            console.log(postId)
            /**
             * change icon to loader
             */
            $(`#saveFixedTermOpportunityIcon${postId}`).hide()
            $(`#saveFixedTermOpportunityLoader${postId}`).show()

            /**
             * change loader back to icon
             * after api call
             */
            setTimeout(function () {
                $(`#savedFixedTermOpportunityIcon${postId}`).show()
                $(`#saveFixedTermOpportunityLoader${postId}`).hide()

                NioApp.Toast('Opportunity has added to saved items.', 'success', {
                    position: 'bottom-center'
                });
            }, 3000)

            /**
             * toast
             */

        }
    </script>
    <script src="{{asset('public/js/work/fixed-term-jobs/index.js')}}"></script>
@endsection
