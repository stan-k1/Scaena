<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <?php include('Elements/TagmgrTag.html') ?>
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
    <script>
        var retrieval_checker=setTimeout(function () {
            if (document.getElementById("query_check").innerHTML==="Waiting For Data..."){
                $("#no_login_message").show();
            }
        },5000)
    </script>
    <?php include('Elements/ReportingApi.html')?>
</head>

<body>
<!-- Load the JavaScript API client and Sign-in library. -->
<script src="https://apis.google.com/js/client:platform.js"></script>
<!--Query Script Imports-->
<?php include('Queries\Query_Startup.php') ?>
<?php include('Queries\Query_Analytics.php') ?>
<!--Main Body-->
<?php include('Elements\Header.html'); ?>

<div class="container">
    <div class="row">
        <div class="col-xl-6 text-center">
            <h1>Content Preview</h1>
            <h6>Watch or review your media</h6>
            <video class="video-js vjs-theme-sea" controls="true" id="video_player"
                   poster="https://www.carbonbrief.org/wp-content/uploads/2019/09/Blue-green-sea-surface-background-with-fishes-full-frame-composition-DWGX61-420x280.jpg">
                <source src="Assets/sea_video.mp4" type="video/mp4">
<!--                <source src="//vjs.zencdn.net/v/oceans.webm" type="video/webm">-->
            </video>
            <script src="https://vjs.zencdn.net/7.8.3/video.js"></script>
            <script src="videojs.ga.min.js"></script>
            <script>
                videojs('video_player', {}, function () {
                    this.ga(); // "load the plugin, by defaults tracks everything!!"
                })
            </script>
        </div>
        <div class="col-xl-6 text-center">
            <h1>Quick Glance</h1>
            <h6>Performance overview for the last 30 days</h6>
            <h6 id="no_login_message" style="display: none; color:darkred;"><i class="material-icons">error</i> No Data Retrieved. Please make sure you are logged in to Google Analytics.</h6>

            <p class="analytics_detail"><i class="material-icons">done</i> Video Completions: </p>
            <p class="analytics_detail" id="completions"> </p>
            <br>

            <p class="analytics_detail"><i class="material-icons">play_arrow</i> Video Plays: </p>
            <p id="plays" class="analytics_detail"> </p>
            <br>

            <p class="analytics_detail"><i class="material-icons">remove_red_eye</i> Total Views: </p>
            <p class="analytics_detail" id="views"> </p>
            <br>

            <p class="analytics_detail"><i class="material-icons">web_asset</i> Total Sessions: </p>
            <p class="analytics_detail"  id="sessions"> </p>
            <br>

            <p class="analytics_detail"><i class="material-icons">person</i> Unique Visitors: </p>
            <p class="analytics_detail"  id="visitors"> </p>

            <!-- The Sign-in button. This will run `queryReports()` on success. -->
            <p class="g-signin2" data-onsuccess="queryReports"></p>
        </div>
    </div>
</div>

<?php $queries = array();
echo($_SERVER['QUERY_STRING']);?>

<h1>Content Analytics</h1>
<p id="query_check" style="display: none">Waiting For Data...</p> <!-- Hidden Element that signifies whether data has query results have been printed-->

<div class="container">
    <div class="text-center">
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-secondary" onclick="Query_Analytics('3daysAgo','today', false)">30 Days </button>
        <button type="button" class="btn btn-secondary" onclick="Query_Analytics('2010-01-01','today', false)">All Time</button>
    </div>
    </div>
    <!--Analytic: Video Completion-->
    <h2>Video Completions</h2>
    <div class="row">
        <div class="col-xl-6 text-center" id="CompletionsChart">
            <canvas id="myChart" width="400" height="400"></canvas>
            <script>
                var ctx = document.getElementById('myChart').getContext('2d');

                var myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Complete Watch', 'Partial Watch'],
                        datasets: [{
                            label: 'Complete and Partial Video Watches',
                            data: [50,50],
                            backgroundColor: [
                                'rgba(77, 124, 190, 0.7)',
                                'rgba(244,231,211,0.7)',
                            ],
                            borderColor: [
                                'rgba(77, 124, 190, 1)',
                                'rgba(244,231,211)',
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive:true,
                        maintainAspectRatio: false,
                        tooltips: {
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    return data['labels'][tooltipItem['index']] + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '%';
                                }
                            }
                        }
                    }
                });

            </script>
        </div>

        <div class="col-xl-6 text-center">
            <h3 wclass="suggestionsTitle">Overview</h3>
            <br>
            <span><i class="material-icons">done</i> Video Completions: </span>
            <span id="q1completions"> </span>
            <br>
            <br>
            <span><i class="material-icons">play_arrow</i> Video Plays: </span>
            <span id="q1partials"> </span>
            <h3 class="suggestionsTitle">Suggestions</h3>
            <p id="q1suggestions" class="suggestions">Stand by..</p>
        </div>
    </div>
</div>

<!--Analytic: Device Type-->
<h2>Device Types</h2>
<div class="row">
    <div class="col-xl-6 text-center" id="devicesChartDiv">
        <canvas id="devicesChart" width="400" height="400"></canvas>
        <script>
            var ctx = document.getElementById('devicesChart').getContext('2d');

            var deviceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Desktop', 'Tablet','Mobile'],
                    datasets: [{
                        data: [33,33,33],
                        backgroundColor: [
                            'rgba(77, 124, 190, 0.7)',
                            'rgba(244,231,211,0.7)',
                            'rgba(31,78,95,0.7)',
                        ],
                        borderColor: [
                            'rgba(77, 124, 190, 1)',
                            'rgba(244,231,211)',
                            'rgba(31,78,95)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive:true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false

                    }}
            });

        </script>
    </div>

    <div class="col-xl-6 text-center">
        <h3 wclass="suggestionsTitle">Overview</h3>
        <br>

        <p class="analytics_detail"><i class="material-icons">stay_primary_portrait</i> Desktop Visits: </p>
        <span id="q2desktops"> </span>
        <br>
        <br>

        <p class="analytics_detail"><i class="material-icons">tablet_mac</i> Tablet Visits: </p>
        <span id="q2tablets"> </span>
        <br>
        <br>

        <p class="analytics_detail"><i class="material-icons">desktop_windows</i> Mobile Visits: </p>
        <span id="q2phones"> </span>

        <h3 class="suggestionsTitle">Suggestions</h3>
        <p id="q2suggestions" class="suggestions">Stand by..</p>
    </div>
</div>
</div>

<button class="inpage_button" id="responseToggle">Show Analytics Response (Advanced)</button>
<a href="#" name="button1" onclick="dataLayer.push({'event': 'button1-click'});" >Button 1</a>

<!-- The API response will be printed here. -->
<textarea cols="80" rows="20" id="query-output"></textarea>
<!--End of Main Body-->


</body>
</html>

