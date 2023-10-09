<?php
    include('test/simple_html_dom.php'); //TODO: Turn this back on later
    require $_SERVER['DOCUMENT_ROOT'].'/../db.php';




// insert player data into database and return
    // players_id
    function insertPlayer($name, $rating, $email, $pdganumber) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = "CALL sp_insertPlayer(N'" . $name . "', " . $rating . ", N'" . $email . "', " . $pdganumber .");";

        $result = @mysqli_query($cnxn, $sql);

        $players_id = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $players_id = $row['player_id'];
        }

        return intval($players_id);
    }



    // insert tournament data into database and return
    // tournament_id
    function insertTournament($size, $title, $details = "", $send_email = 0, $is_active = 1) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_insertTournament(' . $size . ', "' . $title . '", "' . $details . '", ' . $send_email . ', ' . $is_active .');';

        $result = @mysqli_query($cnxn, $sql);


        $tournament_id = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $tournament_id = $row['tournament_id'];
        }

        return intval($tournament_id);
    }


    // insert matchup data into database and return
    // matchup_id
    function insertMatchup($player1, $player2, $round, $tournament_id, $matchup_order, $info) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_insertMatchup(' . $player1 . ', ' . $player2 . ', ' . $round . ', ' . $tournament_id . ', ' . $matchup_order . ', "' . $info . '");';

        $result = @mysqli_query($cnxn, $sql);


        /*$matchup_id = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $matchup_id = $row['matchup_id'];
        }

        return intval($matchup_id);*/
    }

    // update tournament and set the winner
    function tournamentWinner($tournament_id, $winner_name) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_updateTournament(' . $tournament_id . ', "' . $winner_name .'");';

        $result = @mysqli_query($cnxn, $sql);

    }


    // update tournament and set the winner
    function roundPlayers($tournament_id, $round) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getRoundPlayers(' . $tournament_id . ', ' . $round .');';

        $result = @mysqli_query($cnxn, $sql);

        $players = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $players[] = $row['name'];

        }

        return $players;

    }


    // get player_id by its name
    function getPlayerId($name) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getPlayerId("' . $name .'");';

        $result = @mysqli_query($cnxn, $sql);

        $player_id = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $player_id = $row['player_id'];

        }

        return intval($player_id);
    }


    // get send_email value for a tournament
    function getSendEmail($tournament_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getSendEmail(' . $tournament_id .');';

        $result = @mysqli_query($cnxn, $sql);

        $send_email = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $send_email = $row['send_email'];

        }

        return (bool)$send_email;
    }

    // get player_id by its name
    function getPlayerRating($player_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getPlayerRating(' . $player_id . ');';

        $result = @mysqli_query($cnxn, $sql);

        $player_rating = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $player_rating = $row['rating'];

        }

        return intval($player_rating);
    }

    // get player_id by its name
    function getPlayerEmail($player_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getPlayerEmail(' . $player_id . ');';

        $result = @mysqli_query($cnxn, $sql);

        $email = "";
        while ($row = mysqli_fetch_assoc($result)) {
            $email = $row['email'];

        }

        return $email;
    }

    // send the same email for 2 players
    function sendEmail($title, $name1, $name2, $rating1, $rating2, $email1, $email2, $matchupToken) {

            $header = 'From: The Pentagon Dev Team <@outlook.com>\r\n';
            $header .= 'Reply-To: pentagongrc@outlook.com\r\n';
            $header .= "MIME-Version: 1.0" . "\r\n";
            $header .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                $to = $email1.','. $email2;
                $message = '<h3>Hi there!</h3><p>You are receiving this email because someone has signed you up for the upcoming Disc Golf Competition, '. $title. ', ' . $name1 . ' with ' . $rating1  .' rating number will be competing against '.  $name2 .' with '. $rating2 . ' rating number .</p>, <p>Click here to report the score . <a href="http://tloudon.greenriverdev.com.greenriverdev.com/MandosDiscGolf/brackets/setScores.php?matchup='.$matchupToken.'">Set Score</a></p>';
                mail($to,$title, $message, $header);
    }

    // get title value for a tournament
    function getTitle($tournament_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getTournamentTitle(' . $tournament_id .');';

        $result = @mysqli_query($cnxn, $sql);

        $title = "";
        while ($row = mysqli_fetch_assoc($result)) {
            $title = $row['title'];

        }

        return $title;
    }

    // get size value for a tournament
    function getSize($tournament_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getTournamentSize(' . $tournament_id .');';

        $result = @mysqli_query($cnxn, $sql);

        $size = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $size = $row['size'];

        }

        return intval($size);
    }

    // get details value for a tournament
    function getDetails($tournament_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getTournamentDetails(' . $tournament_id .');';

        $result = @mysqli_query($cnxn, $sql);

        $details = "";
        while ($row = mysqli_fetch_assoc($result)) {
            $details = $row['details'];

        }

        return $details;
    }


    // get player_id by its name
    function getPlayerName($player_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getPlayerNameById(' . $player_id . ');';

        $result = @mysqli_query($cnxn, $sql);

        $name = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row['name'];

        }

        return $name;
    }

    //get matchup by player names
    function getMatchupId($name1, $name2) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getMatchupId(' . $name1 . ', ' . $name2 . ');';

        $result = @mysqli_query($cnxn, $sql);

        $matchup = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $matchup = $row['matchup_id'];

        }

        return $matchup;
    }

    // get player_id by its name
    function sortForBracket($players) {
        $sorted_array = array();

        //helper arrays
        $order16 = array(1, 16, 8, 9, 4, 13, 5, 12, 2, 15, 7, 10, 3, 14, 6, 11);
        $order32 = array(1, 32, 16, 17, 9, 24, 8, 25, 4, 29, 13, 20, 12, 21, 5, 28, 2, 31, 15, 18, 10, 23, 7, 26, 3, 30, 14, 19, 11, 22, 6, 27);
        $order64 = array(1, 64, 32, 33, 17, 48, 16, 49, 9, 56, 24, 41, 25, 40, 8, 57, 4, 61, 29, 36, 20, 45, 13, 52, 12, 53, 21, 44, 28, 37, 5, 60, 2, 63, 31, 34, 18, 47, 15, 50, 10, 55, 23, 42, 26, 39, 7, 58, 3, 62, 30, 35, 19, 46, 14, 51, 11, 54, 22, 43, 27, 38, 6, 59);

        if (count($players) == 16){
            foreach ($order16 as $x) {
                $sorted_array[] = $players[$x-1];
            }
        } elseif (count($players) == 32) {
            foreach ($order32 as $x) {
                $sorted_array[] = $players[$x-1];
            }
        } elseif (count($players) == 64) {
            foreach ($order64 as $x) {
                $sorted_array[] = $players[$x-1];
            }
        } else {
            $sorted_array = null;
        }

        return $sorted_array;
    }


    // set the matchup winner
    function updateMatchupWinner($tournament_id, $round, $player_id, $first_winner) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_updateMatchupWinner(' . $tournament_id . ', ' . $round . ', ' . $player_id . ', ' . $first_winner . ');';

        $result = @mysqli_query($cnxn, $sql);
    }



    // find the final matchup of a tournament
    function findFinal($tournament_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_findFinalWinner(' . $tournament_id .');';

        $result = @mysqli_query($cnxn, $sql);

        $player1 = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $player1 = $row['player1'];
        }

        return intval($player1);
    }

    // set the tournament winner and deactive the tournament
    function closeTournament($tournament_id, $winner_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_updateTournament(' . $tournament_id .', '. $winner_id .');';

        $result = @mysqli_query($cnxn, $sql);
    }


    // get 10 random matchup data
    function getTenMatchup() {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getTenRandomMatchup();';

        $result = @mysqli_query($cnxn, $sql);

        $matchup_array = array();
        while ($row = mysqli_fetch_assoc($result)) {

            $matchup = array("player1" => $row['player1'], "player2" => $row['player2'], "title" => $row['title'], "round" => $row['round'], "tournament_id" => $row['tournament_id'], "matchup_id" => $row['matchup_id'], "information" => $row['information']);

            $matchup_array[] = $matchup;

        }

        return $matchup_array;
    }


    // get All Tournaments
    function getAllTournaments() {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getAllTournaments();';

        $result = @mysqli_query($cnxn, $sql);

        $tournament_array = array();
        while ($row = mysqli_fetch_assoc($result)) {

            $tournament = array("title" => $row['title'], "size" => $row['size'], "is_active" => $row['is_active'], "tournament_id" => $row['tournament_id']);

            $tournament_array[] = $tournament;

        }

        return $tournament_array;
    }

    // get the max round number of a tournament
    function getMaxRound($tournament_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getMaxRound(' . $tournament_id .');';

        $result = @mysqli_query($cnxn, $sql);

        $max_round = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $max_round = $row['round'];
        }

        return intval($max_round);
    }

    // validate name
    function validateName($name) {
        // Only allow alphabetical characters, spaces, dashes, and single quotes
        $pattern = "/^[a-zA-Z '-]+$/";

        // Use preg_match to check if the input matches the pattern
        return preg_match($pattern, $name);
    }

    // validate email
    function validateEmail($name, $email) {
        // If the name is "Bye" or "bye", email is not required
        if (strtolower($name) === "bye") {
            return true;
        }

        // If email is not provided, return false
        if (empty($email)) {
            return false;
        }

        // Validate the email address using PHP filter_var() function
        $valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);

        // Return true if the email address is valid, false otherwise
        return $valid_email !== false;
    }

    // validate rating
    function validateRating($rating) {
        // Make sure the rating is an integer
        if (!is_int($rating)) {
            return false;
        }

        // Make sure the rating is between 0 and 1200 inclusive
        if ($rating < 0 || $rating > 1200) {
            return false;
        }

        // If the rating is valid, return true
        return true;
    }

    // validate PDGA number
    function validatePDGANumber($pdgaNumber) {
        // If PDGA number is not provided, return true
        if (empty($pdgaNumber)) {
            return true;
        }

        // Make sure the PDGA number is an integer
        if (!is_int($pdgaNumber)) {
            return false;
        }

        // Make sure the PDGA number is between 0 and 1000000 inclusive
        if ($pdgaNumber < 0 || $pdgaNumber > 1000000) {
            return false;
        }

        // If the PDGA number is valid, return true
        return true;
    }

    // get matchup data to change its border
    function getMatchupData($matchup_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getMatchupData(' . $matchup_id . ');';

        $result = @mysqli_query($cnxn, $sql);

        $matchup_array = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $matchup_array = array("round" => $row['round'], "matchup_order" => $row['matchup_order']);

        }

        return $matchup_array;
    }

    // get matchups data to edit its information
    function getMatchupInfo($tournament_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getMatchupInfo(' . $tournament_id . ');';

        $result = @mysqli_query($cnxn, $sql);

        $matchup_array = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $matchup = array("matchup_id" => $row['matchup_id'], "player1" => $row['player1'], "player2" => $row['player2'], "information" => $row['information']);
            $matchup_array[] = $matchup;
        }

        return $matchup_array;
    }

    // update Matchup Info
    function updateMatchupInfo($matchup_id, $info) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_updateMatchupInfo(' . $matchup_id . ', "' . $info .'");';

        $result = @mysqli_query($cnxn, $sql);
    }

    // checks to see if the username and password match admin credentials
    function checkAdmin($username, $password){
        if($username === "admin" and $password === "password"){
            return true;
        } else {
            return false;
        }
    }


    // get just matchup info
    function getOnlyInfo($tournament_id) {
        // connect to database
        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';


        $sql = 'CALL sp_getOnlyInfo(' . $tournament_id . ');';

        $result = @mysqli_query($cnxn, $sql);

        $matchup_info = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $matchup_info[] = $row['information'];
        }

        return $matchup_info;
    }


