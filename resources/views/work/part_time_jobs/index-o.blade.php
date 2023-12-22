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
                            <li class="breadcrumb-item"><a href="#">Discover fixed term opportunities near you and all over the country</a></li>
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
                            <input type="text" class="form-control form-control-lg" name="search"
                                   id="searchFixedTermJobOpportunities"
                                   placeholder="Search keywords" style="border-radius: 4px;height: 60px;">
                        </div>
                    </div>
                    <p class="mb-3"><em class="icon ni ni-bulb"></em> Hit enter to search</p>
                    <div class="d-none d-sm-block .d-sm-none .d-md-block">
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
                                    <input type="checkbox" class="custom-control-input" id="isInternship"
                                           onclick="getFixedTermInternshipOpportunities()">
                                    <label class="custom-control-label" for="isInternship"></label>
                                </div>
                            </div>
                        </div>

                        <hr>
{{--                        <div class="mb-2">--}}
{{--                            <label class="form-label">Search radius</label>--}}
{{--                            <div id="radBox" class="card card-bordered pt-2 pl-3 pr-2" data-toggle="modal"--}}
{{--                                 data-target="#searchRadiusModal"--}}
{{--                                 style="height: 46px;border-radius: 4px;display: flex;flex-direction: row">--}}
{{--                                <div class="text-muted" style="flex: 1" id="searchRadiusTrigger">Eg. 11km from me</div>--}}
{{--                                <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                        <div class="mb-2">
                            <label class="form-label">Categories</label>
                            <div class="card card-bordered pt-2 pl-3 pr-2" id="catBox"
                                 style="height: 46px;border-radius: 4px;display: flex;flex-direction: row"
                                 data-toggle="modal" data-target="#skillsModal">
                                <div class="text-muted" style="flex: 1" id="selectedSkillsBox">Eg. Barber, Fashion
                                    Designer
                                </div>
                                <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>
                            </div>
                        </div>
{{--                        <div class="mb-2">--}}
{{--                            <label class="form-label">Budget range</label>--}}
{{--                            <div class="card card-bordered pt-2 pl-3 pr-2" id="bugBox"--}}
{{--                                 style="height: 46px;border-radius: 4px;display: flex;flex-direction: row" data-toggle="modal" data-target="#budgetModal">--}}
{{--                                <div class="text-muted" style="flex: 1" id="budgetRange">Eg. Between GHS240 and GHS490</div>--}}
{{--                                <div><em class="icon ni ni-chevron-down" style="font-size: 22px;"></em></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-8" style="margin-top: 50px;">
            <div class="text-dark mb-3" style="display: flex; flex-direction: row;">
                <div style="flex:1" id="fixedTermOpportunitiesCountShimmerLoader"></div>
                <div style="flex:1" id="fixedTermOpportunitiesCount"></div>
                <div style="display:flex; gap: 20px;border-bottom: 1px solid #dbdfea;">
                    <span class="text-primary" style="border-bottom: 3px solid #353299;"><b>All</b></span>
{{--                    <a href="#" class="text-dark">Saved</a>--}}
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

    <div class="modal fade zoom" tabindex="-1" id="shareOpportunity" style="border-radius: 4px;">
        <div class="modal-dialog" role="document" style="border-radius: 4px;">
            <div class="modal-content" style="border-radius: 4px;">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross text-danger"></em>
                </a>
                <div class="modal-header" style="border-bottom: none !important;">
                    <div class="modal-title" style="font-size: 18px;"><b>Share Opportunity</b></div>
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

    <div class="modal fade zoom" tabindex="-1" id="skillsModal" style="border-radius: 16px;">
        <div class="modal-dialog" role="document" style="border-radius: 16px;">
            <div class="modal-content" style="border-radius: 16px;">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header" style="border-bottom: none !important;">
                    <h4 class="modal-title"><b>Skills</b></h4>
                </div>
                <div class="modal-body">
                    <hr style=" margin-top: -25px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="categoriesListing"
                                 style="display: flex; flex-direction: column;height: 400px; overflow-y: scroll;gap: 5px;"></div>
                            <div class="btn btn-primary btn-lg bold" style="float: right !important;"
                                 onclick="applyCategoriesFilter()"> Apply filter
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade zoom" tabindex="-1" id="searchRadiusModal" style="border-radius: 16px;">
        <div class="modal-dialog" role="document" style="border-radius: 16px;">
            <div class="modal-content" style="border-radius: 16px;">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header" style="border-bottom: none !important;">
                    <h4 class="modal-title"><b>Search Radius</b></h4>
                </div>
                <div class="modal-body">
                    <hr style=" margin-top: -25px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <p><em class="icon ni ni-bulb"></em> Open up job search radius by updating the search
                                    radius input below. The bigger the value, the wider the search radius.</p>
                            </div>
                            <div class="input-group1 mb-3 mt-3">
                                <label for="radiusInput">Search Radius (km)</label>
                                <input type="number" min="2" value="10" max="100" class="form-control"
                                       placeholder="Number between 1 and 100" name="radiusInput" id="radiusInput">
                            </div>
                            <div class="btn btn-primary btn-lg bold" style="float: right !important;"
                                 onclick="getFixedTermOpportunitiesBySearchRadius()"> Apply filter
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade zoom" tabindex="-1" id="budgetModal" style="border-radius: 16px;">
        <div class="modal-dialog" role="document" style="border-radius: 16px;">
            <div class="modal-content" style="border-radius: 16px;">
                <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                    <em class="icon ni ni-cross"></em>
                </a>
                <div class="modal-header" style="border-bottom: none !important;">
                    <h4 class="modal-title"><b>Budget Range</b></h4>
                </div>
                <div class="modal-body">
                    <hr style=" margin-top: -25px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div>
                                <p><em class="icon ni ni-bulb"></em> Filter job opportunities based on the budget range</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group1 mb-3 mt-3">
                                        <label for="radiusInput">Min budget (GHS)</label>
                                        <input type="number" min="2" value="10" max="100" class="form-control"
                                               placeholder="Min budget" name="radiusInput" id="radiusInput">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group1 mb-3 mt-3">
                                        <label for="radiusInput">Max budget (GHS)</label>
                                        <input type="number" min="2" value="10" max="100" class="form-control"
                                               placeholder="Max budget" name="radiusInput" id="radiusInput">
                                    </div>
                                </div>
                            </div>
                            <div class="btn btn-primary btn-lg bold" style="float: right !important;"
                                 onclick="getFixedTermOpportunitiesBySearchRadius()"> Apply filter
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        localStorage.removeItem("filteredFixedTermPosts")

        /**
         * setup fixed term opportunities
         * shimmer loader
         */
        const isLoadingFixedTermOpportunities = (isLoading) => {
            const fixedTermOpportunitiesShimmerLoader = `<div class="text-center" >Loading...</div>`
            const fixedTermOpportunitiesCountShimmerLoader = `<div class="s-fixed-term-card" style="width: 130px !important;"><div class="date-line" style="width: 130px !important;"></div></div>`

            if (isLoading) {
                $("#fixedTermOpportunitiesCountShimmerLoader").html(fixedTermOpportunitiesCountShimmerLoader)
                $("#fixedTermOpportunitiesListingLoader").html(fixedTermOpportunitiesShimmerLoader)
                $("#fixedTermOpportunitiesCountShimmerLoader").show()
                $("#fixedTermOpportunitiesListingLoader").show()
                $("#fixedTermOpportunitiesListing").html("")
                $("#fixedTermOpportunitiesCount").html("")
            } else {
                $("#fixedTermOpportunitiesCountShimmerLoader").hide()
                $("#fixedTermOpportunitiesListingLoader").hide("slow")
            }
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
            console.log(categories)


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

                    <div style="display: flex; flex-direction: row; padding: 10px;border: 1px solid #dbdfea;border-radius: 4px;" class="bg-secondary-dim mb-2">
                        <div style="display: flex; flex-direction: row; gap: 10px;flex: 1;">
<!--                           <div style="border: 1px solid #364a63; padding: 5px; height: 80px; width: 80px !important;border-radius: 4px;">Image</div>-->
                           <div style="display: flex; justify-content: center;flex-direction: column;">
                              <div style="font-size: 18px;color: #1c2b46 !important"><b>${post.title}</b></div>
                              <div class="text-muted"><small><em class="icon ni ni-clock"></em> ${post.createdOn}</small></div>
                           </div>
                        </div>
<!--                        <div>Save</div>-->
                    </div>

                        <div class="user-toggle">
                                                    <div class="user-avatar bg-secondary-dim sm">
                                                        <em class="icon ni ni-building"></em>
                                                    </div>
                                                    <div class="user-info" style="">
                                                        <div class="nk-menu-text " style="color: red"> <small class="text-muted">Company</small>  <br>
                         ${post.employer}</div>
                            </div>
                        </div>




                    <div style="display: flex; flex-direction: row;gap: 10px; justify-content: space-between ">
                        <div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-coins"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text-green-600" style="color: green"> <small class="text-muted">Budget (GHS/month)</small>  <br>
 ${post.min_budget}GHS - ${post.max_budget}GHS</div>
                            </div>
                        </div>
                        <div class="flex flex-column border round-sm text-center bg-secondary-dim justify-center items-center" style="width: 100px;height: 100px;">
                            <div><small>Duration</small></div>
                            <div style="font-size: 28px;"><b>${post.duration}</b></div>
                            <div><small><b>months</b></small></div>
                        </div>
                    </div>


                    <div><div class="user-toggle">
                            <div class="user-avatar bg-secondary-dim sm">
                                <em class="icon ni ni-map-pin"></em>
                            </div>
                            <div class="user-info" style="">
                                <div class="nk-menu-text text"><small class="text-muted">Location</small>  <br> ${post.location} (${post.distance}km)</div>
                            </div>
                        </div>
                    </div>




                <div class="flex flex-row justify-between bg-secondary-dim mt-2" style="margin: 0px;border: 1px solid #dbdfea;border-radius: 4px;padding: 10px;">
                    <div class=" " style="margin-top: 5px;">
                        <a type="button" style="float: left;justify-content: center;align-items: center"  onclick="setupShareableLink('${post.type}', '${post.id}')" data-toggle="modal" data-target="#shareOpportunity" href="javascript:void(0)" >
                                <em class="icon ni ni-link" style="font-size: 28px;" data-toggle="tooltip" data-bs-placement="right" title="Share with family and friends"></em>
                        </a>
                    </div>
                    <div class="" ><a data-bs-toggle="tooltip" data-bs-placement="right" title="See more details" href="${route}" class="btn btn-outline-primary " style="height: 40px;margin-top: 0px;float: right !important;">View Details</a></div>
                    </div>
                </div>

<!--                </div>-->
<!--                </a>-->
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
                    const data = _data
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

        /**
         * get categories
         */
        const getCategories = () => {
            $.ajax({
                url: "{{env("BACKEND_URL")}}/getCategories",
                method: "GET",
                dataType: "json",
                contentType: "application/json",
                data: {},
                crossDomain: true,
                success: function (_data) {
                    const data = _data.data
                    let categories = ``;
                    data.map(category => {
                        categories += ` <div class="custom-control custom-checkbox mt-1">
                            <input type="checkbox" class="custom-control-input" value="${category.name}" name="categories" id="${category.name}">
                            <label class="custom-control-label bold" for="${category.name}">${category.name}</label>
                        </div>`
                    })
                    $("#categoriesListing").html(categories)
                },
                error: function (e) {
                    $("#categoriesListing").html(ComponentFixedTermOpportunitiesFetchError())
                    isLoadingFixedTermOpportunities(false)
                }
            })
        }
        getCategories()

        const getFixedTermOpportunitiesBySearchRadius = () => {
            isLoadingFixedTermOpportunities(true)
            const radiusInput = document.getElementById("radiusInput")
            let radBox = document.getElementById("radBox")
            const radius = radiusInput.value
            $("#searchRadiusModal").modal('hide');
            /**
             * make api call
             */
            $.ajax({
                url: `{{env("BACKEND_URL")}}/getFixedTermOpportunitiesBySearchRadius/${radius}`,
                method: "GET",
                dataType: "json",
                contentType: "application/json",
                data: {},
                crossDomain: true,
                success: function (_data) {
                    const data = _data.data
                    localStorage.setItem("fixedTermPosts", JSON.stringify(data.opportunities))

                    $("#searchRadiusTrigger").html(`<span class="text-dark"><b>${radius}km from you</b></span>`)
                    radBox.classList.add("borderActive")

                    $("#fixedTermOpportunitiesListing").html("")
                    let fixedTermOpportunitiesList = `<div class="row">`
                    let counter = 0;
                    $.each(data.opportunities, function (key, opportunity) {
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

    </script>
    <script src="{{asset('public/js/work/fixed-term-jobs/index.js')}}"></script>
@endsection
