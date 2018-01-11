<?php


require __DIR__ . "/moviedb.php";
require __DIR__ . "/../helpers/CacheUtil.php";

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
        $this->cache = new CacheUtil(__DIR__ . "/../../cache/lists/");
        $this->rest = $this->movieDb->rest;
        $this->torrentapiUrl = "https://torrentapi.org/pubapi_v2.php";
        $this->torrentapiToken = null;
    }

    private function getTorrentApiToken()
    {
        if ($this->torrentapiToken == null) {
            $token = json_decode($this->rest->get("$this->torrentapiUrl?get_token=get_token"), true);
            $this->torrentapiToken = $token['token'];
        }
        return $this->torrentapiToken;
    }

    public function getPopularUndergroundMovies($useCache = true)
    {
        $cacheKey = "popular_underground";
        if (!$this->cache->has($cacheKey) || $useCache === false) {
            $tokenKey = $this->getTorrentApiToken();
            $data = $this->rest->get("$this->torrentapiUrl?category=movies&append_to_response=videos,similar&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey");
            $ids = $this->getMovieDbIds(json_decode($data, true));
            $movies = $this->movieDb->getMovieDetailsByIds($ids);
            if (sizeof($movies) > 0) {
                $this->cache->set($cacheKey, $movies);
                return $movies;
            }
            return false;
        }

        return $this->cache->get($cacheKey);

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

    private function getMovieDbIds($data)
    {
        $result = [];
        if (isset($data) && isset($data['torrent_results'])) {
            foreach ($data['torrent_results'] as $row) {
                array_push($result, $row['episode_info']['themoviedb']);
            }
            return array_values(array_unique($result));
        } else {
            return false;
        }

    }

}