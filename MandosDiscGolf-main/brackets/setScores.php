<!--Send an email to a player with a link that takes them here. The email will be sent when the tournament is originally created.-->
<!--This page will allow them to input the final score of the game. -->
<!--This page should only have the ability for the player to enter in the score of their current matchup.-->
<!--Score should match between the 2 players. If not then the admin will have to input the final score. -->
<!--Once all of the scores have been collected through this page then the next round of the tournament can be determined.-->

<?php
//connect to the database
require $_SERVER['DOCUMENT_ROOT'].'/../db.php';
// Retrieve the matchup token from the query parameters
$matchupToken = $_GET['matchup'];

$query = "SELECT * FROM matchup WHERE matchup_id = '$matchupToken'";
$result = mysqli_query($cnxn, $query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = $_POST['score'];

    // Update the matchup in the database with the entered score
    $query = "UPDATE matchup SET score = '$score' WHERE matchup_id = $matchupToken";
    $updateResult = mysqli_query($cnxn, $query);

    if ($updateResult) {
       echo "player 2 score update successfully";
    } else {
        echo "player 2 score update failed";
    }

    $score2 = $_POST['score2'];

    // Update the matchup in the database with the entered score
    $query = "UPDATE matchup SET score2 = '$score2' WHERE matchup_id = $matchupToken";
    $updateResult = mysqli_query($cnxn, $query);

    if ($updateResult) {
        echo "player 2 score update successfully";
    } else {
        echo "player 2 score update failed";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Score</title>
</head>
<body>
<h1>Set Score</h1>
<form method="POST" action="">
    <label for="score">Enter Player 1 Score:</label>
    <input type="text" name="score" id="score" required>

    <label for="score2">Enter Player 2 Score:</label>
    <input type="text" name="score2" id="score2" required>
    <button type="submit">Submit</button>
</form>
</body>
</html>








