<script>
    //Initializes API connection. MUST run on page load to estabilish connection with the Analytics Reporting API BEFORE performing any other queries.
    //Returns to retrieve and display historical data.
    {
        console.log("Analytics: Query_QuickGlance has been included.")

        var mydata;
        // Replace with your view ID.
        var VIEW_ID = '222595492';

        //Global Vars For Historical Data
        var m1views;
        var m1vis;
        var m1time;

        var m2views;
        var m2vis;
        var m2time;

        var m3views;
        var m3vis;
        var m3time;

        // Query the API and print the results to the page.
        function queryReports() {
            mydata = gapi.client.request({
                path: '/v4/reports:batchGet',
                root: 'https://analyticsreporting.googleapis.com/',
                method: 'POST',
                body: {
                    reportRequests: [
                        {
                            viewId: VIEW_ID,
                            dateRanges: [{startDate: '30daysAgo', endDate: 'today'}],
                            metrics: [
                                {expression: 'ga:pageviews'},
                                {expression: 'ga:timeOnPage'},
                            ],
                            "dimensionFilterClauses": [
                                {
                                    "filters": [
                                        {
                                            "operator": "PARTIAL",
                                            "dimensionName": "ga:pagePath",
                                            "expressions": [
                                                "/Scaena/Scaena.php"
                                            ]
                                        }
                                    ]
                                }
                            ],
                        }

                    ]
                }
            }).then(displayResults_startup, console.error.bind(console));
        }

        function displayResults_startup(response) {
            Query_Analytics('30daysAgo', 'today',true);
            var formattedJson = JSON.stringify(response.result, null, 2);
            var original = response.result;
            m1views=original.reports[0].data.totals[0].values[0];
            m1time=original.reports[0].data.totals[0].values[1];
            m1time=parseInt(m1time/60);
            queryReports_hist();
        }

        function queryReports_hist() {
            mydata = gapi.client.request({
                path: '/v4/reports:batchGet',
                root: 'https://analyticsreporting.googleapis.com/',
                method: 'POST',
                body: {
                    reportRequests: [
                        {
                            viewId: VIEW_ID,
                            dateRanges: [{startDate: '60daysAgo', endDate: '30daysAgo'}],
                            metrics: [
                                {expression: 'ga:pageviews'},
                                {expression: 'ga:timeOnPage'},
                            ],
                            "dimensionFilterClauses": [
                                {
                                    "filters": [
                                        {
                                            "operator": "PARTIAL",
                                            "dimensionName": "ga:pagePath",
                                            "expressions": [
                                                "/Scaena/Scaena.php"
                                            ]
                                        }
                                    ]
                                }
                            ],
                        }

                    ]
                }
            }).then(displayResults_hist, console.error.bind(console));
        }

        function displayResults_hist(response) {
            var formattedJson = JSON.stringify(response.result, null, 2);
            var original = response.result;
            m2views=original.reports[0].data.totals[0].values[0];
            m2time=original.reports[0].data.totals[0].values[1];
            m2time=parseInt(m2time/60);
            histChart.update();
            queryReports_hist2();
        }

        function queryReports_hist2() {
            mydata = gapi.client.request({
                path: '/v4/reports:batchGet',
                root: 'https://analyticsreporting.googleapis.com/',
                method: 'POST',
                body: {
                    reportRequests: [
                        {
                            viewId: VIEW_ID,
                            dateRanges: [{startDate: '90daysAgo', endDate: '60daysAgo'}],
                            metrics: [
                                {expression: 'ga:pageviews'},
                                {expression: 'ga:timeOnPage'},
                            ],
                            "dimensionFilterClauses": [
                                {
                                    "filters": [
                                        {
                                            "operator": "PARTIAL",
                                            "dimensionName": "ga:pagePath",
                                            "expressions": [
                                                "/Scaena/Scaena.php"
                                            ]
                                        }
                                    ]
                                }
                            ],
                        }

                    ]
                }
            }).then(displayResults_hist2, console.error.bind(console));
        }

        function displayResults_hist2(response) {
            console.log("Preparing to display historical data.")
            var formattedJson = JSON.stringify(response.result, null, 2);
            var original = response.result;
            m3views=original.reports[0].data.totals[0].values[0];
            m3time=original.reports[0].data.totals[0].values[1];
            m3time=parseInt(m3time/60);
            histChart.data.datasets[0].data = [m1views, m2views, m3views];
            histChart.data.datasets[1].data = [m1time, m2time, m3time];
            histChart.update();
        }


    }
</script>