//    function search_players($name, $pdga_number)
//    {
//        require $_SERVER['DOCUMENT_ROOT'].'/../db.php';
//
//        // prepare SQL statement
//        $sql = "SELECT * FROM player WHERE name='%$name%' OR pdga_number='$pdga_number'";
//        $result = $mysqli->query($sql);
//
//        //echo "<td>hello world</td>";
//
//        // print results in table
//        while ($row = $result->fetch_assoc()) {
//                echo "<tr>";
//                echo "<td>" . $row['name'] . "</td>";
//                echo "<td>" . $row['pdga_number'] . "</td>";
//                echo "<td>" . $row['rating'] . "</td>";
//                echo "<td>" . $row['email'] . "</td>";
//                echo "</tr>";
//        }
//
//    }

function search_players($name)
{
    require $_SERVER['DOCUMENT_ROOT'].'/../db.php';

    // prepare SQL statement
//    $sql = 'CALL sp_searchPlayer(' . $name . ');';
    $query = "'%' '$name' '%'";

    $sql = "SELECT * FROM player WHERE name LIKE CONCAT($query);";

    //echo $sql;
    $result = @mysqli_query($cnxn, $sql);


    // print results in table
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr class='clickable-row'>";
        echo "<td id='nameColumnValue'>" . $row['name'] . "</td>";
        echo "<td id='pdgaNumberColumnValue'>" . $row['pdga_number'] . "</td>";
        echo "<td>" . $row['rating'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>";
        echo "<a href='player_profile.php?name=" . urlencode($row['name']) . "&pdga_number=" . urlencode($row['pdga_number']) . "&rating=" . urlencode($row['rating']) . "&email=" . urlencode($row['email']) . "'>Save</a>";
        echo "</td>";
        echo "</tr>";
    }

}

