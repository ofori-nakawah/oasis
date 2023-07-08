

const getFixedTermInternshipOpportunities = () => {
    /**
     * check if the checkbox is checked or not
     */
    isLoadingFixedTermOpportunities(true)
    let isInternship = document.getElementById("isInternship");
    if (isInternship.checked) {
        let opportunities = (localStorage.getItem("filteredFixedTermPosts")) ? JSON.parse(localStorage.getItem("filteredFixedTermPosts")) : JSON.parse(localStorage.getItem("fixedTermPosts"))
        const keys = Object.keys(opportunities);
        let isInternshipOpportunities = []
        keys.forEach(opportunity => {
            if (opportunities[opportunity].is_internship === "yes") {
                isInternshipOpportunities.push(opportunities[opportunity])
            }
        })

        /**
         * show replace info view
         */
        $("#fixedTermOpportunitiesListing").html("")
        let fixedTermOpportunitiesList = `<div class="row">`
        let counter = 0;
        $.each(isInternshipOpportunities, function (key, opportunity) {
            fixedTermOpportunitiesList += ComponentFixedTermOpportunity(opportunity)
            counter++;
        })
        $("#fixedTermOpportunitiesCount").html(`<p>${counter} jobs listed</p>`)
        $("#fixedTermOpportunitiesListing").html(fixedTermOpportunitiesList)
        fixedTermOpportunitiesList += `</div>`
        $(".savedFixedTermOpportunityIcon").hide()
        $(".saveFixedTermOpportunityLoader").hide()
        isLoadingFixedTermOpportunities(false)
    } else {
        /**
         * remove is internship from query
         */
        let opportunities = (localStorage.getItem("filteredFixedTermPosts")) ? JSON.parse(localStorage.getItem("filteredFixedTermPosts")) : JSON.parse(localStorage.getItem("fixedTermPosts"))
        const keys = Object.keys(opportunities);
        let allOpportunities = []
        keys.forEach(opportunity => {
            allOpportunities.push(opportunities[opportunity])
        })

        $("#fixedTermOpportunitiesListing").html("")
        let fixedTermOpportunitiesList = `<div class="row">`
        let counter = 0;
        $.each(allOpportunities, function (key, opportunity) {
            fixedTermOpportunitiesList += ComponentFixedTermOpportunity(opportunity)
            counter++;
        })
        $("#fixedTermOpportunitiesCount").html(`<p>${counter} jobs listed</p>`)
        $("#fixedTermOpportunitiesListing").html(fixedTermOpportunitiesList)
        fixedTermOpportunitiesList += `</div>`
        $(".savedFixedTermOpportunityIcon").hide()
        $(".saveFixedTermOpportunityLoader").hide()
        isLoadingFixedTermOpportunities(false)
    }
}

const searchFixedJobOpportunities = () => {
    const searchInput = document.getElementById("searchFixedTermJobOpportunities")
    searchInput.onkeyup = (e) => {
        if(e.keyCode === 13){
            const query = searchInput.value
            if (query.trim() !== "") {
                isLoadingFixedTermOpportunities(true)

                let _opportunities = (localStorage.getItem("filteredFixedTermPosts")) ? JSON.parse(localStorage.getItem("filteredFixedTermPosts")) : JSON.parse(localStorage.getItem("fixedTermPosts"))
                const keys = Object.keys(_opportunities);
                let opportunities = []
                keys.forEach(opportunity => {
                    opportunities.push(_opportunities[opportunity])
                })

                const searchResults = opportunities.filter(opportunity => `${opportunity.title} ${opportunity.employer}`.toLowerCase().includes(query.toLowerCase()))
                $("#fixedTermOpportunitiesListing").html("")
                let fixedTermOpportunitiesList = `<div class="row">`
                let counter = 0;
                $.each(searchResults, function (key, opportunity) {
                    fixedTermOpportunitiesList += ComponentFixedTermOpportunity(opportunity)
                    counter++;
                })
                $("#fixedTermOpportunitiesCount").html(`<p>${counter} jobs listed</p>`)
                $("#fixedTermOpportunitiesListing").html(fixedTermOpportunitiesList)
                fixedTermOpportunitiesList += `</div>`
                $(".savedFixedTermOpportunityIcon").hide()
                $(".saveFixedTermOpportunityLoader").hide()
                isLoadingFixedTermOpportunities(false)
            }
        }
    }
}

searchFixedJobOpportunities()


const applyCategoriesFilter = () => {
    $("#skillsModal").modal('hide');
    /**
     * get selected categories
     */
    let selectedCategories = []
    let checkboxes = document.querySelectorAll('input[name="categories"]:checked')
    let catBox = document.getElementById("catBox")

    for (let i = 0; i < checkboxes.length; i++) {
        selectedCategories.push(checkboxes[i].value)
    }

    let selectSkillsBoxText = `Eg. Barber, Fashion Designer`;

    if (selectedCategories.length === 1) {
        selectSkillsBoxText = `<span class="text-dark"><b>${selectedCategories.join("")}</b></span>`
        catBox.classList.add("borderActive")
    }

    if (selectedCategories.length > 1) {
        selectSkillsBoxText = `<span class="text-dark"><b>Selected <em class="icon ni ni-circle-fill" style="font-size: 10px;"></em> ${selectedCategories.length}</b></span>`
        catBox.classList.add("borderActive")
    }

    document.getElementById("selectedSkillsBox").innerHTML = selectSkillsBoxText

    if (selectedCategories.length > 0) {
        isLoadingFixedTermOpportunities(true)

        const query = selectedCategories.join(" ")
        let _opportunities = JSON.parse(localStorage.getItem("fixedTermPosts"))
        const keys = Object.keys(_opportunities);
        let opportunities = []
        keys.forEach(opportunity => {
            opportunities.push(_opportunities[opportunity])
        })

        let filterResults = []
        opportunities.forEach(opportunity => {
            const tags = JSON.parse(opportunity.tags)
            tags.forEach(tag => {
                if (query.includes(tag)) {
                    filterResults.push(opportunity)
                }
            })
        })

        let filteredResults = [...new Set(filterResults)]

        localStorage.setItem("filteredFixedTermPosts", JSON.stringify(filteredResults))
        $("#fixedTermOpportunitiesListing").html("")
        let fixedTermOpportunitiesList = `<div class="row">`
        let counter = 0;
        $.each(filteredResults, function (key, opportunity) {
            fixedTermOpportunitiesList += ComponentFixedTermOpportunity(opportunity)
            counter++;
        })
        $("#fixedTermOpportunitiesCount").html(`<p>${counter} jobs listed</p>`)
        $("#fixedTermOpportunitiesListing").html(fixedTermOpportunitiesList)
        fixedTermOpportunitiesList += `</div>`
        $(".savedFixedTermOpportunityIcon").hide()
        $(".saveFixedTermOpportunityLoader").hide()
        isLoadingFixedTermOpportunities(false)
    }

    /**
     * do the filtering
     * save filtered posts in filter local storage
     */
}


