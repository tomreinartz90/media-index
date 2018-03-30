<?php


  require_once __DIR__ . "/moviedb.php";
  require_once __DIR__ . "/../helpers/CacheUtil.php";

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
      $this -> movieDb = new Moviedb();
      $this -> cache = new CacheUtil( __DIR__ . "/../../cache/lists/" );
      $this -> rest = $this -> movieDb -> rest;
      $this -> torrentapiUrl = "https://torrentapi.org/pubapi_v2.php?app_id=my_local_movie_app";
      $this -> torrentapiToken = null;
    }

    private function getTorrentApiToken()
    {
      if ( $this -> torrentapiToken == null ) {
        $token = $this -> rest -> getJson( "$this->torrentapiUrl&get_token=get_token" );
        $this -> torrentapiToken = $token[ 'token' ];
        return $token;
      }
      return $this -> torrentapiToken;
    }

    public function getPopularUndergroundMovies( $useCache = true )
    {
      $getData = function () {
        $tokenKey = $this -> getTorrentApiToken();
        $data = $this -> rest -> getJson( "$this->torrentapiUrl&category=movies&format=json_extended&mode=list&sort=seeders&limit=100&page=1&token=$tokenKey" );
        $ids = $this -> getMovieDbIds( $data );
        $movies = $this -> movieDb -> toSimpleMovieObjs( $this -> movieDb -> getMovieDetailsByIds( $ids ) );
        if ( sizeof( $movies ) > 0 ) {
          return $movies;
        } else {
          return false;
        }

      };

      return $this -> cache -> getOrSetData( "popular_underground_movies", $useCache, $getData );

    }

    public function getRecentUndergroundMovies( $useCache = true )
    {

      $getData = function () {
        $tokenKey = $this -> getTorrentApiToken();
        $data = $this -> rest -> getJson( "$this->torrentapiUrl&category=movies&format=json_extended&mode=list&sort=last&limit=100&page=1&token=$tokenKey" );
        $ids = $this -> getMovieDbIds( $data );
        $movies = $this -> movieDb -> toSimpleMovieObjs( $this -> movieDb -> getMovieDetailsByIds( $ids ) );
        if ( sizeof( $movies ) > 0 ) {
          return $movies;
        }
        return false;
      };

      return $this -> cache -> getOrSetData( "recent_underground_movies", $useCache, $getData );

    }


    public function getNewSeries( $useCache )
    {
      $getData = function () {
        return $this -> movieDb -> discoverSeries( "first_air_date.desc" )[ 'results' ];
      };

      return $this -> cache -> getOrSetData( "new_series", $useCache, $getData );


    }

    public function getPopularSeries( $useCache )
    {
      $getData = function () {
        return $this -> movieDb -> discoverSeries( "popularity.desc" )[ 'results' ];
      };

      return $this -> cache -> getOrSetData( "popular_series", $useCache, $getData );
    }


    private function getMovieDbIds( $data )
    {
      $result = [];
      if ( isset( $data ) && isset( $data[ 'torrent_results' ] ) ) {
        foreach ( $data[ 'torrent_results' ] as $row ) {
          if ( isset( $row[ 'episode_info' ][ 'themoviedb' ] ) and $row[ 'episode_info' ][ 'themoviedb' ] != 0 ) {
            array_push( $result, $row[ 'episode_info' ][ 'themoviedb' ] );
          }
        }
        return array_values( array_unique( $result ) );
      } else {
        return false;
      }
    }


  }