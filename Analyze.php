<?php
session_start();
include_once('Elements\dbConnector.php');

//Retrieves the video based on the url (GET), defaults to the sea test video
//E.g. Analyze.php?view=sea_video.mp4 loads the Analyze.php page with sea_video.mp4
if (@$_GET['view']==null){
    //If no view is provided, a default is used.
$querry=$conn->query("SELECT * FROM content where filename='sea_video.mp4'");
}
else{
    //Otherwise, the user provided view string is used to display the correct video.
    //User provided view value is sanitized to prevent MySQL injection attacks.
    $view=htmlspecialchars($_GET['view']);
    $view=$conn->real_escape_string($view);
    $querry=$conn->query("SELECT * FROM content where filename='".$view."'");
}

//Get video details from database
$querry_output=$querry->fetch_assoc();
$c_filename=$querry_output["filename"];
$short_desc=$querry_output["short_desc"];
$poster=$querry_output["poster"];
$access=$querry_output["access_level"];
$uploader=$querry_output['uploader'];
$title = $querry_output['title'];

//Access Control for Analytics Pages
if ($user_type != 'admin' && $username != $uploader) {
    $_SESSION['cust_error_msg'] = "You are not authorized to see this page. If you believe this is an error, please contact your administrator.";
    header('Location: Error.php');
}

//Delete the video if the form has been submitted
if (isset($_POST['delete_video'])){
    if ($_POST['delete_video']=='delete'){
        $delete_query=$conn->query("DELETE FROM content WHERE filename='$view'");
        unlink("Content/$c_filename");
        unlink("Content/$poster");
        header('Location: Browse.php');
    }
}

$conn->close()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('Elements/TagmgrTag.html') ?>
    <!--Meta-->
    <meta charset="UTF-8">
    <title>Scaena | Analyze</title>
    <?php include('Elements\Imports.html') ?>

    <!--Functional Scripts-->
    <script>
        var currentNavItem = "#navLinkAnalyze";
        var setDatesToggled = false;
        function deleteVideo(){
            document.getElementById('delete_video').value='delete';
            document.getElementById('deleteVideoForm').submit();
        }
    </script>
    <script>
        $(document).ready(function () {
            $("#query-output").hide();
            $("#responseToggle").click(function () {
                $("#query-output").toggle();
            });
        });
    </script>
    <script>
        var retrieval_checker = setTimeout(function () {
            if (document.getElementById("query_check").innerHTML === "Waiting For Data...") {
                $("#no_login_message").show();
            }
        }, 6000)
    </script>
    <?php include('Elements\ReportingApi.html') ?>
</head>

