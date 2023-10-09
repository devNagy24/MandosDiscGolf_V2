<?php
// Enables admin/visitor session to be used with this page 
include_once '../includes/session.php';
// Checks to see if there is an active admin session
require_once '../includes/admin_check.php';
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
    <title>Ranking </title>
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
                        echo '<a class="flex-sm-fill text-sm-center nav-link alink" href="seedings.php">Seedings</a>';
                    } 
                ?>
                <a class="flex-sm-fill text-sm-center nav-link alink" href="../brackets/brackets.php">Brackets</a>
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

    <form 
      class="container needs-validation"
      name="ranking_page"
      method="POST"
      action="../brackets/bracket.php"
      novalidate>
        <div class="row gx-2 gy-2 input-group pb-2 mx-5 w-50">
            <div class="col-md-9 col-sm-12 form-floating mb-2">
                <input type="text" class="form-control" id="title" name="title" placeholder="Event Name: " pattern=".{1,}" required/>
                <label for="title" >Event Name</label>
                <div class="valid-feedback">
                    Looks good.
                </div>
                <div class="invalid-feedback">
                    Please enter a title.
                </div>
            </div>
            <div class="col-md-2 col-sm-12 form-check mb-2 mx-2 form-check form-switch">
                <input class="form-check-input" type="checkbox" name="sendemail" id="sendemail">
                <label class="form-check-label" for="sendemail">
                    Send email to all competitors
                </label>
            </div>
        </div>
        <div class="row gx-2 gy-2 input-group pb-2 mx-5 w-75">
            <div class="col form-floating mb-2">
                <textarea class="form-control" id="details" name="details" placeholder="Event Details: "></textarea>
                <label for="details">Event Details </label>
            </div>
        </div>



        
        <table class="table table-responsive table-sm table-striped table-hover table-bordered border-dark caption-top mx-5 w-75">
            <caption>Sorted Players</caption>
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Rating</th>
                <th scope="col">PDGA Number</th>
                <th scope="col">Email Address</th>
            </tr>
            </thead>
            <tbody>
    
            <?php
    
            //create an empty array for players data
            $dataArray = array();
    
            //create an empty array for players rating
            $ratingArray = array();
    
            // populate the new array with POST array
            foreach ($_POST as $players => $array2) {
                foreach ($array2 as $key => $val) {
                    $dataArray[] = $val ;
                    $ratingArray[] = $val["rating"];
                }
            }
    
            //sorting the rating array base on its values which is rating
            arsort($ratingArray);
    
            //create a counter to keep rows' number
            $counter = 1;
    

    
    
            // create table rows
            foreach ($ratingArray as $k => $value) {
                echo "<tr>";
                echo '<th scope="row">' . $counter . '</th>';
                echo '<td>' . $dataArray[$k]["name"] . '</td>';
                echo '<td>' . $dataArray[$k]["rating"] . '</td>';
                echo '<td>' . $dataArray[$k]["pdganumber"] . '</td>';
                echo '<td>' . $dataArray[$k]["email"] . '</td>';
                echo "</tr>";

                $counter++;
            }

            ?>
    
    
            </tbody>
        </table>


        <!-- buttons -->
        <div class="btn-group" role="group" aria-label="Basic outlined example">
            <button type="button" onclick="history.back()" class="btn btn-outline-dark m-5">Go Back</button>
            <button type="submit" value="Submit" onclick="formValidation()" class="btn btn-outline-dark m-5" id="button">Create Brackets</button>
        </div>


        <?php
        $counter2 = 0;
        // create players array
        foreach ($ratingArray as $k => $value) {

            echo '<input type="hidden" name="players[' . $counter2 . '][0]" value="' . $dataArray[$k]["name"] . '"/>';
            echo '<input type="hidden" name="players[' . $counter2 . '][2]" value="' . $dataArray[$k]["email"] . '"/>';
            echo '<input type="hidden" name="players[' . $counter2 . '][1]" value="' . $dataArray[$k]["rating"] . '"/>';
            echo '<input type="hidden" name="players[' . $counter2 . '][3]" value="' . $dataArray[$k]["pdganumber"] . '"/>';
            $counter2++;

        }

        ?>
    </form>
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

