<?php
include_once 'includes/session.php';
// call functions
require 'functions/functions.php';

if (isset($_POST["name"]) || isset($_POST["pdga_number"])) {
    // get search parameters
    $name = $_POST["name"];
}
if (isset($_POST["pdga_number"])) {
    // get search parameters
    $pdga_number = $_POST["pdga_number"];
}
//echo $name;
//echo $pdga_number;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!--Bootstrap 5 CSS-->
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
            crossorigin="anonymous"
    />
    <link rel="stylesheet" href="styles/style.css" />
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Player Search </title>
</head>
<body class="d-flex flex-column">
<header class="bg-dark">
    <div class="d-flex flex-column flex-sm-row align-items-center">
        <div>
            <a class="navbar-brand" href="index.php"><img src="assets/mandos_redesign_3.webp" id="logo" alt="mandos_redesign_3.webp"></a>
        </div>

        <div>
            <nav class="nav nav-pills flex-column flex-sm-row navbar-dark bg-dark">
                <a class="flex-sm-fill text-sm-center nav-link alink" href="index.php">Home</a>
                <?php
                if(isset($_SESSION['username'])){
                    echo '<a class="flex-sm-fill text-sm-center nav-link alink new-active" href="player_search.php">Player Search</a>';
                    echo '<a class="flex-sm-fill text-sm-center nav-link alink" href="seedings/seedings.php">Seedings</a>';
                }
                ?>
                <a class="flex-sm-fill text-sm-center nav-link alink" href="brackets/brackets.php">Brackets</a>
            </nav>
        </div>
        <nav class="nav ms-auto">
            <?php
            if(!isset($_SESSION['username'])){
                echo '<a class="text-sm-center nav-link alink" href="login.php">Login</a>';
            } else {
                echo '<div id="loginName" class ="text-white">Hello '. $_SESSION['username'] .'! </div>';
                echo '<a class="text-sm-center nav-link alink" href="logout.php">Logout</a>';
            }
            ?>
        </nav>
    </div>

    <h1 class="bg-dark text-center headertext"> Mando's Disc Golf Tournaments</h1>

</header>
    <!-- player search -->
    <div class="container my-5">
        <h1 class="text-center mb-5">Player Search</h1>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <!-- player search form -->
                <form name="player_search" method="POST" action="">
                    <div class="row">
                        <label for="search-input" class="form-label">Search by Name Or PDGA Number:</label>
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" name="pdga_number" id="pdga_number" placeholder="PDGA Number">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger">Search</button>
                </form>
            </div>
        </div>
        <!-- player search results table -->
        <div class="row my-5">
            <div class="col-md-8 offset-md-2">

                <form name="player_search" method="POST" action="player_profile.php">


                    <table id="player-search-results" class="table table-striped table-bordered border-dark">


                        <!-- Player search results displayed here -->
                        <?php
                        // if admin searches for player
                       if (isset($_POST["name"])) {
                            echo " <thead>
                                      <tr>
                                        <th>Name</th>
                                        <th>PDGA Number</th>
                                        <th>Rating</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th></th>
                                     </tr>
                                    </thead>
                                    <tbody>";
                            //echo $_POST["name"];
                            // search for players
                            search_players($_POST["name"], $_POST["pdga_number"]);
                        }
                        // default page view
                        else {
                            echo" <thead>
                    <tr>
                        <th>Name</th>
                        <th>PDGA Number</th>
                        <th>Rating</th>
                        <th>Email</th>
                        <th>Phone</th>
                    </tr>
                    </thead>
                    <tbody>";
                        }
                        //var_dump($_POST["pdga_number"]);
                        ?>
                        </tbody>
                </table>
<!--                    <input type="hidden" name="name" id="nameHidden" value="--><?php //echo $name?><!--">-->
<!--                    <input type="hidden" name="pdgaNumber" id="pdgaNumber" value="">-->
<!---->
<!--                    <button type="submit" class="btn btn-danger">View Player Profile</button>-->


                </form>

            </div>
        </div>
    </div>
    </br>
    </br>
    </br>
    </br>


<!-- footer -->

<footer class="w-100 bg-dark">
    </br>
    </br>
    <div class="d-flex flex-row justify-content-center mb-3">
        <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.mandos-shop.com/" target="_blank"><img src="assets/globe.png" alt="globe.png"></a></div>
        <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.instagram.com/mandos_disc_golf/" target="_blank"><img src="assets/instagram.png" alt="instagram.png"></a></a></div>
        <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.facebook.com/Mandosdiscgolfshop/" target="_blank"><img src="assets/facebook.png" alt="facebook.png"></a></a></div>
    </div>
    </br>
    </br>
    </br>

    <div class="d-flex flex-row justify-content-evenly mb-3">
        <div class="text-center p-3">
            © 2023 Copyright:
            <a class="text-secondary text-decoration-none"

               href="https://thepentagon.greenriverdev.com/"
            >The Pentagon Team</a
            >
        </div>
    </div>
</footer>



<!-- JavaScript-->
<script src="scripts/script.js"></script>
<!--Bootstrap 5 JavaScript plugins and Popper.-->
<script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"
></script>
<script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"
></script>
</body>
</html>