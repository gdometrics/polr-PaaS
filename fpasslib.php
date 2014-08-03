<?php
// includes for forgotpassword functions
require_once 'sgmail.php';
require_once 'polrauth.php';
require_once 'req.php';
$polrauth = new polrauth();
$sgmail = new sgmail();

class fpass {

    public function sendfmail($to, $username, $rkey) {
        global $ip;
        global $sgmail;
        global $wsa;
        $subject = 'Polr Password Recovery';
        $message = 'Hello,<br /><br />'
                . "Someone has requested a password reset email (IP: $ip)<br />"
                . "To recover your password, click here: <a href='http://polr.me/forgotpass.php?key=$rkey&username=$username'>http://polr.cf/forgotpass.php?key=$rkey&username=$username</a><br />"
                . "If this was not you, no further action is needed. If you are constantly receiving these emails, but did not request<br />"
                . "a new password, please email support@polr.cf with the IP printed above. <br />"
                . "<br />"
                . "Cheers,<br />"
                . "The Polr Team<br />"
                . "<em>support@polr.cf</em>";


        $sgmail->sendmail($to, $subject, $message); //actually send the email
    }

    public function hash($pass) {
        $opts = [
            'cost' => 10,
        ];
        $hashed = password_hash($pass, PASSWORD_BCRYPT, $opts);
        return $hashed;
    }

    public function changepass($newpass, $username) {
        global $mysqli;
        $username = $mysqli->real_escape_string($username);
        $hashpass = $this->hash($newpass);
        $qr = "UPDATE auth SET password='{$hashpass}' WHERE username='$username';";
        $e = $mysqli->query($qr) or showerror();
        return true;
    }

}

