<body>
<script> //Applies Active Nav Element Styling to Element Specified by Host Page's currentNavItem var
$( window ).on( "load", function(){
    $((currentNavItem).toString()).addClass("nav-item active");
})
</script>

<!--Top Nav-->
<div class="container" style="border-bottom: black">
<nav class="navbar navbar-expand-lg navbar-light" id="navbar" style="font-family:Roboto !important; font-weight: bold;
margin-bottom: 16px; margin-top: 16px">
    <a class="navbar-brand" href="#">
        <img src="Assets/scaena_logo_transparent.png" width="100" height="60" alt="Scaena" style="margin-bottom: 6px">
    </a>

    <!-- Mobile Navigation Toggle -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="main-navigation">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item" id="navLinkHome">
                <a class="nav-link" href="#">Home</a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="Discover.php" id="navLinkWatch">Watch</a>
            </li>
            <?php
            if ($user_type == 'admin' || $user_type == 'mod') {
                echo<<<EOD
             <li class="nav-item">
                <a class="nav-link" href="Browse.php" id="navLinkAnalyze">Analyze</a>
            </li>
EOD
                ;

            }

            ?>

            <li class="nav-item">
                <a class="nav-link" href="Options.php" id="navLinkOptions">Options</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="Contact.php" id="navLinkContact">About</a>
            </li>
        </ul>
    </div>
</nav>
</div>
</body>
