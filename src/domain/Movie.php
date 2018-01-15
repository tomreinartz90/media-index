<?php

  /**
   * Created by PhpStorm.
   * User: taren
   * Date: 11-1-2018
   * Time: 22:05
   */
  class Movie
  {
    public $adult; //boolean
    public $backdrop_path; //String
    public $belongs_to_collection; //BelongsToCollection
    public $budget; //int
    public $genres; //array(Genre)
    public $homepage; //String
    public $id; //int
    public $imdb_id; //String
    public $original_language; //String
    public $original_title; //String
    public $overview; //String
    public $popularity; //double
    public $poster_path; //String
    public $production_companies; //array(ProductionCompany)
    public $production_countries; //array(ProductionCountry)
    public $release_date; //String
    public $revenue; //int
    public $runtime; //int
    public $spoken_languages; //array(SpokenLanguage)
    public $status; //String
    public $tagline; //String
    public $title; //String
    public $video; //boolean
    public $vote_average; //double
    public $vote_count; //int
    public $videos; //Videos
    public $recommendations; //Recommendations

    function __construct( $data = [] )
    {

      try {
        if ( isset( $data ) && isset( $data -> id ) ) {
          foreach ( $data as $key => $value ) {
            $this -> $key = $value;
          }
        }
      } catch ( Exception $err ) {
//        var_dump( $err );
      }
    }

    public function getSimple()
    {
      return [
        'id'            => $this -> id,
        'title'         => $this -> title,
        'vote_average'  => $this -> vote_average,
        'release_date'  => $this -> release_date,
        'genres'        => $this -> genres,
        'poster_path'   => $this -> poster_path,
        'backdrop_path' => $this -> backdrop_path,
      ];
    }
  }