<?php

// Enables admin/visitor session to be used with this page 

include_once '../includes/session.php';

// call functions

require '../functions/functions.php';

//$token = $_GET['token'];
//$_SESSION['username'] = 'player';

//var_dump($_SESSION['username']);
//var_dump('TournamentID= ' + $_SESSION['tournamentIdEmail']);

$tournament_id = 0;

$size = count($_POST['players']);


//check if it is the creation or the update process

if (!isset($_POST['tournament_id']) && isset($_POST['title']) && (($size == 16) || ($size == 32) || ($size == 64))) {

    $tournamentId = $_POST['tournament_id'];

    $title = $_POST['title'];

    $details = $_POST['details'];

    $player_ids = array();

    $is_active = 1;

    $send_email = 0;

    if (isset($_POST['sendemail'])) {

        $send_email = 1;
    }


    // the sort function that sort players for each bracket size

    $players = $_POST['players'];


    //=============================> sorting

    $groupArray = array();

    $sortedPlayers = array();


    function sortArray()

    {

        global $groupArray;

        for ($index = 0; $index < sizeof($groupArray); ++$index) {

            $groupArray[$index][0] = $groupArray[$index];

            $groupArray[$index][1] = end($groupArray);

            array_pop($groupArray);
        }
    }


    function createSortedArray($arraySize)

    {

        global $size, $players, $groupArray, $sortedPlayers;


        for ($index = 0; $index < sizeof($players); ++$index) {

            $groupArray[$index][0] = $players[$index];

            $groupArray[$index][1] = end($players);

            array_pop($players);
        }


        $roundNum = 0;

        while ($arraySize / 2 != 1) {

            $roundNum++;

            $arraySize = $arraySize / 2;
        }


        for ($i = 0; $i < $roundNum; $i++) {

            sortArray();
        }


        if ($size == 16) {

            for ($row = 0; $row < sizeof($groupArray); $row++) {

                $i = 0;

                for ($col = 0; $col < 2; $col++) {

                    for ($index = 0; $index < 2; $index++) {

                        for ($row1 = 0; $row1 < 2; $row1++) {

                            for ($row2 = 0; $row2 < 2; $row2++) {

                                $sortedPlayers[$i] = $groupArray[$row][$col][$index][$row1][$row2];

                                $i++;
                            }
                        }
                    }
                }
            }
        } elseif ($size == 32) {

            for ($row1 = 0; $row1 < sizeof($groupArray); $row1++) {

                $i = 0;

                for ($row2 = 0; $row2 < 2; $row2++) {

                    for ($row3 = 0; $row3 < 2; $row3++) {

                        for ($row4 = 0; $row4 < 2; $row4++) {

                            for ($row5 = 0; $row5 < 2; $row5++) {

                                for ($row6 = 0; $row6 < 2; $row6++) {

                                    $sortedPlayers[$i] = $groupArray[$row1][$row2][$row3][$row4][$row5][$row6];

                                    $i++;
                                }
                            }
                        }
                    }
                }
            }
        } elseif ($size == 64) {

            for ($row = 0; $row < sizeof($groupArray); $row++) {

                $i = 0;

                for ($col = 0; $col < 2; $col++) {

                    for ($index = 0; $index < 2; $index++) {

                        for ($row1 = 0; $row1 < 2; $row1++) {

                            for ($row2 = 0; $row2 < 2; $row2++) {

                                for ($row3 = 0; $row3 < 2; $row3++) {

                                    for ($row4 = 0; $row4 < 2; $row4++) {

                                        $sortedPlayers[$i] = $groupArray[$row][$col][$index][$row1][$row2][$row3][$row4];

                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }


    createSortedArray($size);


    // insert players data

    foreach ($sortedPlayers as $key => $val) {


        // data validation

        $nameBool = validateName($val[0]) == 1;

        $val[1] = empty($val[1]) ? 0 : intval($val[1]);

        $ratingBool = validateRating($val[1]);

        $val[3] = empty($val[3]) ? 0 : intval($val[3]);

        $pdgaBool = validatePDGANumber($val[3]);

        $emailBool = validateEmail($val[0], $val[2]);


        if ($nameBool && $ratingBool && $emailBool && $pdgaBool) {

            $player_ids[] = insertPlayer($val[0], $val[1], $val[2], $val[3]);
        } else {

            exit("The data is not valid!");
        }
    }


    // create tournament

    $tournament_id = insertTournament($size, $title, $details, $send_email, $is_active);


    $counter = 0;

    // create matchups

    for ($i = 0; $i < $size; $i += 2) {


        $counter++;

        $matchup_info = 'Round 1 Matchup ' . $counter;

        insertMatchup(intval($player_ids[$i]), intval($player_ids[$i + 1]), 1, $tournament_id, $counter, $matchup_info);


        // send email for each matchup players

        if (getSendEmail($tournament_id)) {

            $name1 = getPlayerName(intval($player_ids[$i]));

            $name2 = getPlayerName(intval($player_ids[$i + 1]));


            $rating1 = getPlayerRating(intval($player_ids[$i]));

            $rating2 = getPlayerRating(intval($player_ids[$i + 1]));


            $email1 = getPlayerEmail(intval($player_ids[$i]));

            $email2 = getPlayerEmail(intval($player_ids[$i + 1]));

            $player1Id = getPlayerId($name1);
            $player2Id = getPlayerId($name2);
            $matchupToken = getMatchupId($player1Id,$player2Id); //TODO add function to get playerid by name
            echo '<h1>' . $matchupToken . '</h1>';
//            $matchupToken = 1373;

            sendEmail($title, $name1, $name2, $rating1, $rating2, $email1, $email2, $matchupToken);

        }
    }
} else {

    //variables

    $tournament_id = $_POST['tournament_id'];
}


if (isset($_GET['tournament_id'])) {

    $tournament_id = $_GET['tournament_id'];
    //var_dump($tournament_id);

}


// get required variables and arrays

$title = getTitle($tournament_id);

$details = getDetails($tournament_id);

$size = getSize($tournament_id);

$max_round = getMaxRound($tournament_id);

$counter = 0;

$players1 = roundPlayers($tournament_id, 1);

$players2 = null;

$players3 = null;

$players4 = null;

$players5 = null;

$players6 = null;

$players7 = null;

$m_info1 = array();
$m_info2 = array();
$m_info3 = array();
$m_info4 = array();
$m_info5 = array();
$m_info6 = array();


if ($max_round == 1) {

    $m_info1 = getOnlyInfo($tournament_id);
}

if ($max_round == 2) {

    $players2 = roundPlayers($tournament_id, 2);
    $m_info2 = getOnlyInfo($tournament_id);
} elseif ($max_round == 3) {

    $players2 = roundPlayers($tournament_id, 2);

    $players3 = roundPlayers($tournament_id, 3);
    $m_info3 = getOnlyInfo($tournament_id);
} elseif ($max_round == 4) {

    $players2 = roundPlayers($tournament_id, 2);

    $players3 = roundPlayers($tournament_id, 3);

    $players4 = roundPlayers($tournament_id, 4);
    $m_info4 = getOnlyInfo($tournament_id);
} elseif ($max_round == 5) {

    $players2 = roundPlayers($tournament_id, 2);

    $players3 = roundPlayers($tournament_id, 3);

    $players4 = roundPlayers($tournament_id, 4);

    $players5 = roundPlayers($tournament_id, 5);
    $m_info5 = getOnlyInfo($tournament_id);
} elseif ($max_round == 6) {

    $players2 = roundPlayers($tournament_id, 2);

    $players3 = roundPlayers($tournament_id, 3);

    $players4 = roundPlayers($tournament_id, 4);

    $players5 = roundPlayers($tournament_id, 5);

    $players6 = roundPlayers($tournament_id, 6);
    $m_info6 = getOnlyInfo($tournament_id);
} elseif ($max_round == 7) {

    $players2 = roundPlayers($tournament_id, 2);

    $players3 = roundPlayers($tournament_id, 3);

    $players4 = roundPlayers($tournament_id, 4);

    $players5 = roundPlayers($tournament_id, 5);

    $players6 = roundPlayers($tournament_id, 6);

    $players7 = roundPlayers($tournament_id, 7);
}


$j = 0; // a counter


//create matchup for round 2

if (!is_null($_POST['players2'])) {

    for ($i = 0; $i < count($_POST['players2']); $i += 2) {


        $player1 = getPlayerId($_POST['players2'][$i]);

        $player2 = getPlayerId($_POST['players2'][$i + 1]);


        // Is the first player winner? - for odd matchups

        if ($players1[$j] == $_POST['players2'][$i]) {

            updateMatchupWinner($tournament_id, 1, $player1, 1);
        } else {

            updateMatchupWinner($tournament_id, 1, getPlayerId($players1[$j]), 0);
        }


        // Is the first player winner? - for even matchups

        if ($players1[$j += 2] == $_POST['players2'][$i + 1]) {

            updateMatchupWinner($tournament_id, 1, $player2, 1);
        } else {

            updateMatchupWinner($tournament_id, 1, getPlayerId($players1[$j]), 0);
        }

        $j += 2;


        $counter++;

        $matchup_info = 'Round 2 Matchup ' . $counter;

        insertMatchup($player1, $player2, 2, $tournament_id, $counter, $matchup_info);


        // send email for each matchup players

        if (getSendEmail($tournament_id)) {

            $name1 = $_POST['players2'][$i];

            $name2 = $_POST['players2'][$i + 1];


            $rating1 = getPlayerRating($player1);

            $rating2 = getPlayerRating($player2);


            $email1 = getPlayerEmail($player1);

            $email2 = getPlayerEmail($player2);

            $player1Id = getPlayerId($name1);
            $player2Id = getPlayerId($name2);
            $matchupToken = getMatchupId($player1Id,$player2Id); //TODO add function to get playerid by name
            echo '<h1>' . $matchupToken . '</h1>';
//            $matchupToken = 1373;


            sendEmail($title, $name1, $name2, $rating1, $rating2, $email1, $email2, $matchupToken);

        }
    }

    $players2 = roundPlayers($tournament_id, 2);
}


//create matchup for round 3

if (!is_null($_POST['players3'])) {


    for ($i = 0; $i < count($_POST['players3']); $i += 2) {

        $player1 = getPlayerId($_POST['players3'][$i]);

        $player2 = getPlayerId($_POST['players3'][$i + 1]);


        // Is the first player winner? - for odd matchups

        if ($players2[$j] == $_POST['players3'][$i]) {

            updateMatchupWinner($tournament_id, 2, $player1, 1);
        } else {

            updateMatchupWinner($tournament_id, 2, getPlayerId($players2[$j]), 0);
        }


        // Is the first player winner? - for even matchups

        if ($players2[$j += 2] == $_POST['players3'][$i + 1]) {

            updateMatchupWinner($tournament_id, 2, $player2, 1);
        } else {

            updateMatchupWinner($tournament_id, 2, getPlayerId($players2[$j]), 0);
        }

        $j += 2;


        $counter++;

        $matchup_info = 'Round 3 Matchup ' . $counter;

        insertMatchup($player1, $player2, 3, $tournament_id, $counter, $matchup_info);


        // send email for each matchup players

        if (getSendEmail($tournament_id)) {

            $name1 = $_POST['players3'][$i];

            $name2 = $_POST['players3'][$i + 1];


            $rating1 = getPlayerRating($player1);

            $rating2 = getPlayerRating($player2);


            $email1 = getPlayerEmail($player1);

            $email2 = getPlayerEmail($player2);


            $player1Id = getPlayerId($name1);
            $player2Id = getPlayerId($name2);
            $matchupToken = getMatchupId($player1Id,$player2Id); //TODO add function to get playerid by name
            echo '<h1>' . $matchupToken . '</h1>';
//            $matchupToken = 1373;


            sendEmail($title, $name1, $name2, $rating1, $rating2, $email1, $email2, $matchupToken);

        }
    }


    $players3 = roundPlayers($tournament_id, 3);
}


//create matchup for round 4

if (!is_null($_POST['players4'])) {


    for ($i = 0; $i < count($_POST['players4']); $i += 2) {

        $player1 = getPlayerId($_POST['players4'][$i]);

        $player2 = getPlayerId($_POST['players4'][$i + 1]);


        // Is the first player winner? - for odd matchups

        if ($players3[$j] == $_POST['players4'][$i]) {

            updateMatchupWinner($tournament_id, 3, $player1, 1);
        } else {

            updateMatchupWinner($tournament_id, 3, getPlayerId($players3[$j]), 0);
        }


        // Is the first player winner? - for even matchups

        if ($players3[$j += 2] == $_POST['players4'][$i + 1]) {

            updateMatchupWinner($tournament_id, 3, $player2, 1);
        } else {

            updateMatchupWinner($tournament_id, 3, getPlayerId($players3[$j]), 0);
        }

        $j += 2;


        $counter++;

        $matchup_info = 'Round 4 Matchup ' . $counter;

        insertMatchup($player1, $player2, 4, $tournament_id, $counter, $matchup_info);


        // send email for each matchup players

        if (getSendEmail($tournament_id)) {

            $name1 = $_POST['players4'][$i];

            $name2 = $_POST['players4'][$i + 1];


            $rating1 = getPlayerRating($player1);

            $rating2 = getPlayerRating($player2);


            $email1 = getPlayerEmail($player1);

            $email2 = getPlayerEmail($player2);



            $player1Id = getPlayerId($name1);
            $player2Id = getPlayerId($name2);
            $matchupToken = getMatchupId($player1Id,$player2Id); //TODO add function to get playerid by name
            echo '<h1>' . $matchupToken . '</h1>';
//            $matchupToken = 1373;


            sendEmail($title, $name1, $name2, $rating1, $rating2, $email1, $email2, $matchupToken);

        }
    }


    $players4 = roundPlayers($tournament_id, 4);
}


//create matchup for round 5

if (!is_null($_POST['players5'])) {


    for ($i = 0; $i < count($_POST['players5']); $i += 2) {

        $player1 = getPlayerId($_POST['players5'][$i]);

        $player2 = getPlayerId($_POST['players5'][$i + 1]);


        // Is the first player winner? - for odd matchups

        if ($players4[$j] == $_POST['players5'][$i]) {

            updateMatchupWinner($tournament_id, 4, $player1, 1);
        } else {

            updateMatchupWinner($tournament_id, 4, getPlayerId($players4[$j]), 0);
        }


        // Is the first player winner? - for even matchups

        if ($players4[$j += 2] == $_POST['players5'][$i + 1]) {

            updateMatchupWinner($tournament_id, 4, $player2, 1);
        } else {

            updateMatchupWinner($tournament_id, 4, getPlayerId($players4[$j]), 0);
        }

        $j += 2;


        $counter++;

        $matchup_info = 'Round 5 Matchup ' . $counter;

        insertMatchup($player1, $player2, 5, $tournament_id, $counter, $matchup_info);


        // send email for each matchup players

        if (getSendEmail($tournament_id) && $size > 16) {

            $name1 = $_POST['players5'][$i];

            $name2 = $_POST['players5'][$i + 1];


            $rating1 = getPlayerRating($player1);

            $rating2 = getPlayerRating($player2);


            $email1 = getPlayerEmail($player1);

            $email2 = getPlayerEmail($player2);


            $player1Id = getPlayerId($name1);
            $player2Id = getPlayerId($name2);
            $matchupToken = getMatchupId($player1Id,$player2Id); //TODO add function to get playerid by name
            echo '<h1>' . $matchupToken . '</h1>';
//            $matchupToken = 1373;


            sendEmail($title, $name1, $name2, $rating1, $rating2, $email1, $email2, $matchupToken);

        }
    }


    $players5 = roundPlayers($tournament_id, 5);
}


//create matchup for round 6

if (!is_null($_POST['players6'])) {


    for ($i = 0; $i < count($_POST['players6']); $i += 2) {

        $player1 = getPlayerId($_POST['players6'][$i]);

        $player2 = getPlayerId($_POST['players6'][$i + 1]);


        // Is the first player winner? - for odd matchups

        if ($players5[$j] == $_POST['players6'][$i]) {

            updateMatchupWinner($tournament_id, 5, $player1, 1);
        } else {

            updateMatchupWinner($tournament_id, 5, getPlayerId($players5[$j]), 0);
        }


        // Is the first player winner? - for even matchups

        if ($players5[$j += 2] == $_POST['players6'][$i + 1]) {

            updateMatchupWinner($tournament_id, 5, $player2, 1);
        } else {

            updateMatchupWinner($tournament_id, 5, getPlayerId($players5[$j]), 0);
        }

        $j += 2;


        $counter++;

        $matchup_info = 'Round 6 Matchup ' . $counter;

        insertMatchup($player1, $player2, 6, $tournament_id, $counter, $matchup_info);


        // send email for each matchup players

        if (getSendEmail($tournament_id) && $size > 32) {

            $name1 = $_POST['players6'][$i];

            $name2 = $_POST['players6'][$i + 1];


            $rating1 = getPlayerRating($player1);

            $rating2 = getPlayerRating($player2);


            $email1 = getPlayerEmail($player1);

            $email2 = getPlayerEmail($player2);



            $player1Id = getPlayerId($name1);
            $player2Id = getPlayerId($name2);
            $matchupToken = getMatchupId($player1Id,$player2Id); //TODO add function to get playerid by name
            echo '<h1>' . $matchupToken . '</h1>';
//            $matchupToken = 1373;


            sendEmail($title, $name1, $name2, $rating1, $rating2, $email1, $email2, $matchupToken);

        }
    }


    $players6 = roundPlayers($tournament_id, 6);
}


//create matchup for round 7

if (!is_null($_POST['players7'])) {


    for ($i = 0; $i < count($_POST['players7']); $i += 2) {

        $player1 = getPlayerId($_POST['players7'][$i]);

        $player2 = getPlayerId($_POST['players7'][$i + 1]);


        // Is the first player winner? - for odd matchups

        if ($players6[$j] == $_POST['players7'][$i]) {

            updateMatchupWinner($tournament_id, 6, $player1, 1);
        } else {

            updateMatchupWinner($tournament_id, 6, getPlayerId($players6[$j]), 0);
        }


        // Is the first player winner? - for even matchups

        if ($players6[$j += 2] == $_POST['players7'][$i + 1]) {

            updateMatchupWinner($tournament_id, 6, $player2, 1);
        } else {

            updateMatchupWinner($tournament_id, 6, getPlayerId($players6[$j]), 0);
        }

        $j += 2;


        $counter++;

        $matchup_info = 'Round 7 Matchup ' . $counter;

        insertMatchup($player1, $player2, 7, $tournament_id, $counter, $matchup_info);
    }


    $players7 = roundPlayers($tournament_id, 7);
}


$winner_id = findFinal($tournament_id);

if ($winner_id != 0) {

    closeTournament($tournament_id, $winner_id);
}


?>


<!DOCTYPE html>

<html lang="en">


<head>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"/>

    <link rel="stylesheet" href="../styles/style.css"/>

    <?php

    if (isset($_GET['matchup_id'])) {

        $data_matchup = getMatchupData($_GET['matchup_id']);

        $matchup_round = $data_matchup["round"];

        $matchup_order = $data_matchup["matchup_order"];

        $id_str = "#round" . $matchup_round . "matchup" . $matchup_order;

        echo '<style>

                    ' . $id_str . '{border: 2px solid black;

                                    border-radius: 5px;

                                    }

                  </style>';
    }

    ?>

    <meta charset="UTF-8"/>

    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>

    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <title>Mando's Disc Golf Tournament Bracket</title>

</head>


<body class="d-flex flex-column">

<?php


if ($size == 16) {

    echo '<header class="bg-dark width-b-16">';
} elseif ($size == 32) {

    echo '<header class="bg-dark width-b-32">';
} elseif ($size == 64) {

    echo '<header class="bg-dark width-b-64">';
} else {

    echo '<header class="bg-dark width-b-16">';
}


?>


<div class="d-flex flex-column flex-sm-row align-items-center">

    <div><a class="navbar-brand" href="../index.php"><img src="../assets/mandos_redesign_3.webp" id="logo"
                                                          alt="mandos_redesign_3.webp"></a>

    </div>


    <div>

        <nav class="nav nav-pills flex-column flex-sm-row navbar-dark bg-dark">

            <a class="flex-sm-fill text-sm-center nav-link alink" href="../index.php">Home</a>

            <?php

            if (isset($_SESSION['username'])) {
                echo '<a class="flex-sm-fill text-sm-center nav-link alink" href="../player_search.php">Player Search</a>';
                echo '<a class="flex-sm-fill text-sm-center nav-link alink" href="../player_profile.php">Player Profile</a>';
                echo '<a class="flex-sm-fill text-sm-center nav-link alink" href="../seedings/seedings.php">Seedings</a>';
            }

            ?>

            <a class="flex-sm-fill text-sm-center nav-link alink new-active" href="brackets.php">Brackets</a>

        </nav>

    </div>

    <nav class="nav ms-auto">

        <?php

        if (!isset($_SESSION['username'])) {

            echo '<a class="text-sm-center nav-link alink" href="../login.php">Login</a>';
        } else {

            echo '<div id="loginName" class ="text-white">Hello ' . $_SESSION['username'] . '! </div>';

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

    <!--title and details-->

    <div class="d-flex flex-row justify-content-center">

        <div class="d-flex flex-column justify-content-center">


            <?php


            echo '<h3 class="text-center">' . $title . '</h3>';

            echo '</br>';

            echo '<h5 class="text-center">' . $details . '</h5>';


            echo '</div>';

            echo '</div>';
            echo '<br>';
            echo '<br>';

            ?>

            <?php


            if ($size == 16) {

                echo '<!-- Round titles-->
    <div class="d-flex flex-row flex-nowrap">
        <!--Round 1 column-->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 1)? "bg-secondary":"text-white bg-dark") . '">
                        Round 1
                    </li>
                </ul>
            </div>
        </div>

        <!-- Quarter-finals column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 2)? "bg-secondary":"text-white bg-dark") . '">
                        Quarter Finals
                    </li>
                </ul>
            </div>
        </div>

        <!-- Semi-finals column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 3)? "bg-secondary":"text-white bg-dark") . '">
                        Semi-Finals
                    </li>
                </ul>
            </div>
        </div>

        <!-- Final column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 4)? "bg-secondary":"text-white bg-dark") . '">
                        Finals
                    </li>
                </ul>
            </div>

        </div>


        <!-- Winner column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 5)? "bg-secondary":"text-white bg-dark") . '">
                        Champion
                    </li>
                </ul>
            </div>

        </div>
    </div>';
            } elseif ($size == 32) {

                echo '<!-- Round titles-->
    <div class="d-flex flex-row flex-nowrap">
        <!--Round 1 column-->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 1)? "bg-secondary":"text-white bg-dark") . '">
                        Round 1
                    </li>
                </ul>
            </div>
        </div>
        
        <!--Round 2 column-->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 2)? "bg-secondary":"text-white bg-dark") . '">
                        Round 2
                    </li>
                </ul>
            </div>
        </div>
        

        <!-- Quarter-finals column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 3)? "bg-secondary":"text-white bg-dark") . '">
                        Quarter Finals
                    </li>
                </ul>
            </div>
        </div>

        <!-- Semi-finals column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 4)? "bg-secondary":"text-white bg-dark") . '">
                        Semi-Finals
                    </li>
                </ul>
            </div>
        </div>

        <!-- Final column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 5)? "bg-secondary":"text-white bg-dark") . '">
                        Finals
                    </li>
                </ul>
            </div>

        </div>


        <!-- Winner column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 6)? "bg-secondary":"text-white bg-dark") . '">
                        Champion
                    </li>
                </ul>
            </div>

        </div>
    </div>';
            } elseif ($size == 64) {

                echo '<!-- Round titles-->
    <div class="d-flex flex-row flex-nowrap">
        <!--Round 1 column-->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 1)? "bg-secondary":"text-white bg-dark") . '">
                        Round 1
                    </li>
                </ul>
            </div>
        </div>
        
        <!--Round 2 column-->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 2)? "bg-secondary":"text-white bg-dark") . '">
                        Round 2
                    </li>
                </ul>
            </div>
        </div>
        
        <!--Round 3 column-->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 3)? "bg-secondary":"text-white bg-dark") . '">
                        Round 3
                    </li>
                </ul>
            </div>
        </div>

        <!-- Quarter-finals column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 4)? "bg-secondary":"text-white bg-dark") . '">
                        Quarter Finals
                    </li>
                </ul>
            </div>
        </div>

        <!-- Semi-finals column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 5)? "bg-secondary":"text-white bg-dark") . '">
                        Semi-Finals
                    </li>
                </ul>
            </div>
        </div>

        <!-- Final column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 6)? "bg-secondary":"text-white bg-dark") . '">
                        Finals
                    </li>
                </ul>
            </div>

        </div>


        <!-- Winner column -->
        <div class="d-flex flex-column">
            <div class="card" >
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-center ' . (($max_round > 7)? "bg-secondary":"text-white bg-dark") . '">
                        Champion
                    </li>
                </ul>
            </div>

        </div>
    </div>';
            } else {

                echo '<header class="bg-dark width-b-16">';
            }


            ?>

            <form name="bracket_page" method="POST" action="#">


                <?php

                echo '<input type="hidden" name="tournament_id" value="' . $tournament_id . '"/>';


                echo '<br>';

                echo '<br>';


                // ================================= 16 players bracket ===========================================

                if ($size == 16) {

                    if (!isset($_SESSION['username'])) {

                        echo '

    <!-- all columns are in 1 row flex-wrap-->

    <div class="d-flex flex-row flex-nowrap bracket16">

        <!--Round 1 column-->

        <div class="d-flex flex-column justify-content-around column-border">

            <div class="card" id="round1matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "winner" : "loser")) . '">' . $players1[0] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "winner" : "loser")) . '">' . $players1[1] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[1] . '">

            

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "winner" : "loser")) . '">' . $players1[2] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "winner" : "loser")) . '">' . $players1[3] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[2] . '">

            

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "winner" : "loser")) . '">' . $players1[4] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "winner" : "loser")) . '">' . $players1[5] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[3] . '">

             

                <ul class="list-group list-group-flush">

                 

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "winner" : "loser")) . '">' . $players1[6] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "winner" : "loser")) . '">' . $players1[7] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[4] . '">

             

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "winner" : "loser")) . '">' . $players1[8] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "winner" : "loser")) . '">' . $players1[9] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[5] . '">

              

                <ul class="list-group list-group-flush">

               

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "winner" : "loser")) . '">' . $players1[10] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "winner" : "loser")) . '">' . $players1[11] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[6] . '">

              

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "winner" : "loser")) . '">' . $players1[12] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "winner" : "loser")) . '">' . $players1[13] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[7] . '">

             

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "winner" : "loser")) . '">' . $players1[14] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "winner" : "loser")) . '">' . $players1[15] . '</li>

                </ul>

            </div>

        </div>



        <!-- Quarter-finals column -->

        <div class="d-flex flex-column justify-content-around column-border">

            <div class="card" id="round2matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[0] . '">

             

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[0]) . '</li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[1]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[1] . '">

              

                <ul class="list-group list-group-flush" >

                

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[2]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[3]) . '</li>                

				</ul>

            </div>



            <div class="card" id="round2matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[2] . '">

             

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[4]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[5]) . '</li>                

				</ul>

            </div>

            <div class="card" id="round2matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[3] . '">

                <ul class="list-group list-group-flush">

               

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[6]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[7]) . '</li>

				</ul>

            </div>

        </div>



        <!-- Semi-finals column -->

        <div class="d-flex flex-column justify-content-around column-border">

            <div class="card" id="round3matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[0]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[1]) . '</li>

                </ul>

            </div>

            <div class="card" id="round3matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[2]) . '</li>

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[3]) . '</li>

                </ul>

            </div>

        </div>



        <!-- Final column -->

        <div class="d-flex flex-column justify-content-around column-border">

            <div class="card" id="round4matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[0]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[1]) . '</li>

				</ul>

            </div>

        </div>





        <!-- winner column -->

        <div class="d-flex flex-column justify-content-around">

            <div class="card" id="winner">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item"> ' . (is_null($players5) ? "&nbsp;" : $players5[0]) . '<span>' . (is_null($players5) ? "<!--" : "") . ' <button type="button" class="btn btn-warning"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trophy" viewBox="0 0 16 16">

  <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935zM3.504 1c.007.517.026 1.006.056 1.469.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.501.501 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667.03-.463.049-.952.056-1.469H3.504z"/>

