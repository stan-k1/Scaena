<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Scaena</title>
    <?php include('Elements\Imports.html') ?>

    <!--Functional Scripts-->
    <script>var currentNavItem = "#navLinkHome"</script>
    <script>
        $(document).ready(function () {
            $("#query-output").hide();
            $("#responseToggle").click(function () {
                $("#query-output").toggle();
            });
        });
    </script>

    <?php include('Elements/SiteTag.html') ?>
    <!--Analytics Reproting API v4-->
    <meta name="google-signin-client_id"
          content="808896063761-46ld67opvg6hmrb08b5n3jpih7fg566n.apps.googleusercontent.com">
    <meta name="google-signin-scope" content="https://www.googleapis.com/auth/analytics.readonly">
</head>

<body>
<!--Main Body-->
<?php include('Elements\Header.html') ?>

<h1 style="text-align: center">Scaena Video Test Page</h1>
<video class="video-js" controls="true" id="video_player"
       poster="https://www.carbonbrief.org/wp-content/uploads/2019/09/Blue-green-sea-surface-background-with-fishes-full-frame-composition-DWGX61-420x280.jpg">
    <source src="//vjs.zencdn.net/v/oceans.mp4" type="video/mp4">
    <source src="//vjs.zencdn.net/v/oceans.webm" type="video/webm">
</video>
<script src="https://vjs.zencdn.net/7.8.3/video.js"></script>

<h1>Analytics</h1>
<h6>Last Week</h6>
<p id="views">Views: Retrieving</p>
<p id="sessions">Sessions: Retrieving</p>

<!-- The Sign-in button. This will run `queryReports()` on success. -->
<p class="g-signin2" data-onsuccess="queryReports"></p>

<button id="responseToggle">Show Analytics Response (Advanced)</button>

<!-- The API response will be printed here. -->
<textarea cols="80" rows="20" id="query-output"></textarea>
<!--End of Main Body-->

<script>
    var mydata;
    // Replace with your view ID.
    var VIEW_ID = '222595492';

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
                        dateRanges: [{startDate: '7daysAgo', endDate: 'today'}],
                        metrics: [
                            {expression: 'ga:pageviews'},
                            {expression: 'ga:sessions'}
                        ]
                    }
                ]
            }
        }).then(displayResults, console.error.bind(console));
    }

    function displayResults(response) {
        var formattedJson = JSON.stringify(response.result, null, 2);
        var original = response.result;
        document.getElementById('query-output').value = formattedJson;
        var views = original.reports[0].data.totals[0].values[0];
        document.getElementById('views').innerHTML = "Views: " + views;
        var sessions = original.reports[0].data.totals[0].values[1];
        document.getElementById('sessions').innerHTML = "Sessions: " + sessions;
    }
</script>

<!-- Load the JavaScript API client and Sign-in library. -->
<script src="https://apis.google.com/js/client:platform.js"></script>

</body>
</html>

