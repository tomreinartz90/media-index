<?php
/**
 * Simple class to get the latest information from the movie app and store this in a json file that can be used at a later stage.
 */
require_once "./RestApi.php";

$rest = new RestApi();
$baseUrl = "https://torrentapi.org/pubapi_v2.php";
$result = "Started, ";

//get the token
$token = json_decode($rest->get("$baseUrl?get_token=get_token"), true);
$tokenKey = $token['token'];
$result = $result . "Got a new token, ";

//recent movies
sleep(3);
$recentMovies = $rest->get("$baseUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=last&limit=100&page=1&token=$tokenKey");

//recent movies
sleep(3);
$popularMovies = $rest->get("$baseUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");

//recent tv
sleep(3);
$recentTv = $rest->get("$baseUrl?category=tv&append_to_response=videos,similar&format=json_extended&mode=list&sort=last&limit=100&page=1&token=$tokenKey");

//popular tv
sleep(3);
$popularTv = $rest->get("$baseUrl?category=tv&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");

if (empty(json_decode($popularMovies, true)['error'])) {
    file_put_contents('./popular-movies.json', $popularMovies);
    $result = $result . "updated popular-movies, ";
}
if (empty(json_decode($recentMovies, true)['error'])) {
    file_put_contents('./recent-movies.json', $recentMovies);
    $result = $result . "updated recent-movies, ";
}
if (empty(json_decode($popularTv, true)['error'])) {
    file_put_contents('./popular-tv.json', $popularTv);
    $result = $result . "updated popular-tv, ";
}
if (empty(json_decode($recentTv, true)['error'])) {
    file_put_contents('./recent-tv.json', $recentTv);
    $result = $result . "updated recent-tv, ";
}

$result = $result . "done ";
echo $result;


/**
 * Send the result to pushover to get a notification when its done.
 */
$pushoverToken = "";
$pushoverUser = "";
$pushoverMessage = [];
$pushoverMessage['token'] = $pushoverToken;
$pushoverMessage['user'] = $pushoverUser;
$pushoverMessage['message'] = $result;
$pushoverMessage['priority '] = -2;

//$rest->post("https://api.pushover.net/1/messages.json", json_encode($pushoverMessage));