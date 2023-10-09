<?php
    include_once 'includes/session.php';
    // call functions
    require 'functions/functions.php';


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        $result = checkAdmin($username, $password);
        if ($result) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
        }
    }

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
    <title>Mando's Disc Golf Tournaments User Login</title>
</head>
<body class="d-flex flex-column">
<header class="bg-dark">
    <div class="d-flex flex-column flex-sm-row align-items-center">
        <div>    <a class="navbar-brand" href="index.php"><img src="assets/mandos_redesign_3.webp" id="logo" alt="mandos_redesign_3.webp"></a>
        </div>

        <div>
            <nav class="nav nav-pills flex-column flex-sm-row navbar-dark bg-dark">
                <a class="flex-sm-fill text-sm-center nav-link alink new-active" href="index.php">Home</a>
                <?php
                    if(isset($_SESSION['username'])){
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
                   echo '<div id="loginName" class ="text-white">Hello '.$_SESSION['username'].'! </div>';
                   echo '<a class="text-sm-center nav-link alink" href="logout.php">Logout</a>';
                }
            ?>
        </nav>
    </div>

    <h1 class="bg-dark text-center headertext"> Mando's Disc Golf Tournaments</h1>

</header>
<!-- Login form -->
<br>
<br>
<br>
<div class="d-flex flex-row justify-content-center">

    <div class="d-flex flex-column justify-content-center">
    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $username = $_REQUEST['username'];
        $password = $_REQUEST['password'];

        $result = checkAdmin($username, $password);
        if(!$result){
            echo '<div class="alert alert-danger" role="alert">Username or Password is incorrect! Please try again.</div>';
        } else {
            $_SESSION['username'] = $username;
//            header("Location: index.php");
        }
    }
    ?>
        <form class = "container" method = "POST" action = "login.php">
            <div class="row gx-2 gy-2 input-group pb-2">

                <div class="form-floating mb-2">
                    <input type="text" name = "username" class="form-control" id="username"
                           value = "<?php if($_SERVER['REQUEST_METHOD'] == 'POST'){
                               echo $_POST['username'];
                           }
                           ?>">
                    <label for="username" class="form-label">Username</label>
                </div>
                <div class="form-floating mb-2">
                    <input type="password" name = "password" class="form-control" id="password">
                    <label for="password" class="form-label">Password</label>
                </div>
            </div>

            <button type="submit" class="btn btn-outline-dark">Login</button>
        </form>
</div>
</div>


<br>
<br>
<br>
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