</svg> Winner</button>  ' . (is_null($players5) ? "-->" : "") . ' </span></li>

				</ul>

            </div>

        </div>

    </div>

    </br>

    </br>

';
                    } else {

                        echo '

    <!-- all columns are in 1 row flex-wrap-->

    <div class="d-flex flex-row flex-nowrap bracket16">

        <!--Round 1 column-->

        <div class="d-flex flex-column justify-content-around column-border">

            <div class="card" id="round1matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "winner" : "loser")) . '">' . $players1[0] . '<span><input type="radio" name="players2[0]"  value="' . $players1[0] . '"' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "winner" : "loser")) . '">' . $players1[1] . '<span><input type="radio" name="players2[0]"  value="' . $players1[1] . '"' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "checked disabled" : "disabled")) . '  ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[1] . '">

            

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "winner" : "loser")) . '">' . $players1[2] . '<span><input type="radio" name="players2[1]"  value="' . $players1[2] . '"' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "winner" : "loser")) . '">' . $players1[3] . '<span><input type="radio" name="players2[1]"  value="' . $players1[3] . '"' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "checked disabled" : "disabled")) . '  ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[2] . '">

            

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "winner" : "loser")) . '">' . $players1[4] . '<span><input type="radio" name="players2[2]"  value="' . $players1[4] . '"' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "winner" : "loser")) . '">' . $players1[5] . '<span><input type="radio" name="players2[2]"  value="' . $players1[5] . '"' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[3] . '">

             

                <ul class="list-group list-group-flush">

                 

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "winner" : "loser")) . '">' . $players1[6] . '<span><input type="radio" name="players2[3]"  value="' . $players1[6] . '"' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "winner" : "loser")) . '">' . $players1[7] . '<span><input type="radio" name="players2[3]"  value="' . $players1[7] . '"' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[4] . '">

             

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "winner" : "loser")) . '">' . $players1[8] . '<span><input type="radio" name="players2[4]"  value="' . $players1[8] . '"' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "winner" : "loser")) . '">' . $players1[9] . '<span><input type="radio" name="players2[4]"  value="' . $players1[9] . '"' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[5] . '">

              

                <ul class="list-group list-group-flush">

               

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "winner" : "loser")) . '">' . $players1[10] . '<span><input type="radio" name="players2[5]"  value="' . $players1[10] . '"' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "winner" : "loser")) . '">' . $players1[11] . '<span><input type="radio" name="players2[5]"  value="' . $players1[11] . '"' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[6] . '">

              

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "winner" : "loser")) . '">' . $players1[12] . '<span><input type="radio" name="players2[6]"  value="' . $players1[12] . '"' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "winner" : "loser")) . '">' . $players1[13] . '<span><input type="radio" name="players2[6]"  value="' . $players1[13] . '"' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[7] . '">

             

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "winner" : "loser")) . '">' . $players1[14] . '<span><input type="radio" name="players2[7]"  value="' . $players1[14] . '"' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "winner" : "loser")) . '">' . $players1[15] . '<span><input type="radio" name="players2[7]"  value="' . $players1[15] . '"' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

        </div>



        <!-- Quarter-finals column -->

        <div class="d-flex flex-column justify-content-around column-border">

            <div class="card" id="round2matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[0] . '">

             

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[0]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[0]"  value="' . $players2[0] . '"' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[1]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[0]"  value="' . $players2[1] . '"' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

            <div class="card" id="round2matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[1] . '">

              

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[2]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[1]"  value="' . $players2[2] . '"' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[3]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[1]"  value="' . $players2[3] . '"' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

				</ul>

            </div>



            <div class="card" id="round2matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[2] . '">

             

                <ul class="list-group list-group-flush">

                

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[4]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[2]"  value="' . $players2[4] . '"' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[5]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[2]"  value="' . $players2[5] . '"' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

				</ul>

            </div>

            <div class="card" id="round2matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[3] . '">

                <ul class="list-group list-group-flush">

               

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[6]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[3]"  value="' . $players2[6] . '"' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[7]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[3]"  value="' . $players2[7] . '"' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

				</ul>

            </div>

        </div>



        <!-- Semi-finals column -->

        <div class="d-flex flex-column justify-content-around column-border">

            <div class="card" id="round3matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[0]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[0]"  value="' . $players3[0] . '"' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[1]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[0]"  value="' . $players3[1] . '"' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

            <div class="card" id="round3matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[2]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[1]"  value="' . $players3[2] . '"' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[3]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[1]"  value="' . $players3[3] . '"' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

        </div>



        <!-- Final column -->

        <div class="d-flex flex-column justify-content-around column-border">

            <div class="card" id="round4matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[0]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[0]"  value="' . $players4[0] . '"' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[1]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[0]"  value="' . $players4[1] . '"' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>

				</ul>

            </div>

        </div>





        <!-- winner column -->

        <div class="d-flex flex-column justify-content-around">

            <div class="card" id="winner">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item"> ' . (is_null($players5) ? "&nbsp;" : $players5[0]) . '<span>' . (is_null($players5) ? "<!--" : "") . ' <button type="button" class="btn btn-warning"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trophy" viewBox="0 0 16 16">

  <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935zM3.504 1c.007.517.026 1.006.056 1.469.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.501.501 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667.03-.463.049-.952.056-1.469H3.504z"/>

