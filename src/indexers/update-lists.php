<?php
  //run a maximum of 120 mins;
  set_time_limit( 60 * 120 );
  date_default_timezone_set( "UTC" );

  /** x
   */
  require_once __DIR__ . "/../helpers/RestApi.php";


  function updateLists()
  {
    $rest = new RestApi();
    $lists = [
      "movies/popular-underground",
      "movies/recent-underground",
      "series/new",
      "series/popular"
    ];

    echo "start update of lists\n";
    foreach ( $lists as $list ) {
      $rest -> getJson( "http://moviedb.tomreinartz.com/lists/$list?useCache=false" );
      echo "updated: $list\n";
    }
    echo "all lists have been updated";
    return;

  }

  updateLists();
