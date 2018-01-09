<?php


  require __DIR__ . "/../helpers/RestApi.php";

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
    }

    public function getMovies( $page = 1 )
    {
      return $this -> rest -> get( $this -> baseUrl . "discover/movie?page=$page&with_original_language=en" . $this -> apiKey );
    }

    public function getMovieDetails( $movieId, $useCache = true )
    {
      $filename = __DIR__ . "/../../cache/movies/$movieId.json";

      if ( !file_exists( $filename ) || $useCache === false ) {
        $movieDetails = json_decode( $this -> rest -> get( $this -> baseUrl . "movie/$movieId?append_to_response=videos,recommendations" . $this -> apiKey ), true );
        //check if we successfully got the data from the api
        if ( isset( $movieDetails[ 'id' ] ) ) {
          file_put_contents( $filename, json_encode( $movieDetails ) );
          return $movieDetails;
        } else if ( $movieDetails[ 'status_code' ] == 34 ) {
          return false;
          //else we will wait a bit and return the data
        } else {
          sleep( 5 );
          return $this -> getMovieDetails( $movieId );
        }
      }
      return json_decode( file_get_contents( $filename ), true );
    }

    function getMovieDetailsByIds( $movieIds )
    {
      $result = [];
      foreach ( $movieIds as $movieId ) {
        array_push( $result, $this -> getMovieDetails( $movieId ) );
      }
      return $result;
    }


    function buildMovieCache( $page = 1, $remoteUrl, $useCache = true )
    {
      $moviesSet = json_decode( $this -> getMovies( $page ), true );
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

  }