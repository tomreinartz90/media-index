<?php

  /**
   * Created by PhpStorm.
   * User: taren
   * Date: 12-1-2018
   * Time: 21:05
   */
  class Serie
  {
    public $backdrop_path; //String
    public $created_by; //array(Object)
    public $episode_run_time; //array(int)
    public $first_air_date; //String
    public $genres; //array(Genre)
    public $homepage; //String
    public $id; //int
    public $in_production; //boolean
    public $languages; //array(String)
    public $last_air_date; //String
    public $name; //String
    public $networks; //array(Network)
    public $number_of_episodes; //int
    public $number_of_seasons; //int
    public $origin_country; //array(String)
    public $original_language; //String
    public $original_name; //String
    public $overview; //String
    public $popularity; //double
    public $poster_path; //String
    public $production_companies; //array(Object)
    public $seasons; //array(Season)
    public $status; //String
    public $type; //String
    public $vote_average; //int
    public $vote_count; //int

    function __construct( $data = [] )
    {
      foreach ( $data as $key => $value ) {
        $this -> $key = $value;
      }
    }
  }