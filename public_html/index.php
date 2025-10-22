<?php
require "../phplib/stdinc.php";

    global $Config;

    ui_header("Test");
    $tests = explode(" ", $Config->get("general","tests"));

    if(isset($_GET['oauth_test'])){
        $_SESSION['current_test'] = $_GET['oauth_test'];
    }


    $provider=false;
    $oauthConfig = $Config->getSection($_SESSION['current_test']);
    $redirectUrl = 'http://'.$_SERVER['HTTP_HOST'];


    if($oauthConfig){
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                =>  $oauthConfig['clientid'],//'XXXXXX',    // The client ID assigned to you by the provider
            'clientSecret'            => $oauthConfig['clientsecret'],    // The client password assigned to you by the provider
            'redirectUri'             => $redirectUrl,//https://my.example.com/your-redirect-url/',
            'urlAuthorize'            => $oauthConfig['authorizeurl'],//'https://service.example.com/authorize',
            'urlAccessToken'          => $oauthConfig['tokenurl'],//'https://service.example.com/token',
            'urlResourceOwnerDetails' => $oauthConfig['userinfourl'],//'https://service.example.com/resource'
        ]);
        echo "Provider ".$_SESSION['current_test']." loaded for testing\n";

    } else {
        echo "No provider specified.  Select a test to load parameters.";
    }
    //decho($provider);
    //decho($oauthConfig);
?>
<a href="/">start over</a>
<form action="/" method="get">
    <select name="oauth_test">
        <option value="">-= pick one =-</option>
    <?php
    foreach($tests as $testname){
        ?>
        <option value="<?php echo $testname;?>" <?php if($_SESSION['current_test'] == $testname) echo "selected"; ?>><?php echo $testname; ?></option>
    <?php
    }
    ?>
    </select>
    <input type="submit" value="Change test">
</form>
<?php

if(!$provider)
    exit;

// A session is required to store some session data for later usage
// If we don't have an authorization code then get one
if (!isset($_GET['code'])) {

// Fetch the authorization URL from the provider; this returns the
// urlAuthorize option and generates and applies any necessary parameters
// (e.g. state).
$authorizationUrl = $provider->getAuthorizationUrl();

// Get the state generated for you and store it to the session.
$_SESSION['oauth2state'] = $provider->getState();

// Optional, only required when PKCE is enabled.
// Get the PKCE code generated for you and store it to the session.
$_SESSION['oauth2pkceCode'] = $provider->getPkceCode();

// Redirect the user to the authorization URL.
header('Location: ' . $authorizationUrl);
exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || empty($_SESSION['oauth2state']) || $_GET['state'] !== $_SESSION['oauth2state']) {

if (isset($_SESSION['oauth2state'])) {
unset($_SESSION['oauth2state']);
}

exit('Invalid state');

} else {

try {

// Optional, only required when PKCE is enabled.
// Restore the PKCE code stored in the session.
$provider->setPkceCode($_SESSION['oauth2pkceCode']);

// Try to get an access token using the authorization code grant.
$tokens = $provider->getAccessToken('authorization_code', [
'code' => $_GET['code']
]);

// We have an access token, which we may use in authenticated
// requests against the service provider's API.
echo 'Access Token: ' . $tokens->getToken() . "<br>";
echo 'Refresh Token: ' . $tokens->getRefreshToken() . "<br>";
echo 'Expired in: ' . $tokens->getExpires() . "<br>";
echo 'Already expired? ' . ($tokens->hasExpired() ? 'expired' : 'not expired') . "<br>";

$accessToken = $tokens->getToken();
// Using the access token, we may look up details about the
// resource owner.
$resourceOwner = $provider->getResourceOwner($tokens);

var_export($resourceOwner->toArray());

// The provider provides a way to get an authenticated API request for
// the service, using the access token; it returns an object conforming
// to Psr\Http\Message\RequestInterface.
$request = $provider->getAuthenticatedRequest(
'GET',
'https://service.example.com/resource',
$accessToken
);

} catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

// Failed to get the access token or user details.
exit($e->getMessage());

}

}