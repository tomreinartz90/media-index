<?php
/**
 */
require_once "./moviedb.php";

$movieDb = new moviedb();
//var_dump( json_decode($movieDb->getMovies(1), true));

function getAndStoreAllMovies($movieDb, $page = 1)
{
    echo "Getting movies for page $page \n";
    $moviesSet = json_decode($movieDb->getMovies($page), true);
    $movies = $moviesSet['results'];

    //sleep a bit to prevent reaching the api limit
    sleep(5);

    if (empty($moviesSet) || empty($moviesSet['page'])) {
        var_dump($moviesSet);
        die("Error getting movie details");
    }

    //get details for every movie
    echo "parsing: ";
    foreach ($movies as $movie) {
        $movieId = $movie['id'];
        echo $movie['original_title'] . ' ';
        $movieDetails = $movieDb->getMovieDetails($movieId);
        file_put_contents("./movies/$movieId.json", $movieDetails);
    }
    echo "\n";


    echo "Parsed all movies \n";
    //get the next set of movies.
    if ($moviesSet['page'] != $moviesSet['total_pages']) {
        $page++;
        getAndStoreAllMovies($movieDb, $page);
    }
}

getAndStoreAllMovies($movieDb, 1);
//
////recent movies
//sleep(3);
//$recentMovies = $rest->get("$baseUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=last&limit=100&page=1&token=$tokenKey");
//
////recent movies
//sleep(3);
//$popularMovies = $rest->get("$baseUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");
//
////recent tv
//sleep(3);
//$recentTv = $rest->get("$baseUrl?category=tv&append_to_response=videos,similar&format=json_extended&mode=list&sort=last&limit=100&page=1&token=$tokenKey");
//
////popular tv
//sleep(3);
//$popularTv = $rest->get("$baseUrl?category=tv&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");
//
//if (empty(json_decode($popularMovies, true)['error'])) {
//    file_put_contents('./popular-movies.json', $popularMovies);
//    $result = $result . "updated popular-movies, ";
//}
//if (empty(json_decode($recentMovies, true)['error'])) {
//    file_put_contents('./recent-movies.json', $recentMovies);
//    $result = $result . "updated recent-movies, ";
//}
//if (empty(json_decode($popularTv, true)['error'])) {
//    file_put_contents('./popular-tv.json', $popularTv);
//    $result = $result . "updated popular-tv, ";
//}
//if (empty(json_decode($recentTv, true)['error'])) {
//    file_put_contents('./recent-tv.json', $recentTv);
//    $result = $result . "updated recent-tv, ";
//}
//
//$result = $result . "done ";
//echo $result;
//
//
///**
// * Send the result to pushover to get a notification when its done.
// */
//$pushoverToken = "";
//$pushoverUser = "";
//$pushoverMessage = [];
//$pushoverMessage['token'] = $pushoverToken;
//$pushoverMessage['user'] = $pushoverUser;
//$pushoverMessage['message'] = $result;
//$pushoverMessage['priority '] = -2;
//
////$rest->post("https://api.pushover.net/1/messages.json", json_encode($pushoverMessage));