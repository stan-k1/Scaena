<script>
    function Query_Analytics(startDateStr, endDateStr, initQueryBool) {
        console.log("Analytics: Query_QuickGlance has been included.")

        var mydata;
        // Replace with your view ID.
        var VIEW_ID = '222595492';

        //Data Retrieval Variables
        var vidCompletions;
        var views;
        var progress25;
        var progress50;


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
                                {expression: 'ga:visitors'},
                                {expression: 'ga:avgTimeOnPage'},
                                {expression: 'ga:avgSessionDuration'},
                                {expression: 'ga:exitRate'}
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
            var sessions = original.reports[0].data.totals[0].values[1];
            var visitors = original.reports[0].data.totals[0].values[2];
            var timeonpage = original.reports[0].data.totals[0].values[3];
            console.log('tp' + timeonpage);
            timeonpage = parseInt(timeonpage / 60) //Converts the initial seconds time to min
            var seduration = original.reports[0].data.totals[0].values[4];
            seduration = parseInt(seduration / 60);
            var exitrate = original.reports[0].data.totals[0].values[5];

            document.getElementById('q4sessions').innerHTML = sessions;
            document.getElementById('q4pagetime').innerHTML = parseInt(timeonpage) + ' Min';
            document.getElementById('q4setime').innerHTML = parseInt(seduration) + ' Min';
            timeseChart.data.datasets[0].data = [timeonpage, seduration, sessions];
            timeseChart.update();

            var nonexitrate = 100 - exitrate;
            exitrate = parseFloat(exitrate).toFixed(2);
            nonexitrate = parseFloat(nonexitrate).toFixed(2);
            document.getElementById('q6exitrate').innerHTML = exitrate + '%';
            document.getElementById('q6nonexitrate').innerHTML = nonexitrate + '%';
            exitChart.data.datasets[0].data = [exitrate, nonexitrate];
            exitChart.update();

            if (initQueryBool) {
                // document.getElementById('query-output').value = formattedJson;
                document.getElementById('views').innerHTML = views;
                document.getElementById('sessions').innerHTML = sessions;
                document.getElementById('visitors').innerHTML = visitors;
            }

            //q4 Suggestions Logic
            if (seduration / timeonpage < 1) {
                document.getElementById("q4suggestions").innerHTML = "Time spent on this page is larger than the average session duration for the site. This suggest that students spend a particularly large amount of time on this page.";
            } else {
                document.getElementById("q4suggestions").innerHTML = "Time spent on this page is equal or less than the average session duration for the site. This suggest that students spend an expected amount of time on this page."
            }

            //q6 Suggestions Logic
            if (exitrate > nonexitrate) {
                document.getElementById("q6suggestions").innerHTML = "Most students abandon the site after visiting this page. This might indicate that the content is taxing. Consider offering additional clarifications or splitting content into multiple videos of shorter duration.";
            } else {
                document.getElementById("q6suggestions").innerHTML = "Most students continue browsing this site after visiting this page. This indicates that the content duration is ideal and that the content is not overly taxing. Consider clarifying whether students require additional clarifications on the subjects discussed."
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
            // document.getElementById('query-output').value = formattedJson;
            var x = original.reports[0].data.totals[0].values[0];
            if (initQueryBool) {
                document.getElementById('completions').innerHTML = x;
            }
            document.getElementById('q1completions').innerHTML = x;
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
            // document.getElementById('query-output').value = formattedJson;

            plays = original.reports[0].data.totals[0].values[0];
            if (initQueryBool) {
                document.getElementById('plays').innerHTML = plays;
            }
            document.getElementById('q1partials').innerHTML = plays;
            document.getElementById('q3plays').innerHTML = plays;

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
            // document.getElementById('query-output').value = formattedJson;

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
            // document.getElementById('query-output').value = formattedJson;

            var pauses = original.reports[0].data.totals[0].values[0];
            document.getElementById('q3pauses').innerHTML = pauses;

            document.getElementById('q3views').innerHTML = views;

            ppvChart.data.datasets[0].data = [plays, pauses, views];
            ppvChart.update();

            //q2 Suggestions Logic
            if (pauses / plays >= 4) {
                document.getElementById("q3suggestions").innerHTML = "Students seem to pause multiple times within this video. Consider offering more detailed explanations or providing additional related material .";
            } else {
                document.getElementById("q3suggestions").innerHTML = "Students do not seem to pause particularly frequently on this video. This suggests that the material is clear and easy to comprehend."
            }
            queryReports6();
        }

        // Query the API and print the results to the page.
        function queryReports6() {
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
                                        "expressions": ["25"]
                                    }]
                                }]
                        }]
                }
            }).then(displayResults6, console.error.bind(console));
        }

        function displayResults6(response) {
            var formattedJson = JSON.stringify(response.result, null, 2);
            var original = response.result;
            document.getElementById('query-output').value = formattedJson;

            progress25 = original.reports[0].data.totals[0].values[0];
            document.getElementById('q5progress25').innerHTML = progress25;
            queryReports7();
        }

        function queryReports7() {
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
                                        "expressions": ["25"]
                                    }]
                                }]
                        }]
                }
            }).then(displayResults7, console.error.bind(console));
        }

        function displayResults7(response) {
            var formattedJson = JSON.stringify(response.result, null, 2);
            var original = response.result;
            document.getElementById('query-output').value = formattedJson;

            progress50 = original.reports[0].data.totals[0].values[0];
            document.getElementById('q5progress50').innerHTML = progress50;
            queryReports8();
        }

        function queryReports8() {
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
                                        "expressions": ["75"]
                                    }]
                                }]
                        }]
                }
            }).then(displayResults8, console.error.bind(console));
        }

        function displayResults8(response) {
            var formattedJson = JSON.stringify(response.result, null, 2);
            var original = response.result;
            document.getElementById('query-output').value = formattedJson;

            var progress75 = original.reports[0].data.totals[0].values[0];
            document.getElementById('q5progress75').innerHTML = progress75;
            document.getElementById('q5plays').innerHTML = plays;
            document.getElementById('q5completions').innerHTML = vidCompletions;

            progChart.data.datasets[0].data = [plays, progress25, progress50, progress75, vidCompletions];
            progChart.update();

            //q5 Suggestions Logic
            if (progress50 >= vidCompletions * 2) {
                document.getElementById('q5suggestions').innerHTML = "Many students that watch this video do not complete it. Consider making shorter and more concise content, or spreading complex subjects across multiple, shorter videos.";
            } else if (progress25 >= vidCompletions * 2) {
                document.getElementById('q5suggestions').innerHTML = "A substantial number of students that starts watching this video does not continue it. Consider creating a thought-provoking introduction to increase student engagement.A substantial number of students that starts watching this video does not continue it. Consider creating a thought-provoking introduction to increase student engagement.";
            } else if (vidCompletions > plays * 0.5) {
                document.getElementById('q5suggestions').innerHTML = "Most students seem to maintain engagement in watching this video. Consider making more content of a similar format, length, or content.";
            } else {
                document.getElementById('q5suggestions').innerHTML = "Student engagement with this video does not seem consistent. Consider making improvements to the length or content to further increase engagement.";
            }

            //q5- Additional SUggetsions
            if (progress50 == progress25) { //CHNAGE THIS BACK
                $('#q5suggestions').append("<br> In addition, a large number of students skip the introduction of this video. Consider making shorter or more concise intros to retain student interest.");
            } else if (progress50 > vidCompletions) {
                $('#q5suggestions').append("<br> In addition, many students watch this video to the half point mark but do not complete it. Consider decreasing content length to maintain student interest.");
            } else if (progress25 > progress50 && progress75 > progress50) {
                $('#q5suggestions').append("<br> In addition, many students that watch this video, skip the middle portion, consider making more condense or engaging content to retain student engagement throughout the duration of the video.");
            }

        }
    }


</script>