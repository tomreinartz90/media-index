<?php
require_once "../RestApi.php";
/**
 * Created by PhpStorm.
 * User: t.reinartz
 * Date: 5-1-2018
 * Time: 13:40
 */
class moviedb
{
    private $baseUrl = "http://api.themoviedb.org/3/";
    private $apiKey = "&api_key=ffbd2b663d53a66c2dd00bb517491490";

    /**
     * moviedb constructor.
     */
    public function __construct()
    {
        $this->rest = new RestApi();
    }

    public function getMovies($page = 1)
    {
        return $this->rest->get($this->baseUrl . "discover/movie?page=$page" . $this->apiKey);
    }

    public function getMovieDetails($movieId){
        return $this->rest->get($this->baseUrl . "movie/355193?append_to_response=videos,recommendations" . $this->apiKey);
    }
}