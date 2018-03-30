<?php

  /**
   * Created by PhpStorm.
   * User: t.reinartz
   * Date: 29-11-2017
   * Time: 13:19
   */
  class RestApi
  {
    private function CallAPI( $method, $url, $data = false )
    {
      $curl = curl_init();


//      curl "http://torrentapi.org/pubapi_v2.php?app_id=my_local_movie_app^&get_token=get_token" -h
// "Pragma: no-cache" -h
// "Accept-Encoding: gzip, deflate" -h
// "Accept-Language: nl-NL,nl;q=0.9,en-US;q=0.8,en;q=0.7" -h
// "Upgrade-Insecure-Requests: 1" -h
// "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36" -h
// "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8" -h
// "Cache-Control: no-cache" -h
// "Cookie: __cfduid=d1f9d24deaf8e21e114b167a53ff5b6811516043892" -h
// "Connection: keep-alive" --compressed
      curl_setopt( $curl, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36',
        'Accept: text/html,application/xhtml,+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
        'Cache-Control: no-cache',
        'Connection: keep-alive',
        'Cookie: __cfduid=d1f9d24deaf8e21e114b167a53ff5b6811516043892',
      ] );
      switch ( $method ) {
        case "POST":
        case "PUT":
          if ( $data ) {
            curl_setopt( $curl, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json', 'Content-Length: ' . strlen( $data ) ] );
            curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
          }
          curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $method );
          curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
          break;
        default:
          break;
      }

      curl_setopt( $curl, CURLOPT_URL, $url );
      curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
      curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
      curl_setopt( $curl, CURLOPT_TIMEOUT, 10 );

      $result = curl_exec( $curl );
      $error = curl_error( $curl );

      if ( $error ) {
        throw new Error( $error );
      } else if ( $result == null ) {
        throw  new Error( json_encode(curl_getinfo($curl)) );
      }


      curl_close( $curl );
      return $result;
    }

    function get( $url )
    {
      return $this -> CallAPI( 'GET', $url );
    }

    function getJson( $url )
    {
      return json_decode( $this -> get( $url ), true );
    }

    function put( $url, $data )
    {
      return $this -> CallAPI( 'PUT', $url, $data );
    }

    function post( $url, $data )
    {
      return $this -> CallAPI( 'POST', $url, $data );
    }
  }