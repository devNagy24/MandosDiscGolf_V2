<?php
// Enables admin/visitor session to be used with this page
include_once 'includes/session.php';
// call functions
require 'functions/functions.php';
if (isset($_GET["pdga_number"])) {
    $pdgaNumber = $_GET["pdga_number"];
}
if (isset($_GET["name"])) {
    $playerName = $_GET["name"];
}
var_dump($playerName);
var_dump($pdgaNumber);
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
    <title>Player Profile</title>
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
                    echo '<a class="flex-sm-fill text-sm-center nav-link alink" href="player_search.php">Player Search</a>';
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
<!-- player profile page -->
<div class="container my-5">
    <h1 class="text-center mb-5">Player Profile:
        <?php

        getName($pdgaNumber, $playerName);
//        include('test/simple_html_dom.php');
//        $html = file_get_html('https://www.pdga.com/player/'.'71872');
//        echo $html->find('h1', 0)->plaintext."<br>";
        ?>
    </h1>
<!--    Page only works if going from home page or player search page-->

    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
<!--                <img src="assets/profile_icon.jpg" class="rounded" alt="picture of a green profile icon" width="100%">-->
                <?php
//                include('test/simple_html_dom.php');
//                $html = file_get_html('https://www.pdga.com/player/'.'71872');
//                echo $html->find('img', 1);
                getPlayerImage($_GET["pdga_number"]);
                ?>
            </div>
            <div class="col-6">
                <p><strong>Name:<br></strong> <?php getName($pdgaNumber, $playerName)?></p>
                <hr>
                <p><strong>Phone:<br></strong> <?php getPhone($pdgaNumber, $playerName)?></p>
                <hr>
                <p><strong>Email:<br></strong> <?php getEmail($pdgaNumber, $playerName)?></p>
                <hr>
                <p><strong>PDGA Number:<br></strong> <?php echo $pdgaNumber?></p>
                <hr>
                <p><strong>Rating:<br></strong><?php getRating($pdgaNumber, $playerName)?></p>
                <hr>
            </div>
        </div>
    </div>
</div>
<!--        working ended-->
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
            >The Pentagon Team</a>
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
