<?php

  /**
   * /movies/{id} get details about a specific id
   * /movies?ids=[1,2,3,4,5,6] get details about multiple ids
   * ?useCache=false to force reload details about the movie.
   */
  $app -> get( '/series[/{id}]', function ( $request, $response, $args ) {
    require __DIR__ . '/../services/moviedb.php';

    $data = null;
    $movieDb = new Moviedb();
    $useCache = $request -> getQueryParam( 'useCache' ) !== "false";

    if ( isset( $args[ 'id' ] ) ) {
      $data = $movieDb -> getSerieDetails( $args[ 'id' ], $useCache );
    } else {
      $data = $movieDb -> getSerieDetailsByIds( json_decode( $request -> getQueryParam( 'ids', "[]" ) ) );
    }

    $newResp = $response -> withJson( $data ? $data : [ 'error' => "Could not get show details" ], $data ? 200 : 404 );
    return $newResp;
  } );
