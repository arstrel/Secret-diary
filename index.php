<?php

  session_start();

  $error = "";

  if (array_key_exists("logout", $_GET)) {

    unset($_SESSION);
    session_destroy();
    $_SESSION = array();

    setcookie("id", "", time() - 60*60);
    $_COOKIE = array();

  } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR
   (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {

    header("Location: loggedinpage.php");

  }

  //when the user clicks submit button
  //(eather Signup or login, necause bith buttons send $_POST['submit'])
 if (array_key_exists('submit', $_POST)) {

   //keeping database connection params and password in a sepaprate file to be able to reuse it
   include("connection.php");

   //server-side field validation
    if (!$_POST['email']) {

      $error .= "An email address is required<br>";
    }

    if (!$_POST['password']) {

      $error .= "A password is required<br>";
    }
    //if there are any errors - display them
    if ($error != "") {

      $error = "There were errors in your form: <br>".$error;

    } else {

      //if sign up button clicked (NOT log in button) - proceed with sigin up stuff
      if ($_POST['signUp'] == '1') {

        $query = "SELECT id FROM `users` WHERE email =
        '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

        $result = mysqli_query($link, $query);

        //Sign up form - if email exists in database - it is taken, no signing up
        if (mysqli_num_rows($result) > 0) {

          $error = "That email address is taken";

        } else {

          $hashedPassword = password_hash($_POST['password'],PASSWORD_DEFAULT);

          $userEmail =  $_POST['email'];

          $query ="INSERT INTO `users` (`email`, `password`) VALUES
          ('".mysqli_real_escape_string($link, $userEmail)."', '$hashedPassword')";

          if (!mysqli_query($link, $query)) {

            $error = "Could not sign you up - please try again later.";

          }  else {

            //creating session with the latest created user id
            $_SESSION['id'] = mysqli_insert_id($link);

            //if Stay logged in checked - creating cookie
            if ($_POST['stayLoggedIn'] == '1') {

              setcookie('id', mysqli_insert_id($link), time() + 60*60*24);

            }

              //redirecting user to logged in page
              header("Location: loggedinpage.php");

          }
        }
      }
      //if log in button clicked (NOT sigh up) - proceed with log in stuff
      else {

        $query = "SELECT * FROM `users` WHERE email =
        '".mysqli_real_escape_string($link, $_POST['email'])."'";

        $result = mysqli_query($link, $query);

        $row = mysqli_fetch_array($result);

        if (isset ($row)) {

          if (password_verify($_POST['password'], $row['password'])) {

            $_SESSION['id'] = $row['id'];

            //if Stay logged in checked - creating cookie
            if (array_key_exists('stayLoggedIn', $_POST) AND $_POST['stayLoggedIn'] == '1') {

              setcookie("id", $row['id'], time() + 60*60*24);

            }

              //redirecting user to logged in page
              header("Location: loggedinpage.php");
          }
          else {

            $error = "That email/password combination could not be found.";
          }

        } else {

          $error = "That email/password combination could not be found.";
        }

      }
    }
  }



?>
<?php include("header.php"); ?>

    <div class="container">
       <div class="text-center mb-4">
         <img class="mb-4 pic" src="https://source.unsplash.com/qAjJk-un3BI/82x82" alt="" width="82" height="82">
         <h1 class="h3 mb-3 font-weight-normal">Secret Dairy</h1>
        </div>
     <form class="form-signin" method="post" id="signUpForm">
       <div class="text-center">
         <p>Store your thought permanently and securely</p>
         <p class="lead" id="displayMessage">Interested? Sign up now.</p>
       </div>
       <div id="alertMessage"><?php if ($error != "") {
         echo '<div class="alert alert-danger">'.$error.'</div>';
       }; ?></div>

       <div class="form-label-group">
         <input type="email" name="email" class="form-control" placeholder="Email address" required autofocus>
         <label for="inputEmail">Email address</label>
       </div>

       <div class="form-label-group">
         <input type="password"  name="password" class="form-control" placeholder="Password" required>
         <label for="inputPassword">Password</label>
       </div>

       <div class="checkbox mb-3">
         <label>
           <input type="checkbox" value="1" name="stayLoggedIn"> Stay logged in
         </label>
       </div>
       <div class="d-flex justify-content-center mb-2">
          <input type="hidden" name="signUp" value="1">
         <button class="btn btn-lg btn-primary " name="submit" type="submit" value="signup">Sign up!</button>
       </div>
       <p class="d-flex justify-content-center">
         <a class="toggleForms" >Log In </a>
       </p>


     </form>


     <form class="form-signin" method="post" id="logInForm">
       <div class="text-center">
         <p>Store your thought permanently and securely</p>
         <p class="lead" id="displayMessage">Log in using your username and password.</p>
       </div>
       <div id="alertMessage"><?php if ($error != "") {
         echo '<div class="alert alert-danger">'.$error.'</div>';
       }; ?></div>

       <div class="form-label-group">
         <input type="email"  name="email" class="form-control" placeholder="Email address" required >
         <label for="inputEmail">Email address</label>
       </div>

       <div class="form-label-group">
         <input type="password" name="password" class="form-control" placeholder="Password" required>
         <label for="inputPassword">Password</label>
       </div>

       <div class="checkbox mb-3">
         <label>
           <input type="checkbox" value="1" name="stayLoggedIn"> Stay logged in
         </label>

       </div>

       <div class="d-flex justify-content-center mb-2">
         <input type="hidden" name="signUp" value="0">
        <button class="btn btn-lg btn-success " type="submit" name="submit" value="login">Log in</button>
      </div>

      <p class="d-flex justify-content-center">
        <a class="toggleForms" >Sign Up! </a>
      </p>

     </form>
      <div class="d-flex justify-content-center">
        <p class="mt-5 mb-3 text-center remark">&copy;2019. Artem Streltsov</p>
      </div>
   </div>
 <?php include("footer.php");?>
