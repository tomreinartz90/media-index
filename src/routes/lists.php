<?php

  /**
   * /lists/{name} get details about a specific id
   * ?useCache=false to force reload details.
   */
  $app -> get( '/', function ( $request, $response, $args ) {
    require __DIR__ . '/../services/lists.php';
    $service = new ListService();
    $data = $service -> getPopularUndergroundMovies();
    $newResp = $response -> withJson( $data );
    return $newResp;

  } );

  $app -> get( '/lists/{group}[/{name}]', function ( $request, $response, $args ) {
    require __DIR__ . '/../services/lists.php';

    $data = null;
    $service = new ListService();
    $useCache = $request -> getQueryParam( 'useCache' ) !== "false";
    $newResp = $response -> withJson( [ 'error' => "Could not get list details" ], 404 );
    $data = null;
    switch ( $args[ 'group' ] . '-' . $args[ 'name' ] ) {
      case 'movies-popular-underground':
        $data = $service -> getPopularUndergroundMovies( $useCache );
        break;
      case 'movies-recent-underground':
        $data = $service -> getRecentUndergroundMovies( $useCache );
        break;
      case 'series-new':
        $data = $service -> getNewSeries( $useCache );
        break;
      case 'series-popular':
        $data = $service -> getPopularSeries( $useCache );
        break;
    }

    if ( $data != null ) {
      $newResp = $response -> withJson( $data );
    }

    return $newResp;
  } );
