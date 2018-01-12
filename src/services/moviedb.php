<?php


  require __DIR__ . "/../helpers/RestApi.php";
  require_once __DIR__ . "/../helpers/CacheUtil.php";
  require_once __DIR__ . "/../domain/Movie.php";
  require_once __DIR__ . "/../domain/Serie.php";

  /**
   * Created by PhpStorm.
   *
   * User: t.reinartz
   * Date: 5-1-2018
   * Time: 13:40
   */
  class Moviedb
  {
    private $baseUrl = "http://api.themoviedb.org/3/";
    private $apiKey = "&api_key=ffbd2b663d53a66c2dd00bb517491490";

    /**
     * moviedb constructor.
     */
    public function __construct()
    {
      $this -> rest = new RestApi();
      $this -> movieCache = new CacheUtil( __DIR__ . "/../../cache/movies/" );
      $this -> seriesCache = new CacheUtil( __DIR__ . "/../../cache/series/" );
    }

    public function getMovies( $page = 1 )
    {
      return $this -> rest -> getJson( $this -> baseUrl . "discover/movie?page=$page&with_original_language=en" . $this -> apiKey );
    }

    public function getMovieDetails( $movieId, $useCache = true )
    {
      $getData = function () use ( $movieId ) {
        $details = $this -> rest -> getJson( $this -> baseUrl . "movie/$movieId?append_to_response=videos,recommendations" . $this -> apiKey );
        //check if we successfully got the data from the api
        if ( isset( $details[ 'id' ] ) ) {
          return $details;
        }
        return false;
        //else we will wait a bit and return the data
      };

      $data = $this -> seriesCache -> getOrSetData( $movieId, $useCache, $getData );

      return new Movie( $data );

    }

    function getMovieDetailsByIds( $movieIds )
    {
      $result = [];
      foreach ( $movieIds as $movieId ) {
        $data = $this -> getMovieDetails( $movieId );
        if ( $data != false ) {
          array_push( $result, $data );
        }
      }
      return $result;
    }

    public function getSerieDetails( $serieId, $useCache = true )
    {

      $getData = function () use ( $serieId ) {
        $details = $this -> rest -> getJson( $this -> baseUrl . "tv/$serieId?append_to_response=videos,recommendations" . $this -> apiKey );
        //check if we successfully got the data from the api
        if ( isset( $details[ 'id' ] ) ) {
          return $details;
        }
        return false;
        //else we will wait a bit and return the data
      };

      $data = $this -> seriesCache -> getOrSetData( $serieId, $useCache, $getData );

      return new Serie( $data );
    }

    function getSerieDetailsByIds( $serieIds )
    {
      $result = [];
      foreach ( $serieIds as $id ) {
        $data = $this -> getSerieDetails( $id );
        if ( $data != false ) {
          array_push( $result, $data );
        }
      }
      return $result;
    }


    function buildMovieCache( $page = 1, $remoteUrl, $useCache = true )
    {
      $moviesSet = $this -> getMovies( $page );
      $movies = $moviesSet[ 'results' ];

      //get details for every movie
      foreach ( $movies as $movie ) {
        $movieId = $movie[ 'id' ];
        if ( isset( $remoteUrl ) ) {
          $this -> rest -> get( $remoteUrl . "?movies=" . $movieId );
        } else {
          $this -> getMovieDetails( $movieId, $useCache );
        }
      }

      if ( $moviesSet[ 'page' ] != $moviesSet[ 'total_pages' ] && $moviesSet[ 'page' ] != 999 ) {
        $page++;
        $this -> buildMovieCache( $page, $remoteUrl, $useCache );
      } else {
        return;
      }
    }

    public function getInfoByImbdId( $imdbId )
    {
      $data = null;

      if ( $imdbId ) {
        $data = json_decode( file_get_contents( "https://api.themoviedb.org/3/find/$imdbId?api_key=$this->apiKey&language=en-US&external_source=imdb_id" ), true );
      }

      if ( $data ) {
        if ( sizeof( $data[ 'tv_results' ] ) > 0 ) {
          return $data[ 'tv_results' ][ 0 ];
        } else if ( sizeof( $data[ 'movie_results' ] ) > 0 ) {
          return $data[ 'movie_results' ][ 0 ];
        } else if ( sizeof( $data[ 'person_results' ] ) > 0 ) {
          return $data[ 'person_results' ][ 0 ][ 'id' ];
        }
      }

      return null;
    }

    /**
     * @param $imdbId
     * @return string
     */
    public function getMediaByImbdId( $imdbId, $imageType )
    {
      $info = $this -> getInfoByImbdId( $imdbId );
      $poster_path = $info[ 'poster_path' ];
      $backdrop_path = $info[ 'backdrop_path' ];

      $image = null;
      if ( !$imageType )
        $imageType = 'poster';

      if ( $imageType == 'backdrop' ) {
        $image = file_get_contents( "http://image.tmdb.org/t/p/original/$backdrop_path" );
      } else {
        $image = file_get_contents( "http://image.tmdb.org/t/p/original/$poster_path" );
      }

      return $image;
    }

    public function toSimpleMovieObjs( $movieObjs )
    {
      if ( $movieObjs AND sizeof( $movieObjs ) > 0 ) {
        $mapper = function ( $movie ) {
          if ( $movie && $movie instanceof Movie ) {
            return $movie -> getSimple();
          }
          return false;
        };

        return array_map( $mapper, $movieObjs );
      }
    }

    /**
     * @param $sort | vote_average.desc, vote_average.asc, first_air_date.desc, first_air_date.asc, popularity.desc, popularity.asc
     * @return mixed
     *
     */
    public function discoverSeries( $sort = "popularity.asc", $page = 1 )
    {
      $data = $this -> rest -> getJson( "https://api.themoviedb.org/3/discover/tv?api_key=$this->apiKey&sort_by=$sort&page=$page" );
      if ( isset( $data[ 'results' ] ) ) {
        return $data;
      }
      return false;
    }

  }