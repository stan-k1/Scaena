<script>
    function Query_Analytics(startDateStr, endDateStr, initQueryBool)
        {
            console.log("Analytics: Query_QuickGlance has been included.")

            var mydata;
            // Replace with your view ID.
            var VIEW_ID = '222595492';

            //Data Retrieval Variables
            var vidCompletions;
            var views;

            //Metrics used in multiple display functons (function globals)
            var plays;

            queryReports_Analytics();

            // Query the API and print the results to the page.
            function queryReports_Analytics() {
                mydata = gapi.client.request({
                    path: '/v4/reports:batchGet',
                    root: 'https://analyticsreporting.googleapis.com/',
                    method: 'POST',
                    body: {
                        reportRequests: [
                            {
                                viewId: VIEW_ID,
                                dateRanges: [{startDate: startDateStr, endDate: endDateStr}],
                                metrics: [
                                    {expression: 'ga:pageviews'},
                                    {expression: 'ga:sessions'},
                                    {expression: 'ga:visitors'}
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
                }).then(displayResults, console.error.bind(console));
            }

            function displayResults(response) {
                var formattedJson = JSON.stringify(response.result, null, 2);
                var original = response.result;
                views = original.reports[0].data.totals[0].values[0];
                if (initQueryBool) {
                    document.getElementById('query-output').value = formattedJson;
                    document.getElementById('views').innerHTML=views;
                    var sessions = original.reports[0].data.totals[0].values[1];
                    document.getElementById('sessions').innerHTML=sessions;
                    var visitors = original.reports[0].data.totals[0].values[2];
                    document.getElementById('visitors').innerHTML=visitors;
                }
                queryReports2()
            }

            // Query the API and print the results to the page.
            function queryReports2() {
                mydata = gapi.client.request({
                    path: '/v4/reports:batchGet',
                    root: 'https://analyticsreporting.googleapis.com/',
                    method: 'POST',
                    body: {
                        reportRequests: [
                            {
                                viewId: VIEW_ID,
                                dateRanges: [{startDate: startDateStr, endDate: endDateStr}],
                                metrics: [
                                    {expression: 'ga:totalEvents'}],
                                "dimensionFilterClauses": [
                                    {
                                        "filters": [{
                                            "dimension_name": "ga:eventAction",
                                            "operator": "PARTIAL",
                                            "expressions": ["100"]
                                        }]
                                    },
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
                                    }]
                            }]
                    }
                }).then(displayResults2, console.error.bind(console));
            }

            function displayResults2(response) {
                var formattedJson = JSON.stringify(response.result, null, 2);
                var original = response.result;
                document.getElementById('query-output').value = formattedJson;
                var x = original.reports[0].data.totals[0].values[0];
                if (initQueryBool){document.getElementById('completions').innerHTML=x;}
                document.getElementById('q1completions').innerHTML=x;
                vidCompletions = x;
                queryReports3()
            }

            // Query the API and print the results to the page.
            function queryReports3() {
                mydata = gapi.client.request({
                    path: '/v4/reports:batchGet',
                    root: 'https://analyticsreporting.googleapis.com/',
                    method: 'POST',
                    body: {
                        reportRequests: [
                            {
                                viewId: VIEW_ID,
                                dateRanges: [{startDate: startDateStr, endDate: endDateStr}],
                                metrics: [
                                    {expression: 'ga:totalEvents'}],
                                "dimensionFilterClauses": [{
                                    "filters": [
                                        {
                                            "operator": "PARTIAL",
                                            "dimensionName": "ga:pagePath",
                                            "expressions": [
                                                "/Scaena/Scaena.php"
                                            ]
                                        }
                                    ]
                                },
                                    {
                                        "filters": [{
                                            "dimension_name": "ga:eventAction",
                                            "operator": "PARTIAL",
                                            "expressions": ["Played"]
                                        }]
                                    }]
                            }]
                    }
                }).then(displayResults3, console.error.bind(console));
            }

            function displayResults3(response) {
                var formattedJson = JSON.stringify(response.result, null, 2);
                var original = response.result;
                document.getElementById('query-output').value = formattedJson;

                plays = original.reports[0].data.totals[0].values[0];
                if (initQueryBool) {
                    document.getElementById('plays').innerHTML=plays;
                }
                document.getElementById('q1partials').innerHTML=plays;
                document.getElementById('q3plays').innerHTML=plays;

                <!--Prevents no login <p> message from showing if data has been retrieved-->
                document.getElementById('query_check').innerText = "Logged in and data printed";
                <!--Hides the <p> if data is retrieved after the message has been displayed-->
                $('#no_login_message').hide();

                var VidCompletionsPercentage = vidCompletions * (100 / plays).toFixed(2);
                VidCompletionsPercentage = Math.round(VidCompletionsPercentage * 100) / 100;
                var VidCompletionsPercentageReminder = 100 - VidCompletionsPercentage;
                VidCompletionsPercentageReminder = Math.round(VidCompletionsPercentageReminder * 100) / 100;
                myChart.data.datasets[0].data = [VidCompletionsPercentage, VidCompletionsPercentageReminder];
                myChart.update();
                //q1 Suggestions Logic
                if (VidCompletionsPercentage >= VidCompletionsPercentageReminder) {
                    document.getElementById("q1suggestions").innerHTML = "Most students watch this video to completion. This suggests that the content and length are appropriate. Consider making more videos on similar topics.";
                } else {
                    document.getElementById("q1suggestions").innerHTML = "Most students do not finish watching this video. Consider making future videos shorter or otherwise attempting to increase engagement."
                }
                queryReports4();
            }

            // Query the API and print the results to the page.
            function queryReports4() {
                mydata = gapi.client.request({
                    path: '/v4/reports:batchGet',
                    root: 'https://analyticsreporting.googleapis.com/',
                    method: 'POST',
                    body: {
                        reportRequests: [
                            {
                                viewId: VIEW_ID,
                                dateRanges: [{startDate: startDateStr, endDate: endDateStr}],
                                metrics: [
                                    {expression: 'ga:users'}],
                                dimensions: [{"name": "ga:deviceCategory"}],
                                "dimensionFilterClauses": [{
                                    "filters": [
                                        {
                                            "operator": "PARTIAL",
                                            "dimensionName": "ga:pagePath",
                                            "expressions": [
                                                "/Scaena/Scaena.php"
                                            ]
                                        }
                                    ]
                                }]
                            }]
                    }
                }).then(displayResultsDevices, console.error.bind(console));
            }

            function displayResultsDevices(response) {
                var formattedJson = JSON.stringify(response.result, null, 2);
                var original = response.result;
                document.getElementById('query-output').value = formattedJson;

                var x = original.reports[0].data.totals[0].values[0];
                if (x === undefined) {
                    x = 0;
                }
                document.getElementById('q2desktops').innerHTML = x;
                var desktops = x;

                var x = original.reports[0].data.totals[0].values[1];
                if (x === undefined) {
                    x = 0;
                }
                document.getElementById('q2tablets').innerHTML = x;
                var tablets = x;

                var x = original.reports[0].data.totals[0].values[2];
                if (x === undefined) {
                    x = 0;
                }
                document.getElementById('q2phones').innerHTML = x;
                var phones = x;

                deviceChart.data.datasets[0].data = [desktops, tablets, phones];
                deviceChart.update();

                //q2 Suggestions Logic
                if (desktops + tablets * 0.5 > phones + tablets * 0.5) {
                    document.getElementById("q2suggestions").innerHTML = "This video is watched more frequently on stationary devices. This enables the consumption of longer or more detailed content.";
                } else {
                    document.getElementById("q2suggestions").innerHTML = "This video is watched more frequently in a mobile context. It may be beneficial to maintain a shorter duration and focus on key points."
                }

                console.log("DisplayResultsDevices has been executed.")
                queryReports5()

            }

            // Query the API and print the results to the page.
            function queryReports5() {
                mydata = gapi.client.request({
                    path: '/v4/reports:batchGet',
                    root: 'https://analyticsreporting.googleapis.com/',
                    method: 'POST',
                    body: {
                        reportRequests: [
                            {
                                viewId: VIEW_ID,
                                dateRanges: [{startDate: startDateStr, endDate: endDateStr}],
                                metrics: [
                                    {expression: 'ga:totalEvents'}],
                                "dimensionFilterClauses": [{
                                    "filters": [
                                        {
                                            "operator": "PARTIAL",
                                            "dimensionName": "ga:pagePath",
                                            "expressions": [
                                                "/Scaena/Scaena.php"
                                            ]
                                        }
                                    ]
                                },
                                    {
                                        "filters": [{
                                            "dimension_name": "ga:eventAction",
                                            "operator": "PARTIAL",
                                            "expressions": ["Paused"]
                                        }]
                                    }]
                            }]
                    }
                }).then(displayResults5, console.error.bind(console));
            }

            function displayResults5(response) {
                var formattedJson = JSON.stringify(response.result, null, 2);
                var original = response.result;
                document.getElementById('query-output').value = formattedJson;

                var pauses = original.reports[0].data.totals[0].values[0];
                document.getElementById('q3pauses').innerHTML = pauses;

                document.getElementById('q3views').innerHTML = views;

                ppvChart.data.datasets[0].data = [plays, pauses, views];
                ppvChart.update();

                //q2 Suggestions Logic
                if (pauses/plays>=4) {
                    document.getElementById("q3suggestions").innerHTML = "Viewers seem to pause multiple times within this video. Consider offering more detailed explanations or providing additional related material .";
                } else {
                    document.getElementById("q3suggestions").innerHTML = "Viewers do not seem to pause particularly frequently on this video. This suggests that the material is clear and easy to comprehend."
                }
            }








        }


</script>