function getRating($pdgaNumber, $name)
{
    require $_SERVER['DOCUMENT_ROOT'].'/../db.php';

    $nameQuery = "'%' '$name' '%'";

    $sql = "SELECT * FROM player WHERE pdga_number = '$pdgaNumber' AND name LIKE $nameQuery";

    $result = @mysqli_query($cnxn, $sql);
    $row = mysqli_fetch_assoc($result);
    echo $row['rating'];

}
function getEmail($pdgaNumber, $name)
{
    require $_SERVER['DOCUMENT_ROOT'].'/../db.php';

    $nameQuery = "'%' '$name' '%'";

    $sql = "SELECT * FROM player WHERE pdga_number = '$pdgaNumber' AND name LIKE $nameQuery";

    $result = @mysqli_query($cnxn, $sql);
    $row = mysqli_fetch_assoc($result);
    echo $row['email'];

}

function getName($pdgaNumber, $name)
{
    require $_SERVER['DOCUMENT_ROOT'].'/../db.php';

    $query = "'%' '$pdgaNumber' '%'";
    $nameQuery = "'%' '$name' '%'";


    $sql = "SELECT * FROM player WHERE pdga_number = '$pdgaNumber' AND name LIKE $nameQuery";

    $result = @mysqli_query($cnxn, $sql);
    $row = mysqli_fetch_assoc($result);
    echo $row['name'];

}

function getPDGA($name)
{
    require $_SERVER['DOCUMENT_ROOT'].'/../db.php';

    $nameQuery = "'%' '$name' '%'";


    $sql = "SELECT * FROM player WHERE pdga_number = '$pdgaNumber' AND name LIKE $nameQuery";

    $result = @mysqli_query($cnxn, $sql);
    $row = mysqli_fetch_assoc($result);
    echo $row['name'];

}

function getPlayerImage($pdgaNumber)
{
    $html = file_get_html('https://www.pdga.com/player/'.$pdgaNumber);
    echo $html->find('img', 1);
}

?>