</svg> Winner</button>  ' . (is_null($players5) ? "-->" : "") . ' </span></li>

				</ul>

            </div>

        </div>

    </div>

    </br>

    </br>

';
                    }


                    // ================================= 32 players bracket ===========================================

                } elseif ($size == 32) {

                    if (!isset($_SESSION['username'])) {

                        echo '

    <!-- all columns are in 1 row flex-wrap-->

    <div class="d-flex flex-row flex-nowrap bracket32">

        <!--Round 1 column-->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round1matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "winner" : "loser")) . '">' . $players1[0] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "winner" : "loser")) . '">' . $players1[1] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[1] . '">

            

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "winner" : "loser")) . '">' . $players1[2] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "winner" : "loser")) . '">' . $players1[3] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[2] . '">

            

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "winner" : "loser")) . '">' . $players1[4] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "winner" : "loser")) . '">' . $players1[5] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[3] . '">

             

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "winner" : "loser")) . '">' . $players1[6] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "winner" : "loser")) . '">' . $players1[7] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[4] . '">

             

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "winner" : "loser")) . '">' . $players1[8] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "winner" : "loser")) . '">' . $players1[9] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[5] . '">

              

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "winner" : "loser")) . '">' . $players1[10] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "winner" : "loser")) . '">' . $players1[11] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[6] . '">

              

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "winner" : "loser")) . '">' . $players1[12] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "winner" : "loser")) . '">' . $players1[13] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[7] . '">

             

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "winner" : "loser")) . '">' . $players1[14] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "winner" : "loser")) . '">' . $players1[15] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup9" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[8] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[16] == $players2[8] ? "winner" : "loser")) . '">' . $players1[16] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[17] == $players2[8] ? "winner" : "loser")) . '">' . $players1[17] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup10" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[9] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[18] == $players2[9] ? "winner" : "loser")) . '">' . $players1[18] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[19] == $players2[9] ? "winner" : "loser")) . '">' . $players1[19] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup11" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[10] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[20] == $players2[10] ? "winner" : "loser")) . '">' . $players1[20] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[21] == $players2[10] ? "winner" : "loser")) . '">' . $players1[21] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup12" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[11] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[22] == $players2[11] ? "winner" : "loser")) . '">' . $players1[22] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[23] == $players2[11] ? "winner" : "loser")) . '">' . $players1[23] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup13" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[12] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[24] == $players2[12] ? "winner" : "loser")) . '">' . $players1[24] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[25] == $players2[12] ? "winner" : "loser")) . '">' . $players1[25] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup14" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[13] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[26] == $players2[13] ? "winner" : "loser")) . '">' . $players1[26] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[27] == $players2[13] ? "winner" : "loser")) . '">' . $players1[27] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup15" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[14] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[28] == $players2[14] ? "winner" : "loser")) . '">' . $players1[28] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[29] == $players2[14] ? "winner" : "loser")) . '">' . $players1[29] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup16" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[15] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[30] == $players2[15] ? "winner" : "loser")) . '">' . $players1[30] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[31] == $players2[15] ? "winner" : "loser")) . '">' . $players1[31] . '</li>

                </ul>

            </div>

        </div>











        <!--Round 2 column-->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round2matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[0]) . '</li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[1]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[2]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[3]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[4]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[5]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[6]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[7]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[8] == $players3[4] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[8]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[9] == $players3[4] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[9]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[10] == $players3[5] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[10]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[11] == $players3[5] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[11]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[12] == $players3[6] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[12]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[13] == $players3[6] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[13]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[14] == $players3[7] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[14]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[15] == $players3[7] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[15]) . '</li>

                </ul>

            </div>

        </div>









        <!-- Quarter-finals column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round3matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[0]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[1]) . '</li>

                </ul>

            </div>

            <div class="card" id="round3matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[2]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[3]) . '</li>

                </ul>

            </div>



            <div class="card" id="round3matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[4] == $players4[2] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[4]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[5] == $players4[2] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[5]) . '</li>

                </ul>

            </div>

            <div class="card" id="round3matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[6] == $players4[3] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[6]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[7] == $players4[3] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[7]) . '</li>

                </ul>

            </div>

        </div>



        <!-- Semi-finals column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round4matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[0]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[1]) . '</li>

                </ul>

            </div>

            <div class="card" id="round4matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[2] == $players5[1] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[2]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[3] == $players5[1] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[3]) . '</li>

                </ul>

            </div>

        </div>



        <!-- Final column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round5matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info5[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[0] == $players6[0] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[0]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[1] == $players6[0] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[1]) . '</li>

                </ul>

            </div>

        </div>





        <!-- winner column -->

        <div class="d-flex flex-column bd-highlight justify-content-around">

            <div class="card" id="winner">

                <ul class="list-group list-group-flush">

                <li class="list-group-item"> ' . (is_null($players6) ? "&nbsp;" : $players6[0]) . '<span>' . (is_null($players6) ? "<!--" : "") . ' <button type="button" class="btn btn-warning"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trophy" viewBox="0 0 16 16">

                            <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935zM3.504 1c.007.517.026 1.006.056 1.469.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.501.501 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667.03-.463.049-.952.056-1.469H3.504z"/>

                        </svg> Winner</button>  ' . (is_null($players6) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

        </div>

    </div>

    </br>

    </br>';
                    } else {

                        echo '

    <!-- all columns are in 1 row flex-wrap-->

    <div class="d-flex flex-row flex-nowrap bracket32">

        <!--Round 1 column-->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round1matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "winner" : "loser")) . '">' . $players1[0] . '<span><input type="radio" name="players2[0]"  value="' . $players1[0] . '"' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "winner" : "loser")) . '">' . $players1[1] . '<span><input type="radio" name="players2[0]"  value="' . $players1[1] . '"' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "winner" : "loser")) . '">' . $players1[2] . '<span><input type="radio" name="players2[1]"  value="' . $players1[2] . '"' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "winner" : "loser")) . '">' . $players1[3] . '<span><input type="radio" name="players2[1]"  value="' . $players1[3] . '"' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "winner" : "loser")) . '">' . $players1[4] . '<span><input type="radio" name="players2[2]"  value="' . $players1[4] . '"' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "winner" : "loser")) . '">' . $players1[5] . '<span><input type="radio" name="players2[2]"  value="' . $players1[5] . '"' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "winner" : "loser")) . '">' . $players1[6] . '<span><input type="radio" name="players2[3]"  value="' . $players1[6] . '"' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "winner" : "loser")) . '">' . $players1[7] . '<span><input type="radio" name="players2[3]"  value="' . $players1[7] . '"' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "winner" : "loser")) . '">' . $players1[8] . '<span><input type="radio" name="players2[4]"  value="' . $players1[8] . '"' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "winner" : "loser")) . '">' . $players1[9] . '<span><input type="radio" name="players2[4]"  value="' . $players1[9] . '"' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "winner" : "loser")) . '">' . $players1[10] . '<span><input type="radio" name="players2[5]"  value="' . $players1[10] . '"' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "winner" : "loser")) . '">' . $players1[11] . '<span><input type="radio" name="players2[5]"  value="' . $players1[11] . '"' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "winner" : "loser")) . '">' . $players1[12] . '<span><input type="radio" name="players2[6]"  value="' . $players1[12] . '"' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "winner" : "loser")) . '">' . $players1[13] . '<span><input type="radio" name="players2[6]"  value="' . $players1[13] . '"' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "winner" : "loser")) . '">' . $players1[14] . '<span><input type="radio" name="players2[7]"  value="' . $players1[14] . '"' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "winner" : "loser")) . '">' . $players1[15] . '<span><input type="radio" name="players2[7]"  value="' . $players1[15] . '"' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup9" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[8] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[16] == $players2[8] ? "winner" : "loser")) . '">' . $players1[16] . '<span><input type="radio" name="players2[8]"  value="' . $players1[16] . '"' . (is_null($players2) ? "" : ($players1[16] == $players2[8] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[17] == $players2[8] ? "winner" : "loser")) . '">' . $players1[17] . '<span><input type="radio" name="players2[8]"  value="' . $players1[17] . '"' . (is_null($players2) ? "" : ($players1[17] == $players2[8] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup10" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[9] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[18] == $players2[9] ? "winner" : "loser")) . '">' . $players1[18] . '<span><input type="radio" name="players2[9]"  value="' . $players1[18] . '"' . (is_null($players2) ? "" : ($players1[18] == $players2[9] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[19] == $players2[9] ? "winner" : "loser")) . '">' . $players1[19] . '<span><input type="radio" name="players2[9]"  value="' . $players1[19] . '"' . (is_null($players2) ? "" : ($players1[19] == $players2[9] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup11" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[10] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[20] == $players2[10] ? "winner" : "loser")) . '">' . $players1[20] . '<span><input type="radio" name="players2[10]"  value="' . $players1[20] . '"' . (is_null($players2) ? "" : ($players1[20] == $players2[10] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[21] == $players2[10] ? "winner" : "loser")) . '">' . $players1[21] . '<span><input type="radio" name="players2[10]"  value="' . $players1[21] . '"' . (is_null($players2) ? "" : ($players1[21] == $players2[10] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup12" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[11] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[22] == $players2[11] ? "winner" : "loser")) . '">' . $players1[22] . '<span><input type="radio" name="players2[11]"  value="' . $players1[22] . '"' . (is_null($players2) ? "" : ($players1[22] == $players2[11] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[23] == $players2[11] ? "winner" : "loser")) . '">' . $players1[23] . '<span><input type="radio" name="players2[11]"  value="' . $players1[23] . '"' . (is_null($players2) ? "" : ($players1[23] == $players2[11] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup13" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[12] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[24] == $players2[12] ? "winner" : "loser")) . '">' . $players1[24] . '<span><input type="radio" name="players2[12]"  value="' . $players1[24] . '"' . (is_null($players2) ? "" : ($players1[24] == $players2[12] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[25] == $players2[12] ? "winner" : "loser")) . '">' . $players1[25] . '<span><input type="radio" name="players2[12]"  value="' . $players1[25] . '"' . (is_null($players2) ? "" : ($players1[25] == $players2[12] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup14" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[13] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[26] == $players2[13] ? "winner" : "loser")) . '">' . $players1[26] . '<span><input type="radio" name="players2[13]"  value="' . $players1[26] . '"' . (is_null($players2) ? "" : ($players1[26] == $players2[13] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[27] == $players2[13] ? "winner" : "loser")) . '">' . $players1[27] . '<span><input type="radio" name="players2[13]"  value="' . $players1[27] . '"' . (is_null($players2) ? "" : ($players1[27] == $players2[13] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup15" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[14] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[28] == $players2[14] ? "winner" : "loser")) . '">' . $players1[28] . '<span><input type="radio" name="players2[14]"  value="' . $players1[28] . '"' . (is_null($players2) ? "" : ($players1[28] == $players2[14] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[29] == $players2[14] ? "winner" : "loser")) . '">' . $players1[29] . '<span><input type="radio" name="players2[14]"  value="' . $players1[29] . '"' . (is_null($players2) ? "" : ($players1[29] == $players2[14] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup16" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[15] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[30] == $players2[15] ? "winner" : "loser")) . '">' . $players1[30] . '<span><input type="radio" name="players2[15]"  value="' . $players1[30] . '"' . (is_null($players2) ? "" : ($players1[30] == $players2[15] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[31] == $players2[15] ? "winner" : "loser")) . '">' . $players1[31] . '<span><input type="radio" name="players2[15]"  value="' . $players1[31] . '"' . (is_null($players2) ? "" : ($players1[31] == $players2[15] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

        </div>











        <!--Round 2 column-->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round2matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[0]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[0]"  value="' . $players2[0] . '"' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[1]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[0]"  value="' . $players2[1] . '"' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

            <div class="card" id="round2matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[2]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[1]"  value="' . $players2[2] . '"' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[3]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[1]"  value="' . $players2[3] . '"' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[4]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[2]"  value="' . $players2[4] . '"' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[5]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[2]"  value="' . $players2[5] . '"' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[6]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[3]"  value="' . $players2[6] . '"' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[7]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[3]"  value="' . $players2[7] . '"' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[8] == $players3[4] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[8]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[4]"  value="' . $players2[8] . '"' . (is_null($players3) ? "" : ($players2[8] == $players3[4] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[9] == $players3[4] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[9]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[4]"  value="' . $players2[9] . '"' . (is_null($players3) ? "" : ($players2[9] == $players3[4] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[10] == $players3[5] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[10]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[5]"  value="' . $players2[10] . '"' . (is_null($players3) ? "" : ($players2[10] == $players3[5] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[11] == $players3[5] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[11]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[5]"  value="' . $players2[11] . '"' . (is_null($players3) ? "" : ($players2[11] == $players3[5] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[12] == $players3[6] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[12]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[6]"  value="' . $players2[12] . '"' . (is_null($players3) ? "" : ($players2[12] == $players3[6] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[13] == $players3[6] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[13]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[6]"  value="' . $players2[13] . '"' . (is_null($players3) ? "" : ($players2[13] == $players3[6] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[14] == $players3[7] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[14]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[7]"  value="' . $players2[14] . '"' . (is_null($players3) ? "" : ($players2[14] == $players3[7] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[15] == $players3[7] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[15]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[7]"  value="' . $players2[15] . '"' . (is_null($players3) ? "" : ($players2[15] == $players3[7] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

        </div>









        <!-- Quarter-finals column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round3matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[0]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[0]"  value="' . $players3[0] . '"' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[1]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[0]"  value="' . $players3[1] . '"' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round3matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[2]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[1]"  value="' . $players3[2] . '"' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[3]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[1]"  value="' . $players3[3] . '"' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>



            <div class="card" id="round3matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[4] == $players4[2] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[4]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[2]"  value="' . $players3[4] . '"' . (is_null($players4) ? "" : ($players3[4] == $players4[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[5] == $players4[2] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[5]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[2]"  value="' . $players3[5] . '"' . (is_null($players4) ? "" : ($players3[5] == $players4[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round3matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[6] == $players4[3] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[6]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[3]"  value="' . $players3[6] . '"' . (is_null($players4) ? "" : ($players3[6] == $players4[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[7] == $players4[3] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[7]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[3]"  value="' . $players3[7] . '"' . (is_null($players4) ? "" : ($players3[7] == $players4[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

        </div>



        <!-- Semi-finals column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round4matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[0]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[0]"  value="' . $players4[0] . '"' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[1]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[0]"  value="' . $players4[1] . '"' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round4matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[2] == $players5[1] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[2]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[1]"  value="' . $players4[2] . '"' . (is_null($players5) ? "" : ($players4[2] == $players5[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[3] == $players5[1] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[3]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[1]"  value="' . $players4[3] . '"' . (is_null($players5) ? "" : ($players4[3] == $players5[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

        </div>



        <!-- Final column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round5matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info5[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[0] == $players6[0] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[0]) . '<span>' . (is_null($players5) ? "<!--" : "") . ' <input type="radio" name="players6[0]"  value="' . $players5[0] . '"' . (is_null($players6) ? "" : ($players5[0] == $players6[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players5) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[1] == $players6[0] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[1]) . '<span>' . (is_null($players5) ? "<!--" : "") . ' <input type="radio" name="players6[0]"  value="' . $players5[1] . '"' . (is_null($players6) ? "" : ($players5[1] == $players6[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players5) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

        </div>





        <!-- winner column -->

        <div class="d-flex flex-column bd-highlight justify-content-around">

            <div class="card" id="winner">

                <ul class="list-group list-group-flush">

                <li class="list-group-item"> ' . (is_null($players6) ? "&nbsp;" : $players6[0]) . '<span>' . (is_null($players6) ? "<!--" : "") . ' <button type="button" class="btn btn-warning"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trophy" viewBox="0 0 16 16">

                            <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935zM3.504 1c.007.517.026 1.006.056 1.469.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.501.501 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667.03-.463.049-.952.056-1.469H3.504z"/>

                        </svg> Winner</button>  ' . (is_null($players6) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

        </div>

    </div>

    </br>

    </br>';
                    }


                    // ================================= 64 players bracket ===========================================

                } elseif ($size == 64) {

                    if (!isset($_SESSION['username'])) {

                        echo '



    <!-- all columns are in 1 row flex-wrap-->

    <div class="d-flex flex-row flex-nowrap bracket64">



        <!--Round 1 column-->

       <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round1matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "winner" : "loser")) . '">' . $players1[0] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "winner" : "loser")) . '">' . $players1[1] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "winner" : "loser")) . '">' . $players1[2] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "winner" : "loser")) . '">' . $players1[3] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "winner" : "loser")) . '">' . $players1[4] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "winner" : "loser")) . '">' . $players1[5] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "winner" : "loser")) . '">' . $players1[6] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "winner" : "loser")) . '">' . $players1[7] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "winner" : "loser")) . '">' . $players1[8] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "winner" : "loser")) . '">' . $players1[9] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "winner" : "loser")) . '">' . $players1[10] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "winner" : "loser")) . '">' . $players1[11] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "winner" : "loser")) . '">' . $players1[12] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "winner" : "loser")) . '">' . $players1[13] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "winner" : "loser")) . '">' . $players1[14] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "winner" : "loser")) . '">' . $players1[15] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup9" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[8] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[16] == $players2[8] ? "winner" : "loser")) . '">' . $players1[16] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[17] == $players2[8] ? "winner" : "loser")) . '">' . $players1[17] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup10" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[9] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[18] == $players2[9] ? "winner" : "loser")) . '">' . $players1[18] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[19] == $players2[9] ? "winner" : "loser")) . '">' . $players1[19] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup11" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[10] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[20] == $players2[10] ? "winner" : "loser")) . '">' . $players1[20] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[21] == $players2[10] ? "winner" : "loser")) . '">' . $players1[21] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup12" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[11] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[22] == $players2[11] ? "winner" : "loser")) . '">' . $players1[22] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[23] == $players2[11] ? "winner" : "loser")) . '">' . $players1[23] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup13" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[12] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[24] == $players2[12] ? "winner" : "loser")) . '">' . $players1[24] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[25] == $players2[12] ? "winner" : "loser")) . '">' . $players1[25] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup14" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[13] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[26] == $players2[13] ? "winner" : "loser")) . '">' . $players1[26] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[27] == $players2[13] ? "winner" : "loser")) . '">' . $players1[27] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup15" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[14] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[28] == $players2[14] ? "winner" : "loser")) . '">' . $players1[28] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[29] == $players2[14] ? "winner" : "loser")) . '">' . $players1[29] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup16" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[15] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[30] == $players2[15] ? "winner" : "loser")) . '">' . $players1[30] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[31] == $players2[15] ? "winner" : "loser")) . '">' . $players1[31] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup17" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[16] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[32] == $players2[16] ? "winner" : "loser")) . '">' . $players1[32] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[33] == $players2[16] ? "winner" : "loser")) . '">' . $players1[33] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup18" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[17] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[34] == $players2[17] ? "winner" : "loser")) . '">' . $players1[34] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[35] == $players2[17] ? "winner" : "loser")) . '">' . $players1[35] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup19" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[18] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[36] == $players2[18] ? "winner" : "loser")) . '">' . $players1[36] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[37] == $players2[18] ? "winner" : "loser")) . '">' . $players1[37] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup20" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[19] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[38] == $players2[19] ? "winner" : "loser")) . '">' . $players1[38] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[39] == $players2[19] ? "winner" : "loser")) . '">' . $players1[39] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup21" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[20] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[40] == $players2[20] ? "winner" : "loser")) . '">' . $players1[40] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[41] == $players2[20] ? "winner" : "loser")) . '">' . $players1[41] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup22" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[21] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[42] == $players2[21] ? "winner" : "loser")) . '">' . $players1[42] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[43] == $players2[21] ? "winner" : "loser")) . '">' . $players1[43] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup23" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[22] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[44] == $players2[22] ? "winner" : "loser")) . '">' . $players1[44] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[45] == $players2[22] ? "winner" : "loser")) . '">' . $players1[45] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup24" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[23] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[46] == $players2[23] ? "winner" : "loser")) . '">' . $players1[46] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[47] == $players2[23] ? "winner" : "loser")) . '">' . $players1[47] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup25" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[24] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[48] == $players2[24] ? "winner" : "loser")) . '">' . $players1[48] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[49] == $players2[24] ? "winner" : "loser")) . '">' . $players1[49] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup26" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[25] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[50] == $players2[25] ? "winner" : "loser")) . '">' . $players1[50] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[51] == $players2[25] ? "winner" : "loser")) . '">' . $players1[51] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup27" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[26] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[52] == $players2[26] ? "winner" : "loser")) . '">' . $players1[52] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[53] == $players2[26] ? "winner" : "loser")) . '">' . $players1[53] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup28" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[27] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[54] == $players2[27] ? "winner" : "loser")) . '">' . $players1[54] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[55] == $players2[27] ? "winner" : "loser")) . '">' . $players1[55] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup29" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[28] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[56] == $players2[28] ? "winner" : "loser")) . '">' . $players1[56] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[57] == $players2[28] ? "winner" : "loser")) . '">' . $players1[57] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup30" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[29] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[58] == $players2[29] ? "winner" : "loser")) . '">' . $players1[58] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[59] == $players2[29] ? "winner" : "loser")) . '">' . $players1[59] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup31" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[30] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[60] == $players2[30] ? "winner" : "loser")) . '">' . $players1[60] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[61] == $players2[30] ? "winner" : "loser")) . '">' . $players1[61] . '</li>

                </ul>

            </div>

            <div class="card" id="round1matchup32" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[31] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[62] == $players2[31] ? "winner" : "loser")) . '">' . $players1[62] . '</li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[63] == $players2[31] ? "winner" : "loser")) . '">' . $players1[63] . '</li>

                </ul>

            </div>

        </div>









        <!--Round 2 column-->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round2matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[0]) . '</li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[1]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[2]) . '</li>                   

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[3]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[4]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[5]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[6]) . '</li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[7]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[8] == $players3[4] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[8]) . '</li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[9] == $players3[4] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[9]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[10] == $players3[5] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[10]) . '</li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[11] == $players3[5] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[11]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[12] == $players3[6] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[12]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[13] == $players3[6] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[13]) . '</li>                

               </ul>

            </div>

            <div class="card" id="round2matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[14] == $players3[7] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[14]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[15] == $players3[7] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[15]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup9" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[8] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[16] == $players3[8] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[16]) . '</li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[17] == $players3[8] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[17]) . '</li>

                </ul>

            </div>

            <div class="card" id="round2matchup10" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[9] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[18] == $players3[9] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[18]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[19] == $players3[9] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[19]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup11" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[10] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[20] == $players3[10] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[20]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[21] == $players3[10] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[21]) . '</li>               

                </ul>

            </div>

            <div class="card" id="round2matchup12" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[11] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[22] == $players3[11] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[22]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[23] == $players3[11] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[23]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup13" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[12] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[24] == $players3[12] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[24]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[25] == $players3[12] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[25]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup14" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[13] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[26] == $players3[13] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[26]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[27] == $players3[13] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[27]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup15" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[14] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[28] == $players3[14] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[28]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[29] == $players3[14] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[29]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round2matchup16" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[15] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[30] == $players3[15] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[30]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[31] == $players3[15] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[31]) . '</li>                

                </ul>

            </div>

        </div>









        <!--Round 3 column-->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round3matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "winner" : "loser")) . '">' . (is_null($players3) ? "&nbsp;" : $players3[0]) . '</li>

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "winner" : "loser")) . '"]>' . (is_null($players3) ? "&nbsp;" : $players3[1]) . '</li>

                </ul>

            </div>

            <div class="card" id="round3matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[2]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[3]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round3matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[4] == $players4[2] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[4]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[5] == $players4[2] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[5]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round3matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[6] == $players4[3] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[6]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[7] == $players4[3] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[7]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round3matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[8] == $players4[4] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[8]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[9] == $players4[4] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[9]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round3matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[10] == $players4[5] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[10]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[11] == $players4[5] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[11]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round3matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[12] == $players4[6] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[12]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[13] == $players4[6] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[13]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round3matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[14] == $players4[7] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[14]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[15] == $players4[7] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[15]) . '</li>                

                </ul>

            </div>

        </div>









        <!-- Quarter-finals column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round4matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[0]) . '</li>                   

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[1]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round4matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[2] == $players5[1] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[2]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[3] == $players5[1] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[3]) . '</li>                

                </ul>

            </div>



            <div class="card" id="round4matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[4] == $players5[2] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[4]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[5] == $players5[2] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[5]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round4matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[6] == $players5[3] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[6]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[7] == $players5[3] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[7]) . '</li>                

                </ul>

            </div>

        </div>



        <!-- Semi-finals column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round5matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info5[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[0] == $players6[0] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[0]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[1] == $players6[0] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[1]) . '</li>                

                </ul>

            </div>

            <div class="card" id="round5matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info5[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[2] == $players6[1] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[2]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[3] == $players6[1] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[3]) . '</li>                

                </ul>

            </div>

        </div>



        <!-- Final column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round6matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info6[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players7) ? "" : ($players6[0] == $players7[0] ? "winner" : "loser")) . '"> ' . (is_null($players6) ? "&nbsp;" : $players6[0]) . '</li>                    

                    <li class="list-group-item ' . (is_null($players7) ? "" : ($players6[1] == $players7[0] ? "winner" : "loser")) . '"> ' . (is_null($players6) ? "&nbsp;" : $players6[1]) . '</li>                

                </ul>

            </div>

        </div>





        <!-- winner column -->

        <div class="d-flex flex-column bd-highlight justify-content-around">

            <div class="card" id="winner">

                <ul class="list-group list-group-flush"> 

                    <li class="list-group-item"> ' . (is_null($players7) ? "&nbsp;" : $players7[0]) . '<span>' . (is_null($players7) ? "<!--" : "") . ' <button type="button" class="btn btn-warning"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trophy" viewBox="0 0 16 16">

                            <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935zM3.504 1c.007.517.026 1.006.056 1.469.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.501.501 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667.03-.463.049-.952.056-1.469H3.504z"/>

                        </svg> Winner</button>  ' . (is_null($players7) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

        </div>

    </div>

    </br>

    </br>';
                    } else {

                        echo '



    <!-- all columns are in 1 row flex-wrap-->

    <div class="d-flex flex-row flex-nowrap bracket64">



        <!--Round 1 column-->

       <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round1matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "winner" : "loser")) . '">' . $players1[0] . '<span><input type="radio" name="players2[0]"  value="' . $players1[0] . '"' . (is_null($players2) ? "" : ($players1[0] == $players2[0] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "winner" : "loser")) . '">' . $players1[1] . '<span><input type="radio" name="players2[0]"  value="' . $players1[1] . '"' . (is_null($players2) ? "" : ($players1[1] == $players2[0] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "winner" : "loser")) . '">' . $players1[2] . '<span><input type="radio" name="players2[1]"  value="' . $players1[2] . '"' . (is_null($players2) ? "" : ($players1[2] == $players2[1] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "winner" : "loser")) . '">' . $players1[3] . '<span><input type="radio" name="players2[1]"  value="' . $players1[3] . '"' . (is_null($players2) ? "" : ($players1[3] == $players2[1] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "winner" : "loser")) . '">' . $players1[4] . '<span><input type="radio" name="players2[2]"  value="' . $players1[4] . '"' . (is_null($players2) ? "" : ($players1[4] == $players2[2] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "winner" : "loser")) . '">' . $players1[5] . '<span><input type="radio" name="players2[2]"  value="' . $players1[5] . '"' . (is_null($players2) ? "" : ($players1[5] == $players2[2] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "winner" : "loser")) . '">' . $players1[6] . '<span><input type="radio" name="players2[3]"  value="' . $players1[6] . '"' . (is_null($players2) ? "" : ($players1[6] == $players2[3] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "winner" : "loser")) . '">' . $players1[7] . '<span><input type="radio" name="players2[3]"  value="' . $players1[7] . '"' . (is_null($players2) ? "" : ($players1[7] == $players2[3] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "winner" : "loser")) . '">' . $players1[8] . '<span><input type="radio" name="players2[4]"  value="' . $players1[8] . '"' . (is_null($players2) ? "" : ($players1[8] == $players2[4] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "winner" : "loser")) . '">' . $players1[9] . '<span><input type="radio" name="players2[4]"  value="' . $players1[9] . '"' . (is_null($players2) ? "" : ($players1[9] == $players2[4] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "winner" : "loser")) . '">' . $players1[10] . '<span><input type="radio" name="players2[5]"  value="' . $players1[10] . '"' . (is_null($players2) ? "" : ($players1[10] == $players2[5] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "winner" : "loser")) . '">' . $players1[11] . '<span><input type="radio" name="players2[5]"  value="' . $players1[11] . '"' . (is_null($players2) ? "" : ($players1[11] == $players2[5] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "winner" : "loser")) . '">' . $players1[12] . '<span><input type="radio" name="players2[6]"  value="' . $players1[12] . '"' . (is_null($players2) ? "" : ($players1[12] == $players2[6] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "winner" : "loser")) . '">' . $players1[13] . '<span><input type="radio" name="players2[6]"  value="' . $players1[13] . '"' . (is_null($players2) ? "" : ($players1[13] == $players2[6] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "winner" : "loser")) . '">' . $players1[14] . '<span><input type="radio" name="players2[7]"  value="' . $players1[14] . '"' . (is_null($players2) ? "" : ($players1[14] == $players2[7] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "winner" : "loser")) . '">' . $players1[15] . '<span><input type="radio" name="players2[7]"  value="' . $players1[15] . '"' . (is_null($players2) ? "" : ($players1[15] == $players2[7] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup9" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[8] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[16] == $players2[8] ? "winner" : "loser")) . '">' . $players1[16] . '<span><input type="radio" name="players2[8]"  value="' . $players1[16] . '"' . (is_null($players2) ? "" : ($players1[16] == $players2[8] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[17] == $players2[8] ? "winner" : "loser")) . '">' . $players1[17] . '<span><input type="radio" name="players2[8]"  value="' . $players1[17] . '"' . (is_null($players2) ? "" : ($players1[17] == $players2[8] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup10" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[9] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[18] == $players2[9] ? "winner" : "loser")) . '">' . $players1[18] . '<span><input type="radio" name="players2[9]"  value="' . $players1[18] . '"' . (is_null($players2) ? "" : ($players1[18] == $players2[9] ? "checked disabled" : "disabled")) . ' required ></span> </li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[19] == $players2[9] ? "winner" : "loser")) . '">' . $players1[19] . '<span><input type="radio" name="players2[9]"  value="' . $players1[19] . '"' . (is_null($players2) ? "" : ($players1[19] == $players2[9] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup11" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[10] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[20] == $players2[10] ? "winner" : "loser")) . '">' . $players1[20] . '<span><input type="radio" name="players2[10]"  value="' . $players1[20] . '"' . (is_null($players2) ? "" : ($players1[20] == $players2[10] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[21] == $players2[10] ? "winner" : "loser")) . '">' . $players1[21] . '<span><input type="radio" name="players2[10]"  value="' . $players1[21] . '"' . (is_null($players2) ? "" : ($players1[21] == $players2[10] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup12" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[11] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[22] == $players2[11] ? "winner" : "loser")) . '">' . $players1[22] . '<span><input type="radio" name="players2[11]"  value="' . $players1[22] . '"' . (is_null($players2) ? "" : ($players1[22] == $players2[11] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[23] == $players2[11] ? "winner" : "loser")) . '">' . $players1[23] . '<span><input type="radio" name="players2[11]"  value="' . $players1[23] . '"' . (is_null($players2) ? "" : ($players1[23] == $players2[11] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup13" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[12] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[24] == $players2[12] ? "winner" : "loser")) . '">' . $players1[24] . '<span><input type="radio" name="players2[12]"  value="' . $players1[24] . '"' . (is_null($players2) ? "" : ($players1[24] == $players2[12] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[25] == $players2[12] ? "winner" : "loser")) . '">' . $players1[25] . '<span><input type="radio" name="players2[12]"  value="' . $players1[25] . '"' . (is_null($players2) ? "" : ($players1[25] == $players2[12] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup14" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[13] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[26] == $players2[13] ? "winner" : "loser")) . '">' . $players1[26] . '<span><input type="radio" name="players2[13]"  value="' . $players1[26] . '"' . (is_null($players2) ? "" : ($players1[26] == $players2[13] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[27] == $players2[13] ? "winner" : "loser")) . '">' . $players1[27] . '<span><input type="radio" name="players2[13]"  value="' . $players1[27] . '"' . (is_null($players2) ? "" : ($players1[27] == $players2[13] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup15" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[14] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[28] == $players2[14] ? "winner" : "loser")) . '">' . $players1[28] . '<span><input type="radio" name="players2[14]"  value="' . $players1[28] . '"' . (is_null($players2) ? "" : ($players1[28] == $players2[14] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[29] == $players2[14] ? "winner" : "loser")) . '">' . $players1[29] . '<span><input type="radio" name="players2[14]"  value="' . $players1[29] . '"' . (is_null($players2) ? "" : ($players1[29] == $players2[14] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup16" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[15] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[30] == $players2[15] ? "winner" : "loser")) . '">' . $players1[30] . '<span><input type="radio" name="players2[15]"  value="' . $players1[30] . '"' . (is_null($players2) ? "" : ($players1[30] == $players2[15] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[31] == $players2[15] ? "winner" : "loser")) . '">' . $players1[31] . '<span><input type="radio" name="players2[15]"  value="' . $players1[31] . '"' . (is_null($players2) ? "" : ($players1[31] == $players2[15] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup17" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[16] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[32] == $players2[16] ? "winner" : "loser")) . '">' . $players1[32] . '<span><input type="radio" name="players2[16]"  value="' . $players1[32] . '"' . (is_null($players2) ? "" : ($players1[32] == $players2[16] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[33] == $players2[16] ? "winner" : "loser")) . '">' . $players1[33] . '<span><input type="radio" name="players2[16]"  value="' . $players1[33] . '"' . (is_null($players2) ? "" : ($players1[33] == $players2[16] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup18" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[17] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[34] == $players2[17] ? "winner" : "loser")) . '">' . $players1[34] . '<span><input type="radio" name="players2[17]"  value="' . $players1[34] . '"' . (is_null($players2) ? "" : ($players1[34] == $players2[17] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[35] == $players2[17] ? "winner" : "loser")) . '">' . $players1[35] . '<span><input type="radio" name="players2[17]"  value="' . $players1[35] . '"' . (is_null($players2) ? "" : ($players1[35] == $players2[17] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup19" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[18] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[36] == $players2[18] ? "winner" : "loser")) . '">' . $players1[36] . '<span><input type="radio" name="players2[18]"  value="' . $players1[36] . '"' . (is_null($players2) ? "" : ($players1[36] == $players2[18] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[37] == $players2[18] ? "winner" : "loser")) . '">' . $players1[37] . '<span><input type="radio" name="players2[18]"  value="' . $players1[37] . '"' . (is_null($players2) ? "" : ($players1[37] == $players2[18] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup20" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[19] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[38] == $players2[19] ? "winner" : "loser")) . '">' . $players1[38] . '<span><input type="radio" name="players2[19]"  value="' . $players1[38] . '"' . (is_null($players2) ? "" : ($players1[38] == $players2[19] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[39] == $players2[19] ? "winner" : "loser")) . '">' . $players1[39] . '<span><input type="radio" name="players2[19]"  value="' . $players1[39] . '"' . (is_null($players2) ? "" : ($players1[39] == $players2[19] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup21" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[20] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[40] == $players2[20] ? "winner" : "loser")) . '">' . $players1[40] . '<span><input type="radio" name="players2[20]"  value="' . $players1[40] . '"' . (is_null($players2) ? "" : ($players1[40] == $players2[20] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[41] == $players2[20] ? "winner" : "loser")) . '">' . $players1[41] . '<span><input type="radio" name="players2[20]"  value="' . $players1[41] . '"' . (is_null($players2) ? "" : ($players1[41] == $players2[20] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup22" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[21] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[42] == $players2[21] ? "winner" : "loser")) . '">' . $players1[42] . '<span><input type="radio" name="players2[21]"  value="' . $players1[42] . '"' . (is_null($players2) ? "" : ($players1[42] == $players2[21] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[43] == $players2[21] ? "winner" : "loser")) . '">' . $players1[43] . '<span><input type="radio" name="players2[21]"  value="' . $players1[43] . '"' . (is_null($players2) ? "" : ($players1[43] == $players2[21] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup23" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[22] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[44] == $players2[22] ? "winner" : "loser")) . '">' . $players1[44] . '<span><input type="radio" name="players2[22]"  value="' . $players1[44] . '"' . (is_null($players2) ? "" : ($players1[44] == $players2[22] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[45] == $players2[22] ? "winner" : "loser")) . '">' . $players1[45] . '<span><input type="radio" name="players2[22]"  value="' . $players1[45] . '"' . (is_null($players2) ? "" : ($players1[45] == $players2[22] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup24" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[23] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[46] == $players2[23] ? "winner" : "loser")) . '">' . $players1[46] . '<span><input type="radio" name="players2[23]"  value="' . $players1[46] . '"' . (is_null($players2) ? "" : ($players1[46] == $players2[23] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[47] == $players2[23] ? "winner" : "loser")) . '">' . $players1[47] . '<span><input type="radio" name="players2[23]"  value="' . $players1[47] . '"' . (is_null($players2) ? "" : ($players1[47] == $players2[23] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup25" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[24] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[48] == $players2[24] ? "winner" : "loser")) . '">' . $players1[48] . '<span><input type="radio" name="players2[24]"  value="' . $players1[48] . '"' . (is_null($players2) ? "" : ($players1[48] == $players2[24] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[49] == $players2[24] ? "winner" : "loser")) . '">' . $players1[49] . '<span><input type="radio" name="players2[24]"  value="' . $players1[49] . '"' . (is_null($players2) ? "" : ($players1[49] == $players2[24] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup26" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[25] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[50] == $players2[25] ? "winner" : "loser")) . '">' . $players1[50] . '<span><input type="radio" name="players2[25]"  value="' . $players1[50] . '"' . (is_null($players2) ? "" : ($players1[50] == $players2[25] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[51] == $players2[25] ? "winner" : "loser")) . '">' . $players1[51] . '<span><input type="radio" name="players2[25]"  value="' . $players1[51] . '"' . (is_null($players2) ? "" : ($players1[51] == $players2[25] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup27" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[26] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[52] == $players2[26] ? "winner" : "loser")) . '">' . $players1[52] . '<span><input type="radio" name="players2[26]"  value="' . $players1[52] . '"' . (is_null($players2) ? "" : ($players1[52] == $players2[26] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[53] == $players2[26] ? "winner" : "loser")) . '">' . $players1[53] . '<span><input type="radio" name="players2[26]"  value="' . $players1[53] . '"' . (is_null($players2) ? "" : ($players1[53] == $players2[26] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup28" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[27] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[54] == $players2[27] ? "winner" : "loser")) . '">' . $players1[54] . '<span><input type="radio" name="players2[27]"  value="' . $players1[54] . '"' . (is_null($players2) ? "" : ($players1[54] == $players2[27] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[55] == $players2[27] ? "winner" : "loser")) . '">' . $players1[55] . '<span><input type="radio" name="players2[27]"  value="' . $players1[55] . '"' . (is_null($players2) ? "" : ($players1[55] == $players2[27] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup29" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[28] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[56] == $players2[28] ? "winner" : "loser")) . '">' . $players1[56] . '<span><input type="radio" name="players2[28]"  value="' . $players1[56] . '"' . (is_null($players2) ? "" : ($players1[56] == $players2[28] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[57] == $players2[28] ? "winner" : "loser")) . '">' . $players1[57] . '<span><input type="radio" name="players2[28]"  value="' . $players1[57] . '"' . (is_null($players2) ? "" : ($players1[57] == $players2[28] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup30" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[29] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[58] == $players2[29] ? "winner" : "loser")) . '">' . $players1[58] . '<span><input type="radio" name="players2[29]"  value="' . $players1[58] . '"' . (is_null($players2) ? "" : ($players1[58] == $players2[29] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[59] == $players2[29] ? "winner" : "loser")) . '">' . $players1[59] . '<span><input type="radio" name="players2[29]"  value="' . $players1[59] . '"' . (is_null($players2) ? "" : ($players1[59] == $players2[29] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup31" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[30] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[60] == $players2[30] ? "winner" : "loser")) . '">' . $players1[60] . '<span><input type="radio" name="players2[30]"  value="' . $players1[60] . '"' . (is_null($players2) ? "" : ($players1[60] == $players2[30] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[61] == $players2[30] ? "winner" : "loser")) . '">' . $players1[61] . '<span><input type="radio" name="players2[30]"  value="' . $players1[61] . '"' . (is_null($players2) ? "" : ($players1[61] == $players2[30] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

            <div class="card" id="round1matchup32" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info1[31] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[62] == $players2[31] ? "winner" : "loser")) . '">' . $players1[62] . '<span><input type="radio" name="players2[31]"  value="' . $players1[62] . '"' . (is_null($players2) ? "" : ($players1[62] == $players2[31] ? "checked disabled" : "disabled")) . ' required ></span></li>

                    <li class="list-group-item ' . (is_null($players2) ? "" : ($players1[63] == $players2[31] ? "winner" : "loser")) . '">' . $players1[63] . '<span><input type="radio" name="players2[31]"  value="' . $players1[63] . '"' . (is_null($players2) ? "" : ($players1[63] == $players2[31] ? "checked disabled" : "disabled")) . ' required ></span></li>

                </ul>

            </div>

        </div>









        <!--Round 2 column-->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round2matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[0]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[0]"  value="' . $players2[0] . '"' . (is_null($players3) ? "" : ($players2[0] == $players3[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[1]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[0]"  value="' . $players2[1] . '"' . (is_null($players3) ? "" : ($players2[1] == $players3[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

            <div class="card" id="round2matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[2]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[1]"  value="' . $players2[2] . '"' . (is_null($players3) ? "" : ($players2[2] == $players3[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                   

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[3]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[1]"  value="' . $players2[3] . '"' . (is_null($players3) ? "" : ($players2[3] == $players3[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[4]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[2]"  value="' . $players2[4] . '"' . (is_null($players3) ? "" : ($players2[4] == $players3[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[5]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[2]"  value="' . $players2[5] . '"' . (is_null($players3) ? "" : ($players2[5] == $players3[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[6]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[3]"  value="' . $players2[6] . '"' . (is_null($players3) ? "" : ($players2[6] == $players3[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[7]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[3]"  value="' . $players2[7] . '"' . (is_null($players3) ? "" : ($players2[7] == $players3[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[8] == $players3[4] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[8]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[4]"  value="' . $players2[8] . '"' . (is_null($players3) ? "" : ($players2[8] == $players3[4] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[9] == $players3[4] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[9]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[4]"  value="' . $players2[9] . '"' . (is_null($players3) ? "" : ($players2[9] == $players3[4] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[10] == $players3[5] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[10]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[5]"  value="' . $players2[10] . '"' . (is_null($players3) ? "" : ($players2[10] == $players3[5] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[11] == $players3[5] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[11]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[5]"  value="' . $players2[11] . '"' . (is_null($players3) ? "" : ($players2[11] == $players3[5] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[12] == $players3[6] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[12]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[6]"  value="' . $players2[12] . '"' . (is_null($players3) ? "" : ($players2[12] == $players3[6] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[13] == $players3[6] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[13]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[6]"  value="' . $players2[13] . '"' . (is_null($players3) ? "" : ($players2[13] == $players3[6] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

               </ul>

            </div>

            <div class="card" id="round2matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[14] == $players3[7] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[14]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[7]"  value="' . $players2[14] . '"' . (is_null($players3) ? "" : ($players2[14] == $players3[7] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[15] == $players3[7] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[15]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[7]"  value="' . $players2[15] . '"' . (is_null($players3) ? "" : ($players2[15] == $players3[7] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup9" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[8] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[16] == $players3[8] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[16]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[8]"  value="' . $players2[16] . '"' . (is_null($players3) ? "" : ($players2[16] == $players3[8] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[17] == $players3[8] ? "winner" : "loser")) . '">' . (is_null($players2) ? "&nbsp;" : $players2[17]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[8]"  value="' . $players2[17] . '"' . (is_null($players3) ? "" : ($players2[17] == $players3[8] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

            <div class="card" id="round2matchup10" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[9] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[18] == $players3[9] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[18]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[9]"  value="' . $players2[18] . '"' . (is_null($players3) ? "" : ($players2[18] == $players3[9] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[19] == $players3[9] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[19]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[9]"  value="' . $players2[19] . '"' . (is_null($players3) ? "" : ($players2[19] == $players3[9] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup11" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[10] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[20] == $players3[10] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[20]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[10]"  value="' . $players2[20] . '"' . (is_null($players3) ? "" : ($players2[20] == $players3[10] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[21] == $players3[10] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[21]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[10]"  value="' . $players2[21] . '"' . (is_null($players3) ? "" : ($players2[21] == $players3[10] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>               

                </ul>

            </div>

            <div class="card" id="round2matchup12" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[11] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[22] == $players3[11] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[22]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[11]"  value="' . $players2[22] . '"' . (is_null($players3) ? "" : ($players2[22] == $players3[11] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[23] == $players3[11] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[23]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[11]"  value="' . $players2[23] . '"' . (is_null($players3) ? "" : ($players2[23] == $players3[11] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup13" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[12] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[24] == $players3[12] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[24]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[12]"  value="' . $players2[24] . '"' . (is_null($players3) ? "" : ($players2[24] == $players3[12] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[25] == $players3[12] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[25]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[12]"  value="' . $players2[25] . '"' . (is_null($players3) ? "" : ($players2[25] == $players3[12] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup14" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[13] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[26] == $players3[13] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[26]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[13]"  value="' . $players2[26] . '"' . (is_null($players3) ? "" : ($players2[26] == $players3[13] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[27] == $players3[13] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[27]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[13]"  value="' . $players2[27] . '"' . (is_null($players3) ? "" : ($players2[27] == $players3[13] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup15" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[14] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[28] == $players3[14] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[28]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[14]"  value="' . $players2[28] . '"' . (is_null($players3) ? "" : ($players2[28] == $players3[14] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[29] == $players3[14] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[29]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[14]"  value="' . $players2[29] . '"' . (is_null($players3) ? "" : ($players2[29] == $players3[14] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round2matchup16" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info2[15] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[30] == $players3[15] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[30]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[15]"  value="' . $players2[30] . '"' . (is_null($players3) ? "" : ($players2[30] == $players3[15] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players3) ? "" : ($players2[31] == $players3[15] ? "winner" : "loser")) . '"> ' . (is_null($players2) ? "&nbsp;" : $players2[31]) . '<span>' . (is_null($players2) ? "<!--" : "") . ' <input type="radio" name="players3[15]"  value="' . $players2[31] . '"' . (is_null($players3) ? "" : ($players2[31] == $players3[15] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players2) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

        </div>









        <!--Round 3 column-->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round3matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "winner" : "loser")) . '">' . (is_null($players3) ? "&nbsp;" : $players3[0]) . '<span>' . (is_null($players3) ? "<!--" : "") . '  <input type="radio" name="players4[0]"  value="' . $players3[0] . '"' . (is_null($players4) ? "" : ($players3[0] == $players4[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "winner" : "loser")) . '">' . (is_null($players3) ? "&nbsp;" : $players3[1]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[0]"  value="' . $players3[1] . '"' . (is_null($players4) ? "" : ($players3[1] == $players4[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

            <div class="card" id="round3matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[2]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[1]"  value="' . $players3[2] . '"' . (is_null($players4) ? "" : ($players3[2] == $players4[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[3]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[1]"  value="' . $players3[3] . '"' . (is_null($players4) ? "" : ($players3[3] == $players4[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round3matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[4] == $players4[2] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[4]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[2]"  value="' . $players3[4] . '"' . (is_null($players4) ? "" : ($players3[4] == $players4[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[5] == $players4[2] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[5]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[2]"  value="' . $players3[5] . '"' . (is_null($players4) ? "" : ($players3[5] == $players4[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round3matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[6] == $players4[3] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[6]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[3]"  value="' . $players3[6] . '"' . (is_null($players4) ? "" : ($players3[6] == $players4[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[7] == $players4[3] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[7]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[3]"  value="' . $players3[7] . '"' . (is_null($players4) ? "" : ($players3[7] == $players4[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round3matchup5" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[4] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[8] == $players4[4] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[8]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[4]"  value="' . $players3[8] . '"' . (is_null($players4) ? "" : ($players3[8] == $players4[4] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[9] == $players4[4] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[9]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[4]"  value="' . $players3[9] . '"' . (is_null($players4) ? "" : ($players3[9] == $players4[4] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round3matchup6" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[5] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[10] == $players4[5] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[10]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[5]"  value="' . $players3[10] . '"' . (is_null($players4) ? "" : ($players3[10] == $players4[5] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[11] == $players4[5] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[11]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[5]"  value="' . $players3[11] . '"' . (is_null($players4) ? "" : ($players3[11] == $players4[5] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round3matchup7" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[6] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[12] == $players4[6] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[12]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[6]"  value="' . $players3[12] . '"' . (is_null($players4) ? "" : ($players3[12] == $players4[6] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[13] == $players4[6] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[13]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[6]"  value="' . $players3[13] . '"' . (is_null($players4) ? "" : ($players3[13] == $players4[6] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round3matchup8" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info3[7] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[14] == $players4[7] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[14]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[7]"  value="' . $players3[14] . '"' . (is_null($players4) ? "" : ($players3[14] == $players4[7] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players4) ? "" : ($players3[15] == $players4[7] ? "winner" : "loser")) . '"> ' . (is_null($players3) ? "&nbsp;" : $players3[15]) . '<span>' . (is_null($players3) ? "<!--" : "") . ' <input type="radio" name="players4[7]"  value="' . $players3[15] . '"' . (is_null($players4) ? "" : ($players3[15] == $players4[7] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players3) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

        </div>









        <!-- Quarter-finals column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round4matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[0]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[0]"  value="' . $players4[0] . '"' . (is_null($players5) ? "" : ($players4[0] == $players5[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                   

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[1]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[0]"  value="' . $players4[1] . '"' . (is_null($players5) ? "" : ($players4[1] == $players5[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round4matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[2] == $players5[1] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[2]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[1]"  value="' . $players4[2] . '"' . (is_null($players5) ? "" : ($players4[2] == $players5[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[3] == $players5[1] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[3]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[1]"  value="' . $players4[3] . '"' . (is_null($players5) ? "" : ($players4[3] == $players5[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>



            <div class="card" id="round4matchup3" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[2] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[4] == $players5[2] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[4]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[2]"  value="' . $players4[4] . '"' . (is_null($players5) ? "" : ($players4[4] == $players5[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[5] == $players5[2] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[5]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[2]"  value="' . $players4[5] . '"' . (is_null($players5) ? "" : ($players4[5] == $players5[2] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

            <div class="card" id="round4matchup4" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info4[3] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[6] == $players5[3] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[6]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[3]"  value="' . $players4[6] . '"' . (is_null($players5) ? "" : ($players4[6] == $players5[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players5) ? "" : ($players4[7] == $players5[3] ? "winner" : "loser")) . '"> ' . (is_null($players4) ? "&nbsp;" : $players4[7]) . '<span>' . (is_null($players4) ? "<!--" : "") . ' <input type="radio" name="players5[3]"  value="' . $players4[7] . '"' . (is_null($players5) ? "" : ($players4[7] == $players5[3] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players4) ? "-->" : "") . ' </span></li>                

                </ul>

            </div>

        </div>



        <!-- Semi-finals column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round5matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info5[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[0] == $players6[0] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[0]) . '<span>' . (is_null($players5) ? "<!--" : "") . ' <input type="radio" name="players6[0]"  value="' . $players5[0] . '"' . (is_null($players6) ? "" : ($players5[0] == $players6[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players5) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[1] == $players6[0] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[1]) . '<span>' . (is_null($players5) ? "<!--" : "") . ' <input type="radio" name="players6[0]"  value="' . $players5[1] . '"' . (is_null($players6) ? "" : ($players5[1] == $players6[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players5) ? "-->" : "") . ' </span></li>                

                    </ul>

            </div>

            <div class="card" id="round5matchup2" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info5[1] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[2] == $players6[1] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[2]) . '<span>' . (is_null($players5) ? "<!--" : "") . ' <input type="radio" name="players6[1]"  value="' . $players5[2] . '"' . (is_null($players6) ? "" : ($players5[2] == $players6[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players5) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players6) ? "" : ($players5[3] == $players6[1] ? "winner" : "loser")) . '"> ' . (is_null($players5) ? "&nbsp;" : $players5[3]) . '<span>' . (is_null($players5) ? "<!--" : "") . ' <input type="radio" name="players6[1]"  value="' . $players5[3] . '"' . (is_null($players6) ? "" : ($players5[3] == $players6[1] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players5) ? "-->" : "") . ' </span></li>                

                    </ul>

            </div>

        </div>



        <!-- Final column -->

        <div class="d-flex flex-column bd-highlight justify-content-around column-border">

            <div class="card" id="round6matchup1" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $m_info6[0] . '">

                <ul class="list-group list-group-flush">

                    <li class="list-group-item ' . (is_null($players7) ? "" : ($players6[0] == $players7[0] ? "winner" : "loser")) . '"> ' . (is_null($players6) ? "&nbsp;" : $players6[0]) . '<span>' . (is_null($players6) ? "<!--" : "") . ' <input type="radio" name="players7[0]"  value="' . $players6[0] . '"' . (is_null($players7) ? "" : ($players6[0] == $players7[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players6) ? "-->" : "") . ' </span></li>                    

                    <li class="list-group-item ' . (is_null($players7) ? "" : ($players6[1] == $players7[0] ? "winner" : "loser")) . '"> ' . (is_null($players6) ? "&nbsp;" : $players6[1]) . '<span>' . (is_null($players6) ? "<!--" : "") . ' <input type="radio" name="players7[0]"  value="' . $players6[1] . '"' . (is_null($players7) ? "" : ($players6[1] == $players7[0] ? "checked disabled" : "disabled")) . ' required >' . (is_null($players6) ? "-->" : "") . ' </span></li>                

                    </ul>

            </div>

        </div>





        <!-- winner column -->

        <div class="d-flex flex-column bd-highlight justify-content-around">

            <div class="card" id="winner">

                <ul class="list-group list-group-flush"> 

                    <li class="list-group-item"> ' . (is_null($players7) ? "&nbsp;" : $players7[0]) . '<span>' . (is_null($players7) ? "<!--" : "") . ' <button type="button" class="btn btn-warning"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trophy" viewBox="0 0 16 16">

                            <path d="M2.5.5A.5.5 0 0 1 3 0h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356c.194.048.377.135.537.255L13.3 15.1a.5.5 0 0 1-.3.9H3a.5.5 0 0 1-.3-.9l1.838-1.379c.16-.12.343-.207.537-.255L6.5 13.11v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33.076 33.076 0 0 1 2.5.5zm.099 2.54a2 2 0 0 0 .72 3.935c-.333-1.05-.588-2.346-.72-3.935zm10.083 3.935a2 2 0 0 0 .72-3.935c-.133 1.59-.388 2.885-.72 3.935zM3.504 1c.007.517.026 1.006.056 1.469.13 2.028.457 3.546.87 4.667C5.294 9.48 6.484 10 7 10a.5.5 0 0 1 .5.5v2.61a1 1 0 0 1-.757.97l-1.426.356a.5.5 0 0 0-.179.085L4.5 15h7l-.638-.479a.501.501 0 0 0-.18-.085l-1.425-.356a1 1 0 0 1-.757-.97V10.5A.5.5 0 0 1 9 10c.516 0 1.706-.52 2.57-2.864.413-1.12.74-2.64.87-4.667.03-.463.049-.952.056-1.469H3.504z"/>

                        </svg> Winner</button>  ' . (is_null($players7) ? "-->" : "") . ' </span></li>

                </ul>

            </div>

        </div>

    </div>

    </br>

    </br>';
                    }


                    // ================================= Error ===========================================

                } else {

                    echo '<div class="p-3 m-10 bg-danger text-white"> Error: Start again from the seeding page! </div>';

                    echo '</div>';

                    echo '</div>';

                    echo '</article>';
                }


                if (isset($_SESSION['username'])) {

                    if ($winner_id == 0) {

                        echo '<button type="submit" value="Submit" onclick="formValidation()" class="btn btn-outline-dark m-5" id="button">Go To Next Round</button>';
                    }
                }


                ?>

            </form>

</article>

<!-- footer -->

<?php


if ($size == 16) {

    echo '<footer class="bg-dark width-b-16">';
} elseif ($size == 32) {

    echo '<footer class="bg-dark width-b-32">';
} elseif ($size == 64) {

    echo '<footer class="bg-dark width-b-64">';
} else {

    echo '<footer class="bg-dark width-b-16">';
}


?>


</br>

</br>

<div class="d-flex flex-row justify-content-center mb-3">

    <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.mandos-shop.com/" target="_blank"><img
                    src="../assets/globe.png" alt="globe.png"></a></div>

    <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.instagram.com/mandos_disc_golf/"
                                                        target="_blank"><img src="../assets/instagram.png"
                                                                             alt="instagram.png"></a></a></div>

    <div class="d-flex flex-column p-2 bd-highlight"><a href="https://www.facebook.com/Mandosdiscgolfshop/"
                                                        target="_blank"><img src="../assets/facebook.png"
                                                                             alt="facebook.png"></a></a></div>

</div>

</br>

</br>

</br>


<div class="d-flex flex-row justify-content-evenly mb-3">

    <div class="text-center p-3">

        © 2023 Copyright:

        <a class="text-secondary text-decoration-none" href="https://thepentagon.greenriverdev.com/">The Pentagon

            Team</a>

    </div>

</div>

</footer>

<script src="../scripts/script.js"></script>

<!--Bootstrap 5 JavaScript plugins and Popper.-->

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {

        return new bootstrap.Tooltip(tooltipTriggerEl)

    });


        const disabledRadios = document.querySelectorAll('input[type="radio"]:disabled');
        disabledRadios.forEach((radio) => {
            radio.style.display = 'none';
        });

</script>

</body>


</html>