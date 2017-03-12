<?php

require_once 'composer/vendor/autoload.php';
session_start();

$scriptUri = "http://".$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'];

// Call Google API
$client = new Google_Client();
$client->setAccessType('online');
$client->setApplicationName('The Feed');
$client->setClientId('CLIENT_ID');
$client->setClientSecret('CLIENT_SECRET');
$client->setRedirectUri($scriptUri);
$client->setDeveloperKey('API_KEY'); // API key
$client->addScope(Google_Service_Youtube::YOUTUBE);
$client->addScope(Google_Service_Youtube::YOUTUBE_FORCE_SSL);
$client->addScope(Google_Service_Youtube::YOUTUBE_READONLY);
$client->addScope(Google_Service_Youtube::YOUTUBEPARTNER);

// If we haven't yet received auth code from a session, call Google's Auth URL creator
if (! isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} 

// Otherwise, we can exchange it for an access token, and redirect to the main site (if logged in)
else {
    $client->authenticate($_GET['code']);
    $_SESSION['youtube_access_token'] = $client->getAccessToken();
    $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

?>

