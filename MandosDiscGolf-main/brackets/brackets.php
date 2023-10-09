<?php
// Enables admin/visitor session to be used with this page 
include_once '../includes/session.php';
// call functions
require '../functions/functions.php';

$tournaments = getAllTournaments();
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
    <link rel="stylesheet" href="../styles/style.css" />
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Brackets </title>
</head>
<body class="d-flex flex-column">
<header class="bg-dark">
    <div class="d-flex flex-column flex-sm-row align-items-center">
        <div>    <a class="navbar-brand" href="../index.php"><img src="../assets/mandos_redesign_3.webp" id="logo" alt="mandos_redesign_3.webp"></a>
        </div>

        <div>
            <nav class="nav nav-pills flex-column flex-sm-row navbar-dark bg-dark">
                <a class="flex-sm-fill text-sm-center nav-link alink" href="../index.php">Home</a>
                <?php
                if(isset($_SESSION['username'])){
                    echo '<a class="flex-sm-fill text-sm-center nav-link alink" href="../player_search.php">Player Search</a>';
                    echo '<a class="flex-sm-fill text-sm-center nav-link alink" href="../seedings/seedings.php">Seedings</a>';
                }
                ?>
                <a class="flex-sm-fill text-sm-center nav-link alink new-active" href="brackets.php">Brackets</a>
            </nav>
        </div>
        <nav class="nav ms-auto">
            <?php 
                if(!isset($_SESSION['username'])){
                   echo '<a class="text-sm-center nav-link alink" href="../login.php">Login</a>';
                } else {
                   echo '<div id="loginName" class ="text-white">Hello '. $_SESSION['username'] .'! </div>';
                   echo '<a class="text-sm-center nav-link alink" href="../logout.php">Logout</a>';
                }
            ?>
        </nav>
    </div>

    <h1 class="bg-dark text-center headertext"> Mando's Disc Golf Tournaments</h1>

</header>


<article>
    </br>
    </br>

    <table class="table table-responsive table-sm table-striped table-hover table-bordered border-dark caption-top mx-5 w-75">
        <caption>List of Tournaments</caption>
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Tournament Name</th>
            <th scope="col">Tournament Size</th>
            <th scope="col">Tournament Status</th>
            <th scope="col">&nbsp;</th>
        </tr>
        </thead>
        <tbody>

        <?php

        //create a counter to keep rows' number
        $counter = 1;

        if(!isset($_SESSION['username'])){
                        // create table rows
            foreach ($tournaments as $key => $val) {
                echo '<tr>';
                echo '<th scope="row">' . $counter . '</th>';
                echo '<td>' . $val["title"] . '</td>';
                echo '<td>' . $val["size"] . '</td>';
                echo '<td>' . (($val["is_active"] == 0) ? "Finished" : "Active") . '</td>';
    
                $link = 'bracket.php?' . http_build_query(array('tournament_id' => $val["tournament_id"]));
                echo '<td><a href="' . $link . '" class="btn btn-outline-dark">View</a></td>';
                $counter++;
            }
        } else {
            // create table rows
            foreach ($tournaments as $key => $val) {
                echo '<tr>';
                echo '<th scope="row">' . $counter . '</th>';
                echo '<td>' . $val["title"] . '</td>';
                echo '<td>' . $val["size"] . '</td>';
                echo '<td>' . (($val["is_active"] == 0) ? "Finished" : "Active") . '</td>';
    
                $link = 'bracket.php?' . http_build_query(array('tournament_id' => $val["tournament_id"]));
                $link2 = 'matchups.php?' . http_build_query(array('tournament_id' => $val["tournament_id"]));
    
                if ($val["is_active"] == 0) {
                    echo '<td><a href="' . $link . '" class="btn btn-outline-dark">View</a></td>';
                } else {
                    echo '<td><a href="' . $link . '" class="btn btn-outline-dark">Edit Bracket</a> &nbsp; <a href="' . $link2 . '" class="btn btn-outline-dark">Edit Matchup Information </a></td>';
                }
    
                echo '</tr>';
    
                $counter++;
            }
        }
        

        ?>


        </tbody>
    </table>
    <!--End of container-->

    </br>
    </br>
</article>


<!-- footer -->

<footer class="w-100 bg-dark">
    </br>
    </br>
    <div class="d-flex flex-row justify-content-center mb-3">
        <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.mandos-shop.com/" target="_blank"><img src="../assets/globe.png" alt="globe.png"></a></div>
        <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.instagram.com/mandos_disc_golf/" target="_blank"><img src="../assets/instagram.png" alt="instagram.png"></a></a></div>
        <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.facebook.com/Mandosdiscgolfshop/" target="_blank"><img src="../assets/facebook.png" alt="facebook.png"></a></a></div>
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
<script src="../scripts/script.js"></script>
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

