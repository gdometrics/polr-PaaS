<?php

//POLR *PUBLIC* API - by http://github.com/cydrobolt/polr
//@Author: Cydrobolt

/*
 * Reference:
 * Request Vars Listing:
 * ReqEndpoint: url - the url to perform action on
 * ReqEndpoint: apikey - the APIKey provided for authentication
 * ReqEndpoint: action - action to perform, either lookup or shorten
 * OptEndpoint: temp - whether the URL is temporary or not
 */
$reqargs['nosession'] = true;
require_once('req.php'); //Fetch Config
require_once('dnsbl.php'); //Load Google SafeBrowsing Script

$protocol = '://';
if (!strstr($_REQUEST['url'], $protocol)) {
    $urlr = "http" . $protocol . $_REQUEST['url']; //add http:// if :// not there
}

$dnsbl = new dnsbl(); //create a gsb object
if (is_string($ip) && is_string($_REQUEST['action']) && is_string($_REQUEST['url'])) {
    $apikey = $mysqli->real_escape_string($ip); // Sanitize IP [ :D ]
    $action = $mysqli->real_escape_string($_REQUEST['action']);
    $url_api = $mysqli->real_escape_string($_REQUEST['url']);
} else {
    header("HTTP/1.0 400 Bad Request");
    die("Error: No value specified, or wrong data type.");
}

// Don't check for an API key; the API key is simply the user's IP

$userquota = 8; // The quota for non-API-key users can be changed; but for our purposes, we'll set it to 8 links/min (default API quota is around 40)
//check if valid

if (!filter_var($url_api, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED) && $action != "lookup") {
    header("HTTP/1.0 400 Bad Request");
    echo "Error: URL is not valid"; //URL not well formatted, but allow if action is lookup
    die();
}
//Check if URL given is malware/phishing

$isbl = $dnsbl->isbl($url_api);
if ($isbl === "malware" || $isbl === "phishing") {
    header("HTTP/1.0 401 Unauthorized");
    echo "Polr does not shorten potentially malicious URLs"; //If link tests positive to possible malware/phish, then block
    die();
}

function lookup($urltl) {
    global $mysqli;
    $val = $mysqli->real_escape_string($urltl);
    $query = "SELECT rurl FROM redirinfo WHERE baseval='{$val}'";
    $result = $mysqli->query($query) or die("QUERY ERROR");
    $row = mysqli_fetch_assoc($result);
    return $row['rurl'];
}

function exquota($apikey, $quota) {
    
    if ($quota < 1) {
        return false; // if quota is negative, then no quota
    }
    
    global $mysqli;
    $last_min = time()-60;
    $query = "SELECT `rurl` FROM `redirinfo` WHERE user='IPKEY-{$apikey}' AND UNIX_TIMESTAMP(date) > $last_min;";
    $result = $mysqli->query($query) or showerror();
    $total_queries = $mysqli->affected_rows; //get the amount of queries in the past minute
    $query = "SELECT `rurl` FROM `redirinfo-temp` WHERE user='IPKEY-{$apikey}' AND UNIX_TIMESTAMP(date) > $last_min;";
    $result = $mysqli->query($query) or showerror();
    $total_queries_temp = $mysqli->affected_rows; //get the amount of queries to temp in the past minute
    if (($total_queries+$total_queries_temp) >= $quota) {
        return true; // if met/exeeding quota
    }
    else {
        return false;
    }
    
}

function shorten($urlr, $t = 'false') {
    global $mysqli;
    global $wsa;
    global $apikey;
    global $ip;
    
    $protocol = '://';
    $isshort = array('polr.cf', 'bit.ly', 'is.gd', 'tiny.cc', 'adf.ly', 'ur1.ca', 'goo.gl', 'ow.ly', 'j.mp', 't.co');
    foreach ($isshort as $url_shorteners) {
        if (strstr($urlr, $protocol . $url_shorteners)) {
            header("HTTP/1.0 400 Bad Request");
            die("400 Bad Request (URL Already a ShortURL)");
        }
    }
    $query1 = "SELECT rid FROM redirinfo WHERE rurl='{$urlr}'";
    $result = $mysqli->query($query1);
    $row = mysqli_fetch_assoc($result);
    $existing = $row['rid'];
    if (!$existing) {
        if ($t != 'false') {
            //if tempurl
            $query1 = "SELECT MAX(rid) AS rid FROM `redirinfo-temp`;";
            $result = $mysqli->query($query1);
            $row = mysqli_fetch_assoc($result);
            $ridr = $row['rid'];
            $baseval = "t-" . (string) (base_convert($ridr + 1, 10, 36));
            $query2 = "INSERT INTO `redirinfo-temp` (baseval,user,rurl,ip) VALUES ('{$baseval}','IPKEY-{$apikey}','{$urlr}','{$ip}');";
        } else {
            //if NOT tempurl
            $query1 = "SELECT MAX(rid) AS rid FROM redirinfo;";
            $result = $mysqli->query($query1);
            $row = mysqli_fetch_assoc($result);
            $ridr = $row['rid'];
            $baseval = base_convert($ridr + 1, 10, 36);
            $query2 = "INSERT INTO redirinfo (baseval,user,rurl,ip) VALUES ('{$baseval}','IPKEY-{$apikey}','{$urlr}','{$ip}');";
        }

        $result2r = $mysqli->query($query2) or showerror();
        return "http://{$wsa}/{$baseval}";
    } else {
        $query1 = "SELECT baseval FROM redirinfo WHERE rurl='{$urlr}'";
        $result = $mysqli->query($query1);
        $row = mysqli_fetch_assoc($result);
        $baseval = $row['baseval'];
        return "http://{$wsa}/{$baseval}";
    }
}
/*
 * One last check! 
 * See whether the user is exeeding his quota
 */

$isexeeding = exquota($apikey, $userquota);
if ($isexeeding) {
    header("HTTP/1.0 503 Service Unavailable");
    die('Hey, slow down! Exeeding your perminute quota. Try again in around a minute.'); 
    // don't let them shorten :>
}

// API execute actions. Promised, no more checks :)

if ($action == "shorten") {
    if (isset($_REQUEST['temp'])) {
        $ist = $mysqli->real_escape_string($_REQUEST['temp']);
        $ist = strtolower($ist);
    }
    if (($ist == 'true') || ($ist == 'false')) {
        echo shorten($url_api, $ist);
        die();
    }
    echo shorten($url_api);
    die();
} else if ($action == "lookup") {
    $looked_up_url = lookup($url_api);
    if (!$looked_up_url) {
        header("HTTP/1.0 404 Not Found");
        die("404 Not Found");
    } else {
        echo $looked_up_url;
    }
    die();
} else {
    header("HTTP/1.0 400 Bad Request");
    die("Invalid Action");
}

