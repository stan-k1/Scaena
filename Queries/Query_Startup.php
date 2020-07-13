<script>
    //Dummy Querry, MUST run on page load to estabilish connection with the Analytics Reporting API BEFORE performing any other queries.
    {
        console.log("Analytics: Query_QuickGlance has been included.")

        var mydata;
        // Replace with your view ID.
        var VIEW_ID = '222595492';

        //Data Retrieval Variables
        var vidCompletions;

        // Query the API and print the results to the page.
        function queryReports() {
            mydata = gapi.client.request({
                path: '/v4/reports:batchGet',
                root: 'https://analyticsreporting.googleapis.com/',
                method: 'POST'
            }).then(displayResults_startup, console.error.bind(console));
        }

        function displayResults_startup(response) {
            var formattedJson = JSON.stringify(response.result, null, 2);
            var original = response.result;
            document.getElementById('query-output').value = formattedJson;
            Query_Analytics('1daysAgo', 'today',true);
        }
    }
</script>