<body>
<div class="page-container">
    <!-- Load the JavaScript API client and Sign-in library. -->
    <script src="https://apis.google.com/js/client:platform.js"></script>
    <!--Query Script Imports-->
    <?php include('Queries\Query_Startup.php') ?>
    <?php include('Queries\Query_Analytics.php') ?>
    <!--Main Body-->
    <?php include('Elements\Header.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-xl-6 text-center">
                <h1 id="contentHeading">Content Preview</h1>
                <h6>Watch or review your media</h6>
                <video class="video-js vjs-theme-sea" controls="true" id="video_player"
                       poster="Content/<?php echo $poster ?>">
                    <source src="Content/<?php echo $c_filename ?>" type="video/mp4">
                    <!--                <source src="//vjs.zencdn.net/v/oceans.webm" type="video/webm">-->
                </video>
                <script src="https://vjs.zencdn.net/7.8.3/video.js"></script>
                <script src="videojs.ga.min.js"></script>
                <script>
                    videojs('video_player', {}, function () {
                        this.ga(); // "load the plugin, by defaults tracks everything!!"
                    })
                    var vidPlayer = document.getElementById("video_player");
                </script>
                <h2><?php echo $title ?></h2>
                <h6><?php echo $short_desc ?></h6>
                <a href="Watch.php?view=<?php echo $c_filename ?>">Visit Content Page</a>
                <span> | </span>
                <a href="EditContent.php?view=<?php echo $c_filename ?>">Edit Content Details</a>
                <br>
                <a href="#" id="deleteVideoLink" data-toggle="modal" data-target="#deleteModal">Permanently Delete This Content</a>
            </div>
            <div class="col-xl-6 text-center border border-secondary bg bg-light">
                <h1>Quick Glance</h1>
                <h6>Performance overview for the last 30 days</h6>
                <h6 id="no_login_message" style="display: none; color:darkred;"><i class="material-icons">error</i> No
                    Data Retrieved. Please make sure you are logged in to Google Analytics.</h6>

                <p class="analytics_detail"><i class="material-icons">play_arrow</i> Video Plays: </p>
                <p id="plays" class="analytics_detail"></p>
                <br>

                <p class="analytics_detail"><i class="material-icons">done</i> Video Completions: </p>
                <p class="analytics_detail" id="completions"></p>
                <br>

                <p class="analytics_detail"><i class="material-icons">remove_red_eye</i> Total Views: </p>
                <p class="analytics_detail" id="views"></p>
                <br>

                <p class="analytics_detail"><i class="material-icons">web_asset</i> Total Sessions: </p>
                <p class="analytics_detail" id="sessions"></p>
                <br>

                <p class="analytics_detail"><i class="material-icons">person</i> Unique Visitors: </p>
                <p class="analytics_detail" id="visitors"></p>

                <!-- The Sign-in button. This will run `queryReports()` on success. -->
                <p class="g-signin2" data-onsuccess="queryReports"></p>
            </div>
        </div>
    </div>

<!--  Analytics Panel and Section-->

    <?php $queries = array();?>

    <h1>Analytics</h1>
    <p id="query_check" style="display: none">Waiting For Data...</p>
    <!-- Hidden Element that signifies whether data has query results have been printed-->

    <div class="container">


        <div class="text-center">
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="data_range" id="30d" autocomplete="off" checked
                           onclick="Query_Analytics('30daysAgo','today', false)">30 Days
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="data_range" id="365d" autocomplete="off"
                           onclick="Query_Analytics('365daysAgo','today', false)">1 Year
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="data_range" id="infd"
                           onclick="Query_Analytics('2010-01-01','today', false)">All Time
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="data_range" id="cusd" data-toggle="modal" data-target="#academicyearModal"">Academic Year
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="acyear_range" id="acyeard" data-toggle="modal" data-target="#exampleModal"">Custom
                </label>
            </div>
        </div>

        <!-- Custom Data Range Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Custom Date Range</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form onkeydown="return event.key !== 'Enter'">
                            <label for="daysStart">Please enter the number of previous days to retrieve data
                                from:</label><br>
                            <input type="text" class="form-control" id="daysStart" name="start"><br>
                        </form>
                        <div id="setdates" style="display: none">
                            <h6>Set Custom Dates</h6>
                            <label for="startDate">Start Date:</label>
                            <input type="date" class="form-control" id="startDate" name="startDate"><br>
                            <label for="endDate">End Date:</label>
                            <input type="date" class="form-control" id="endDate" name="endDate"><br>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger float-right" data-dismiss="modal"
                                onclick="cancelCustom()">Cancel
                        </button>
                        <button type="button" class="btn btn-secondary float-right" onclick="showSetDates()">Set Dates
                        </button>
                        <button type="button" class="btn btn-primary pull-right" onclick="customRange()"
                                data-dismiss="modal">Get Data
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            var setDatesToggled = false;

            function customRange() {
                if (setDatesToggled) {
                    var startDateVar = document.getElementById("startDate").value;
                    var endDateVar = document.getElementById("endDate").value;
                    var today = new Date();
                    today.setHours(0, 3, 0, 0);
                    var startDateDate = new Date(startDateVar);
                    var endDateDate = new Date(endDateVar);
                    if (startDateVar > endDateVar) {
                        alert("Could not retrieve data. The end date cannot be set before the start date.");
                        return;
                    }

                    console.log("Custom Start Date Set:" + startDateDate);
                    console.log("Custom End Date Set:" + endDateDate);

                    startDateVar = startDateDate - today;
                    console.log("Start Date Diff: " + startDateVar);
                    startDateVar = parseInt(Math.abs(startDateVar) / (1000 * 60 * 60 * 24));
                    console.log("Days Since Start Day: " + startDateVar);

                    endDateVar = endDateDate - today;
                    console.log("End Date Diff: " + endDateVar);
                    endDateVar = parseInt(Math.abs(endDateVar) / (1000 * 60 * 60 * 24));
                    console.log("Days Since Start Day: " + endDateVar);

                    setDatesToggled = false;
                    $("#setdates").hide();

                    startDateVar = startDateVar.toString().concat("daysAgo");
                    endDateVar = endDateVar.toString().concat("daysAgo");
                    console.log("Analytics Parameter Start Date:" + startDateVar);
                    console.log("Analytics Parameter End Date:" + endDateVar);
                    $("#daysStart").prop("disabled", false);
                    Query_Analytics(startDateVar, endDateVar, false);
                } else {
                    var days = document.getElementById("daysStart").value;
                    if (days == parseInt(days)) {
                        daysString = days.concat("daysAgo");
                        Query_Analytics(daysString, 'today', false)
                    } else alert("Please enter a valid number.")
                }
            }

            function showSetDates() {
                $("#setdates").toggle();
                setDatesToggled = !setDatesToggled;
                if ($('#daysStart').is(':disabled')) {
                    $("#daysStart").prop("disabled", false);
                } else $("#daysStart").prop("disabled", true);
            }

            function cancelCustom() {
                $("#setdates").hide();
                $("#daysStart").prop("disabled", false);
            }
        </script>

        <!-- Academic Year Modal -->
        <div class="modal fade" id="academicyearModal" tabindex="-1" role="dialog" aria-labelledby="academicyearModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="academicyearModalLabel">Academic Year Range</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
<!--                        <label for="acyearSelect">Select an academic year to retrieve data-->
<!--                            from:</label><br>-->
<!--                        <input type="text" class="form-control" id="acyearSelect" name="acyearSelect"><br>-->
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Select a year...
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <?php
                                $year=(int)date('Y');
                                $stop_year=$year-10;
                                //Display the last 10 years
                                while ($year>=$stop_year) {
                                    $preYear=$year-1;
                                    echo "<a class='dropdown-item' onclick='academicYear($year)'>$preYear - $year</a>";
                                    $year--;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger float-right" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function academicYear(year){
                var acYear = year;
                acYearStart = acYear-1;
                acYearStart = acYearStart+('-09-01');
                acYearEnd=acYear+('-07-31')
                console.log('Retrieving Data For Academic Year'+acYear);
                console.log('Academic Year Start: '+acYearStart);
                console.log('Academic Year End: '+acYearEnd);
                Query_Analytics(acYearStart, acYearEnd, false);
                $('#academicyearModal').modal('hide');
            }
        </script>

        <!--Analytic: Video Completion-->
        <h2 class="analytic_heading">Video Completions</h2>
        <div class="row  border border-secondary bg-light">
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
                                data: [50, 50],
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
                            responsive: true,
                            maintainAspectRatio: false,
                            tooltips: {
                                callbacks: {
                                    label: function (tooltipItem, data) {
                                        return data['labels'][tooltipItem['index']] + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '%';
                                    }
                                }
                            }
                        }
                    });

                </script>
            </div>

            <div class="col-xl-6 text-center">
                <br>
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
    <h2 class="analytic_heading">Device Types</h2>
    <div class="row border border-secondary bg-light">
        <div class="col-xl-6 text-center" id="devicesChartDiv">
            <canvas id="devicesChart" width="400" height="400"></canvas>
            <script>
                var ctx = document.getElementById('devicesChart').getContext('2d');

                var deviceChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Desktop', 'Tablet', 'Mobile'],
                        datasets: [{
                            data: [33, 33, 33],
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
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false

                        }
                    }
                });

            </script>
        </div>

        <div class="col-xl-6 text-center">
            <br>
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

    <!--Analytic: Plays, Pauses, Page Views-->
    <h2 class="analytic_heading">Plays, Pauses and Views </h2>
    <div class="row border border-secondary bg-light">
        <div class="col-xl-6 text-center" id="ppvChartDiv">
            <canvas id="ppvChart" width="400" height="400"></canvas>
            <script>
                var ctx = document.getElementById('ppvChart').getContext('2d');

                var ppvChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Plays', 'Pauses', 'Views'],
                        datasets: [{
                            data: [33, 33, 33],
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
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false

                        }
                    }
                });

            </script>
        </div>

        <div class="col-xl-6 text-center">
            <br>
            <h3 wclass="suggestionsTitle">Overview</h3>
            <br>

            <p class="analytics_detail"><i class="material-icons">play_arrow</i> Video Plays: </p>
            <span id="q3plays"> </span>
            <br>
            <br>

            <p class="analytics_detail"><i class="material-icons">pause</i> Video Pauses: </p>
            <span id="q3pauses"> </span>
            <br>
            <br>

            <p class="analytics_detail"><i class="material-icons">remove_red_eye</i> Page Views: </p>
            <span id="q3views"> </span>

            <h3 class="suggestionsTitle">Suggestions</h3>
            <p id="q3suggestions" class="suggestions">Stand by..</p>
        </div>

    </div>

    <!--Analytic: TimeOnPage and Sessions-->
    <h2 class="analytic_heading">Time on Page and Sessions </h2>
    <div class="row border border-secondary bg bg-light ">
        <div class="col-xl-6 text-center" id="timeseChartDiv">
            <canvas id="timeseChart" width="400" height="400"></canvas>
            <script>
                var ctx = document.getElementById('timeseChart').getContext('2d');

                var timeseChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Page Time (Min)', 'Session Time (Min)', 'Sessions'],
                        datasets: [{
                            data: [33, 33, 33],
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
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false

                        }
                    }
                });

            </script>
        </div>

        <div class="col-xl-6 text-center">
            <br>
            <h3 wclass="suggestionsTitle">Overview</h3>
            <br>

            <p class="analytics_detail"><i class="material-icons">access_time</i> Average Time on Page: </p>
            <span id="q4pagetime"> </span>
            <br>
            <br>

            <p class="analytics_detail"><i class="material-icons">timeline</i> Average Session Duration: </p>
            <span id="q4setime"> </span>
            <br>
            <br>

            <p class="analytics_detail"><i class="material-icons">web_asset</i> Total Number of Sessions: </p>
            <span id="q4sessions"> </span>

            <h3 class="suggestionsTitle">Suggestions</h3>
            <p id="q4suggestions" class="suggestions">Stand by..</p>
        </div>

    </div>

    <!--Analytic: Video Progress-->
    <h2 class="analytic_heading">Video Progress Marks</h2>
    <div class="row border border-secondary bg bg-light">
        <div class="col-xl-6 text-center" id="progChartDiv">
            <canvas id="progChart" width="400" height="400"></canvas>
            <script>
                var ctx = document.getElementById('progChart').getContext('2d');

                var progChart = new Chart(ctx, {
                    type: 'horizontalBar',
                    data: {
                        labels: ['Play', '25%', '50%', '75%', 'Completion'],
                        datasets: [{
                            data: [33, 33, 33, 33, 33],
                            backgroundColor: [
                                'rgba(77, 124, 190, 0.7)',
                                'rgba(244,231,211,0.7)',
                                'rgba(31,78,95,0.7)',
                                'rgba(8,129,163,0.7)',
                                'rgba(13,13,13,0.7)'
                            ],
                            borderColor: [
                                'rgba(77, 124, 190, 1)',
                                'rgba(244,231,211)',
                                'rgba(31,78,95)',
                                'rgba(8,129,163)',
                                'rgba(13,13,13)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: false

                        }
                    }
                });

            </script>
        </div>

        <div class="col-xl-6 text-center">
            <br>
            <h3 wclass="suggestionsTitle">Overview</h3>
            <br>

            <p class="analytics_detail"><span><i class="material-icons">play_arrow</i> Video Plays: </span>
                <span id="q5plays"> </span>
                <br>
                <br>

            <p class="analytics_detail"><i class="material-icons">star_border</i> 25% Completions: </p>
            <span id="q5progress25"> </span>
            <br>
            <br>

            <p class="analytics_detail"><i class="material-icons">star_half</i> 50% Completions: </p>
            <span id="q5progress50"> </span>
            <br>
            <br>

            <p class="analytics_detail"><i class="material-icons">star</i> 75% Completions: </p>
            <span id="q5progress75"> </span>
            <br>
            <br>

            <p class="analytics_detail"><i class="material-icons">check</i> Video Completions: </p>
            <span id="q5completions"> </span>

            <h3 class="suggestionsTitle">Suggestions</h3>
            <p id="q5suggestions" class="suggestions">Stand by..</p>
            <br>
            <p id="q5suggestions_additional" class="suggestions"></p>
        </div>

    </div>

    <!--Analytic: Exit Rate-->
    <h2 class="analytic_heading">Exit Rate</h2>
    <div class="row border border-secondary bg bg-light">
        <div class="col-xl-6 text-center" id="CompletionsChart">
            <canvas id="exitChart" width="400" height="400"></canvas>
            <script>
                var ctx = document.getElementById('exitChart').getContext('2d');

                var exitChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ['Exit', 'Non-Exit'],
                        datasets: [{
                            label: 'Complete and Partial Video Watches',
                            data: [50, 50],
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
                        responsive: true,
                        maintainAspectRatio: false,
                        tooltips: {
                            callbacks: {
                                label: function (tooltipItem, data) {
                                    return data['labels'][tooltipItem['index']] + ': ' + data['datasets'][0]['data'][tooltipItem['index']] + '%';
                                }
                            }
                        }
                    }
                });

            </script>
        </div>

        <div class="col-xl-6 text-center">
            <br>
            <h3 wclass="suggestionsTitle">Overview</h3>
            <br>
            <span><i class="material-icons">exit_to_app</i> Exit Rate: </span>
            <span id="q6exitrate"> </span>
            <br>
            <br>
            <span><i class="material-icons">double_arrow</i> Non-Exit Rate: </span>
            <span id="q6nonexitrate"> </span>
            <h3 class="suggestionsTitle">Suggestions</h3>
            <p id="q6suggestions" class="suggestions">Stand by..</p>
        </div>
    </div>

    <!--Analytic: Video Progress-->
    <h1 id="histHeading">Historical Data</h1>
    <h6>Performance glance up to 3 months ago.</h6>
    <div class="row">
        <div class="col-xl-12 text-center" id="histChartDiv">
            <canvas id="histChart" width="400" height="400"></canvas>
            <script>
                var ctx = document.getElementById('histChart').getContext('2d');

                var histChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['1 Month', '2 Months', '3 Months'],
                        datasets: [{
                            label:'Page Views',
                            data: [30, 30, 30],
                            backgroundColor: [
                                'rgba(77, 124, 190, 0.7)'
                            ],
                            borderColor: [
                                'rgba(77, 124, 190, 1)',
                            ],
                            borderWidth: 1,
                            fill:true,
                        },
                            {
                                label:'Time on Page (Min)',
                                data: [40, 40, 40],
                                backgroundColor: [
                                    'rgba(244,231,211,0.7)'
                                ],
                                borderColor: [
                                    'rgba(244, 231, 211, 1)'
                                ],
                                borderWidth: 1,
                                fill:true,
                            }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            display: true,
                            align:'start',
                            position:'top'

                        }
                    }
                });

            </script>
        </div>
    </div>



    <button class="inpage_button" id="responseToggle">Show Analytics Response (Advanced)</button>
    <a href="#" name="button1" onclick="dataLayer.push({'event': 'button1-click'});">Button 1</a>

    <!-- The API response will be printed here. -->
    <textarea cols="80" rows="20" id="query-output"></textarea>
</div>

<!--Delete Video Modal-->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Permanently Delete Video</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                This action will permanently delete this video and cannot be reversed. Are you sure you want to proceed?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" onclick="deleteVideo()">Delete</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!--End of Main Body-->

<form id="deleteVideoForm" name="deleteVideoForm" method="post">
    <input type="hidden" id="delete_video" name="delete_video" value="">
</form>


</body>
</html>

