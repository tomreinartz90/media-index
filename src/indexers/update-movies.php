<?php
  //run a maximum of 120 mins;
  set_time_limit( 60 * 120 );
  date_default_timezone_set( "UTC" );

  /** x
   */
  require_once __DIR__ . "/../helpers/RestApi.php";


  function getRecentlyChangesMovies( $page = 1, $movies = [] )
  {
    echo "getting page $page of movies that need to be updated\n";
    $rest = new RestApi();
    $date = new DateTime();
    $date -> add( DateInterval ::createFromDateString( 'yesterday' ) );
    $yesterday = $date -> format( "Y-m-d" );


    $data = $rest -> getJson( "https://api.themoviedb.org/3/movie/changes?api_key=ffbd2b663d53a66c2dd00bb517491490&start_date=$yesterday&page=$page" );
    $total = $data[ 'total_pages' ];

    //just get the ids from the list
    foreach ( $data[ 'results' ] as $movie ) {
      $id = $movie[ 'id' ];
      array_push( $movies, $id );
    }

    if ( $page != $total ) {
      return getRecentlyChangesMovies( $page + 1, $movies );
    };


    $totalMovies = sizeof( $movies );
    echo "a total of $totalMovies need to be updated\n";
    return $movies;
  }

  function updateMovies( $movies )
  {
    $rest = new RestApi();
    $updated = 0;
    $total = sizeof( $movies );
    echo "starting update of $total\n";
    foreach ( $movies as $id ) {
      $data = $rest -> getJson( "http://moviedb.tomreinartz.com/movies/$id?useCache=false" );
      $test = $data[ 'id' ];
      $updated = $updated + 1;
      echo "updated $test ( $updated / $total)\n";
    }
    echo "everyThing has been updated\n";
    return;
  }

  $movies = getRecentlyChangesMovies();
  updateMovies( $movies );
