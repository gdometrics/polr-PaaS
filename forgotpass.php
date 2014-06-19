<?php

require_once 'req.php'; // require core libs
require_once 'polrauth.php'; // require auth libs
require_once 'sgmail.php'; // require mail libs
require_once 'password.php'; // require password encryption libs
require_once 'fpasslib.php'; // require fpass functions

$polrauth = new polrauth();
$fpass = new fpass();
require_once 'header.php';

if (isset($_POST['rnpass']) && isset($_POST['npass']) && isset($_POST['crkey']) && isset($_POST['cuser'])) {
    $ckey = $mysqli->real_escape_string($_POST['crkey']);
    $rnpass = $mysqli->real_escape_string($_POST['rnpass']);
    $cuser = $mysqli->real_escape_string($_POST['cuser']);
    $npass = $mysqli->real_escape_string($_POST['npass']);

    $userinfoc = $polrauth->getinfomu($cuser); // fetch info

    if ($userinfoc == false) {
        echo "<h2>That username is not associated with any account. Please try again.</h2>"
        . "<br />"
        . "<a href='forgotpass.php'>Back</a>";
        require_once 'footer.php';
        die();
    }

    if ($userinfoc == false) {
        // if user does not exist
        require_once 'header.php';
        echo "<h2>User or key invalid or already used.</h2>";
        require_once 'footer.php';
        die();
    }
    if ($userinfoc['rkey'] == $_POST['crkey']) { // if rkey & user check out
        if ($npass != $rnpass) {
            // if new pass & repeat don't match
            require_once 'header.php';

            echo "<h2>Passwords don't match. Try again. (click the link in the email again)</h2>";

            require_once 'footer.php';
            die();
        } else { // all checks out
            $fpass->changepass($npass, $cuser); // change pass
            $polrauth->crkey($cuser); //change rkey
            require_once 'header.php';
            echo "<h2>Password changed.</h2>";
            require_once 'footer.php';
            die();
        }
    }
}
$fpass = new fpass();
if (isset($_GET['key']) && isset($_GET['username'])) {
    $username = $mysqli->real_escape_string($_GET['username']);
    $userinfoc = $polrauth->getinfomu($username); // fetch info

    if ($userinfoc == false) {
        echo "<h2>That username is not associated with any account. Please try again.</h2>"
        . "<br />"
        . "<a href='forgotpass.php'>Back</a>";
        require_once 'footer.php';
        die();
    }

    if ($userinfoc == false) {
        // if user does not exist
        require_once 'header.php';
        echo "<h2>User or key invalid or already used.</h2>";
        require_once 'footer.php';
        die();
    }
    //var_dump($userinfoc);
    if ($userinfoc['rkey'] == $_GET['key']) {
        require_once 'header.php';
        echo "<h2>Change Password for {$_GET['username']}</h2>";
        echo "<form action='forgotpass.php' method='POST'>"
        . "<input type='password' name='npass' placeholder='New Password' class='faorm-control' size='50'/>"
        . "<input type='password' name='rnpass' placeholder='Repeat New Password' class='faorm-control' size='50'/>"
        . "<input type='hidden' name='crkey' value='{$_GET['key']}' />"
        . "<input type='hidden' name='cuser' value='{$username}' />"
        . "<input type='submit' value='Change Password' />"
        . "</form>";
        echo "<script src='fpass.js'></script>";
        require_once 'footer.php';
        die();
    }
}
/*
  if (isset($_POST['username']) == true && isset($_POST['key']) == true) {

  }
 */
@$email = $_POST['email'];
if (!$email) {
    echo "<h2>Forgot your password?</h2>"
    . "<br/ >"
    . "<form action='forgotpass.php' method='POST'>"
    . "<input type='text' name='email' placeholder='Email...' />"
    . "<input type='submit' value='Get a password reset email' />"
    . "</form>";

    require_once 'footer.php';
    die();
}
if (isset($_POST['email']) == false) {
    echo "<h2>Forgot your password?</h2>"
    . "<br/ >"
    . "<form action='forgotpass.php' method='POST'>"
    . "<input type='text' name='email' placeholder='Email...' />"
    . "<input type='submit' value='Get a password reset email' />"
    . "</form>";

    require_once 'footer.php';
    die();
}

if (strlen($email) < 5) {
    echo "<h2>Forgot your password?</h2>"
    . "<br/ >"
    . "<form action='forgotpass.php' method='POST'>"
    . "<input type='text' name='email' placeholder='Email...' />"
    . "<input type='submit' value='Get a password reset email' />"
    . "</form>";

    require_once 'footer.php';
    die();
}


$email = $mysqli->real_escape_string($_POST['email']);
$userinfo = $polrauth->getinfome($email);

if ($userinfo == false) {
    echo "<h2>That email is not associated with any account. Please try again.</h2>"
    . "<br />"
    . "<a href='forgotpass.php'>Back</a>";
    require_once 'footer.php';
    die();
}

$rkey = $userinfo['rkey'];
$username = $userinfo['username'];
$fpass->sendfmail($email, $username, $rkey); // send the email

echo "Email successfully sent. Check your inbox for more info.";
require_once 'footer.php';

