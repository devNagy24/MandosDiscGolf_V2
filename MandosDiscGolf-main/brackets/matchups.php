<?php
// Enables admin/visitor session to be used with this page 
include_once '../includes/session.php';
// Checks to see if there is an active admin session
require_once '../includes/admin_check.php';
// call functions
require '../functions/functions.php';

$tournament_id = 0;

if (isset($_GET['tournament_id'])){
    $tournament_id = $_GET['tournament_id'];
}

if (isset($_POST['tournament_id'])){
    $tournament_id = $_POST['tournament_id'];
}

$title = getTitle($tournament_id);
$round = getMaxRound($tournament_id);

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
    <title>Edit Matchup Information  </title>
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
    <?php
        echo '<h3 class="text-center">' . $title . '</h3>';
        echo '</br>';
        echo '<h5 class="text-center">Round ' . $round . '</h5>';
    ?>
    </br>
    </br>

    <?php
        if (isset($_GET['tournament_id']) && !isset($_POST['tournament_id'])){
            $matchups = getMatchupInfo($_GET['tournament_id']);

            //create a counter to keep rows' number
            $counter = 0;

            echo ' <form
                class="container needs-validation"
                name="form1"
                method="POST"
                action=""
                novalidate
                             >';


            // create table rows
            foreach ($matchups as $key => $val) {
                echo '<td>' . $val["title"] . '</td>';

                echo '<fieldset class="row gx-2 gy-2 input-group pb-2">
                <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
                    <input
                            type="number"
                            class="form-control"
                            value="' . $val["matchup_id"] . '"
                            aria-label="name"
                            name="matchups[' . $counter . '][id]"
                            readonly
                    />
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
                    <input
                            type="text"
                            class="form-control"
                            value="' . $val["player1"] . '"
                            aria-label="name"
                            disabled
                    />
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
                    <input
                            type="text"
                            class="form-control"
                            value="' . $val["player2"] . '"
                            aria-label="name"
                            disabled
                    />
                </div>
                <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
                    <input
                            type="text"
                            class="form-control"
                            placeholder="Name"
                            value="' . $val["information"] . '"
                            aria-label="name"
                            name="matchups[' . $counter . '][info]"
                            pattern=".{1,}"
                            required
                    />
                    <label>Information</label>
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Enter the information.</div>
                </div>
            </fieldset>';

                $counter++;
            }
            echo '<input type="hidden" name="tournament_id" value="' . $_GET['tournament_id'] . '"/>';
            echo '</br>
                    </br>
                    <div class="row mt-3">
                        <div class="col">
                            <button
                                    class="btn btn-outline-dark"
                                    id="button"
                                    type="submit"
                                    value="Submit"
                                    onclick="formValidation()"
                            >
                                submit
                            </button>
                        </div>
                    </div>
                </form>';
        }
    ?>

    <?php
        if (isset($_POST['matchups'])){
            echo '<div class="alert alert-light mx-5 w-75" role="alert">
                    The Information is edited successfully!
                    </div>';
            foreach ($_POST['matchups'] as $key => $value) {
                updateMatchupInfo($value['id'], $value['info']);
            }


            echo '<table class="table table-responsive table-sm table-striped table-hover table-bordered border-dark caption-top mx-5 w-75">
                    <caption>List of Matchups</caption>
                    <thead>
                    <tr>
                        <th scope="col">Matchup ID</th>
                        <th scope="col">Player 1</th>
                        <th scope="col">Player 2</th>
                        <th scope="col">Information</th>
                    </tr>
                    </thead>
                    <tbody>';


                // get all matchups
                $matchups = getMatchupInfo($_POST['tournament_id']);


                // create table rows
                foreach ($matchups as $key => $val) {
                    echo '<tr>';
                    echo '<td>' . $val["matchup_id"] . '</td>';
                    echo '<td>' . $val["player1"] . '</td>';
                    echo '<td>' . $val["player2"] . '</td>';
                    echo '<td>' . $val["information"] . '</td>';
                    echo '</tr>';
                }
                }

        echo '</tbody>
                </table>';
    ?>


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


