<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
  $app -> add( function ( $request, $response, $next ) {
    $start = microtime( true );
    $response = $next( $request, $response );

    $total = round( microtime( true ) - $start, 3 ) * 1000;
    $this -> logger -> info( $request -> getUri() -> getPath() . ' ' . $request -> getUri() -> getQuery() . ' (' . $total . ')ms' );
    return $response;
  } );