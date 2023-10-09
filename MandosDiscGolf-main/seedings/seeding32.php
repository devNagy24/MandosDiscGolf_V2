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
    <title>Seeding for 32 players </title>
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
                echo '<a class="flex-sm-fill text-sm-center nav-link alink new-active" href="seedings.php">Seedings</a>';
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
        name="form1"
        method="POST"
        action="ranking.php"
        novalidate
>
	<!--Player 0-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
            type="text"
            class="form-control"
            placeholder="Name"
            aria-label="name"
            name="players[0][name]"
            pattern=".{1,}"
            required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
            type="number"
            class="form-control"
            min="0"
            max="1200"
            placeholder="Rating"
            aria-label="rating"
            name="players[0][rating]"
            required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
            type="number"
            class="form-control"
            min="0"
            max="1000000"
            placeholder="PDGA Number"
            aria-label="pdganumber"
            name="players[0][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
            type="email"
            class="form-control"
            placeholder="example@example.com"
            aria-label="email"
            name="players[0][email]"
            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
            required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 0-->

      <!--Player 1-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
            type="text"
            class="form-control"
            placeholder="Name"
            aria-label="name"
            name="players[1][name]"
            pattern=".{1,}"
            required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
            type="number"
            class="form-control"
            min="0"
            max="1200"
            placeholder="Rating"
            aria-label="rating"
            name="players[1][rating]"
            required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
            type="number"
            class="form-control"
            min="0"
            max="1000000"
            placeholder="PDGA Number"
            aria-label="pdganumber"
            name="players[1][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
            type="email"
            class="form-control"
            placeholder="example@example.com"
            aria-label="email"
            name="players[1][email]"
            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
            required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 1-->


      <!--Player 2-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[2][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[2][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[2][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[2][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 2-->

      <!--Player 3-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[3][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[3][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[3][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[3][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 3-->


      <!--Player 4-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[4][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[4][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[4][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[4][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 4-->

      <!--Player 5-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[5][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[5][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[5][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[5][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 5-->



      <!--Player 6-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[6][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[6][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[6][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[6][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 6-->

      <!--Player 7-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[7][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[7][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[7][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[7][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 7-->



      <!--Player 8-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[8][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[8][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[8][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[8][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 8-->

      <!--Player 9-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[9][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[9][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[9][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[9][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 9-->



      <!--Player 10-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[10][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[10][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[10][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[10][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 10-->

      <!--Player 11-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[11][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[11][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[11][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[11][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 11-->


      <!--Player 12-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[12][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[12][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[12][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[12][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 12-->

      <!--Player 13-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[13][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[13][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[13][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[13][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 12-->



      <!--Player 14-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[14][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[14][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[14][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[14][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 14-->

      <!--Player 15-->
      <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  aria-label="name"
                  name="players[15][name]"
                  pattern=".{1,}"
                  required
          />
          <label>Name</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1200"
                  placeholder="Rating"
                  aria-label="rating"
                  name="players[15][rating]"
                  required
          />
          <label>Rating</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="number"
                  class="form-control"
                  min="0"
                  max="1000000"
                  placeholder="PDGA Number"
                  aria-label="pdganumber"
                  name="players[15][pdganumber]"
          />
          <label>PDGA Number</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid PDGA number.
          </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
          <input
                  type="email"
                  class="form-control"
                  placeholder="example@example.com"
                  aria-label="email"
                  name="players[15][email]"
                  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                  required
          />
          <label>Email address</label>
          <div class="valid-feedback">Looks good!</div>
          <div class="invalid-feedback">
            Please provide a valid email address.
          </div>
        </div>
      </fieldset>
      <!-- end player 15-->




    <!--Player 16-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[16][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[16][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[16][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[16][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 16-->

    <!--Player 17-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[17][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[17][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[17][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[17][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 17-->


    <!--Player 18-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[18][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[18][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[18][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[18][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 18-->

    <!--Player 19-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[19][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[19][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[19][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[19][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 19-->


    <!--Player 20-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[20][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[20][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[20][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[20][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 20-->

    <!--Player 21-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[21][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[21][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[21][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[21][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 21-->



    <!--Player 22-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[22][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[22][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[22][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[22][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 22-->

    <!--Player 23-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[23][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[23][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[23][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[23][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 23-->



    <!--Player 24-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[24][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[24][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[24][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[24][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 24-->

    <!--Player 25-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[25][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[25][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[25][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[25][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 25-->



    <!--Player 26-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[26][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[26][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[26][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[26][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 26-->

    <!--Player 27-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[27][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[27][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[27][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[27][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 27-->


    <!--Player 28-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[28][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[28][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[28][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[28][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 28-->

    <!--Player 29-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[29][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[29][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[29][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[29][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 28-->



    <!--Player 30-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[30][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[30][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[30][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[30][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 30-->

    <!--Player 31-->
    <fieldset class="row gx-2 gy-2 input-group pb-2">
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="text"
                    class="form-control"
                    placeholder="Name"
                    aria-label="name"
                    name="players[31][name]"
                    pattern=".{1,}"
                    required
            />
            <label>Name</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid name.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1200"
                    placeholder="Rating"
                    aria-label="rating"
                    name="players[31][rating]"
                    required
            />
            <label>Rating</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please provide a valid rating.</div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="number"
                    class="form-control"
                    min="0"
                    max="1000000"
                    placeholder="PDGA Number"
                    aria-label="pdganumber"
                    name="players[31][pdganumber]"
            />
            <label>PDGA Number</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid PDGA number.
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-12 form-floating mb-2">
            <input
                    type="email"
                    class="form-control"
                    placeholder="example@example.com"
                    aria-label="email"
                    name="players[31][email]"
                    pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                    required
            />
            <label>Email address</label>
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">
                Please provide a valid email address.
            </div>
        </div>
    </fieldset>
    <!-- end player 31-->
  </br>
  </br>
    <!--Submit button-->
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
</form>
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
