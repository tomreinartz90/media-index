<?php
/**
 * Simple class to get the latest information from the movie app and store this in a json file that can be used at a later stage.
 */
require_once "./RestApi.php";

$rest = new RestApi();
$baseUrl = "https://torrentapi.org/pubapi_v2.php";

//get the token
$token = json_decode($rest->get("$baseUrl?get_token=get_token"), true);
$tokenKey = $token['token'];

//recent movies
$recentMovies = $rest->get("$baseUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");

//sleep for 1 seconds due to the restriction on the api
sleep(1);

//recent tv
$recentTv = $rest->get("$baseUrl?category=tv&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");


file_put_contents('./recentMovies.json', $recentMovies);
file_put_contents('./recentTv.json', $recentTv);

