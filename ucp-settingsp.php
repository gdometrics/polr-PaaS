<?php

// Setting Changes Endpoint


require_once 'req.php';
if (!$_SESSION['li']) {
    header('Location: index.php');
}

require_once 'polrauth.php';
$polrauth = new polrauth();
$islogged = $polrauth->islogged();

//checking

$action = $mysqli->real_escape_string($_POST['action']);
$username = $mysqli->real_escape_string($_SESSION['username']);


if ($action == 'changepw') {
    $currpw = $mysqli->real_escape_string($_POST['currpw']);
    $newpw = $mysqli->real_escape_string($_POST['newpw']);
    
    require_once 'password.php';
    function noMc($length = 23) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    $salt = noMc();
    $opts = array(
        'cost' => 10,
        'salt' => $salt
    );
    $hashed = password_hash($newpw, PASSWORD_BCRYPT, $opts);
    $sqr = "SELECT `password` FROM `auth` WHERE `username`='{$username}';";
    $res = $mysqli->query($sqr);
    $fetch = mysqli_fetch_assoc($res);
    $hpw = $fetch['password'];
    $islegit = $polrauth->processlogin($username, $currpw);
    if (!$islegit) {
        die('Invalid current password. <a href="forgotpass.php">Forgot your password?</a>  <a href="ucp.php">Back</a>');
    }
    
    $sqr = "UPDATE auth SET password = '{$hashed}' WHERE `username`='{$username}';";
    $res = $mysqli->query($sqr);
    if ($res) {
        require_once 'header.php';
        echo "Success! <a href='ucp.php'>Back</a>";
        require_once 'footer.php';
        die();
    } else {
        require_once 'header.php';
        echo "Error! <a href='ucp.php'>Back</a>";
        require_once 'footer.php';
        die();
    }
}
echo "Invalid Action";
die();
