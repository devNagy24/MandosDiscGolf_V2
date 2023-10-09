<?php
    if(!isset($_SESSION['username'])){
        header("Location: ../bracket_view.php");
    }
?>