<?php

/**
 * This function transforms the php.ini notation for numbers (like '2M') to an integer (2*1024*1024 in this case)
 *
 * @param string $sSize
 * @return integer The value in bytes
 */
function convertPHPSizeToBytes($sSize)
{
    //
    $sSuffix = strtoupper(substr($sSize, -1));
    if (!in_array($sSuffix,array('P','T','G','M','K'))){
        return (int)$sSize;
    }
    $iValue = substr($sSize, 0, -1);
    switch ($sSuffix) {
        case 'P':
            $iValue *= 1024;
        // Fallthrough intended
        case 'T':
            $iValue *= 1024;
        // Fallthrough intended
        case 'G':
            $iValue *= 1024;
        // Fallthrough intended
        case 'M':
            $iValue *= 1024;
        // Fallthrough intended
        case 'K':
            $iValue *= 1024;
            break;
    }
    return (int)$iValue;
}


function diediedie($code=500)
{
    http_response_code($code);
    exit(0);
}

function debuglog($log)
{
    if(!is_devel())
        return;
    error_log("DEBUG: $log");
}

function dolog($log)
{
    error_log($log);
}

function decho($str)
{
    if(!is_devel())
        return;
    echo "<PRE>DEBUG: ";
    print_r($str);
    echo "</PRE>";
}

/**
 *  Returns true if the site is in development mode, meaning [sys][is_devel] is set to 1 in config and you are coming from localhost
 * @return bool
 * @throws Exception
 */
function is_devel()
{
    global $Config;
    if($Config->get("sys",'is_devel')==1){
        if($_SERVER['REMOTE_ADDR']=='127.0.0.1')
            return true;
    }
    return false;
}

/**
 * This function returns the maximum files size that can be uploaded
 * in PHP
 * @returns int File size in bytes
 **/
function getMaximumFileUploadSize()
{
    return min(convertPHPSizeToBytes(ini_get('post_max_size')), convertPHPSizeToBytes(ini_get('upload_max_filesize')));
}


/** Returns the website's base URL based on the current HTTP HOST header or the [sys][baseurl] configuration key.  Falls
 * back to baseurl if the requested http host is not an allowed name as per [sys][allowedhostnames].
 * @return Config
 * @throws Exception
 */
function getsiteaddress()
{
    global $Config;
    $c=$Config;    //$host = SimpleRouter::request()->getHost();
    $referer = $_SERVER["HTTP_REFERER"];
    $foo=parse_url($referer);
    $referer_host = $foo['host'];
    $referer_port = $foo['port'];
    $referer_scheme = $foo['scheme'];

    $baseurl = $c->get("sys", "baseurl");

    if(validateReferer($referer)){
        return $referer_scheme.'://'.$referer_host.":".$referer_port;
    } else {
        $ret = $c->get("sys", "baseurl");
        return $ret;
    }

    throw new Exception("Can't determine siteaddress - no [sys][baseurl] defined and no HTTP_HOST was set, or was valid per allowedhostnames");
}

/** Checks to see if a particular url is from us (based on http referer.  The ['sys']['allowedhostnames'] configuration
 * key is used to populate a list of domains that will pass the request
 * */
function validateReferer($referer)
{
    global $Config;
    $c=$Config;
    $stuff=explode(' ',$c->get("sys","allowedhostnames"));
    if(is_array($stuff)){
        $domains=$stuff;
    } else {
        $domains = [];
    }

    $siteurl = $c->get("sys", "baseurl");
    $foo=parse_url($siteurl);
    $domains[] = $foo['host'];

    $foo = parse_url($referer);
    foreach($domains as $siteurl) {
        if (strtolower($siteurl) == strtolower($foo['host'])) {
            return (true);
        }
    }
    debuglog("validateReferer($referer) returning false; domains=" . print_r($domains, true));
    return(false);
}

function require_login()
{
    if(ClientAuth::checkLogin()==false){
        header("Location: /login/");
        exit;
    }
}

/**
 * Validates an email address with a multi-step process, including format and MX record checks.
 *
 * This function provides a more reliable validation than filter_var($email, FILTER_VALIDATE_EMAIL) alone.
 *
 * @param string $email The email address to validate.
 * @return bool True if the email address is valid and the domain has MX records, false otherwise.
 */
function is_email_valid(string $email): bool
{
    // Step 1: Use a regular expression for a strict format check.
    // This regex is a widely used and effective pattern for RFC 5322 compliance.
    // It is more stringent than what filter_var typically allows.
    $pattern = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7E]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7E]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

    if (!preg_match($pattern, $email)) {
        // Failed the basic format test.
        return false;
    }

    // Step 2: Extract the domain name from the email address.
    $domain = substr(strrchr($email, "@"), 1);

    if ($domain === false) {
        // This should be caught by the regex, but as a safeguard.
        return false;
    }

    // Step 3: Check for DNS MX (Mail Exchange) records for the domain.
    // getmxrr() is often more reliable than checkdnsrr() for this specific purpose.
    // It also provides the actual MX records if needed later.
    // We also check for a basic A or AAAA record as a fallback, as some servers
    // accept mail directly without a dedicated MX record.
    if (!checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A') && !checkdnsrr($domain, 'AAAA')) {
        // The domain does not have MX records, meaning it's not configured to receive email.
        return false;
    }

    // If all checks pass, the email is considered valid.
    return true;
}

function bin2uuid($bin) {
    $uuidReadable = unpack("H*",$bin);
    $uuidReadable = preg_replace("/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/", "$1-$2-$3-$4-$5", $uuidReadable);
    $uuidReadable = array_merge($uuidReadable)[0];
    return $uuidReadable;
}
