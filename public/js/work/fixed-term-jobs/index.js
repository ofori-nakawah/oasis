

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
                let _opportunities = (localStorage.getItem("filteredFixedTermPosts")) ? JSON.parse(localStorage.getItem("filteredFixedTermPosts")) : JSON.parse(localStorage.getItem("fixedTermPosts"))
                const keys = Object.keys(_opportunities);
                let opportunities = []
                keys.forEach(opportunity => {
                    opportunities.push(_opportunities[opportunity])
                })

                const searchResults = opportunities.filter(opportunity => `${opportunity.title} ${opportunity.employer}`.toLowerCase().includes(query.toLowerCase()))
                isLoadingFixedTermOpportunities(true)
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
