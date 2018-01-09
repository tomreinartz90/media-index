<?php


require __DIR__ . "/moviedb.php";

/**
 * Created by PhpStorm.
 *
 * User: t.reinartz
 * Date: 5-1-2018
 * Time: 13:40
 */
class ListService
{

    /**
     * ListService constructor.
     */
    public function __construct()
    {
        $this->movieDb = new Moviedb();
        $this->rest = $this->movieDb->rest;
        $this->torrentapiUrl = "https://torrentapi.org/pubapi_v2.php";
        $this->torrentapiToken = null;
    }

    private function getTorrentApiToken()
    {
        if ($this->torrentapiToken == null) {
            $token = json_decode($this->rest->get("$this->torrentapiUrl?get_token=get_token"), true);
            $this->torrentapiToken = $token['token'];
//            sleep(2);
        }
        return $this->torrentapiToken;
    }

    public function getPopularUndergroundMovies($useCache = true)
    {
        $tokenKey = $this->getTorrentApiToken();
        $data = $this->rest->get("$this->torrentapiUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");
        return [
            'token' => $tokenKey,
            'data' => json_decode($data)
        ];
        /*$data = json_decode());
        if (empty(json_decode($data, true)['error'])) {
            file_put_contents('./popular-movies.json', $data);
        } else {
            sleep(2);
            return $this->getPopularUndergroundMovies($useCache);
        }
        return $data;*/
    }

    public function getRecentUndergroundMovies($useCache = true)
    {
        $tokenKey = $this->getTorrentApiToken();
        $data = json_decode($this->rest->get("$this->torrentapiUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=last&limit=100&page=1&token=$tokenKey"));
    }

    public function getRecentUndergroundSeries($useCache = true)
    {

        $tokenKey = $this->getTorrentApiToken();
        $data = $this->rest->get("$this->torrentapiUrl?category=tv&append_to_response=videos,similar&format=json_extended&mode=list&sort=last&limit=100&page=1&token=$tokenKey");

    }

    public function getPopularUndergroundSeries($useCache = true)
    {
        $tokenKey = $this->getTorrentApiToken();
        $data = $this->rest->get("$this->torrentapiUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");
    }

}